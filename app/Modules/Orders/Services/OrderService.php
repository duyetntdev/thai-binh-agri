<?php

namespace App\Modules\Orders\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Get paginated orders for a customer.
     */
    public function listForUser(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return $this->orderRepository->paginateForUser($user, $perPage);
    }

    /**
     * Create a new order with stock validation inside a transaction.
     *
     * @param  array<int, array{product_id: int, quantity: int}>  $items
     *
     * @throws InsufficientStockException
     * @throws \Throwable
     */
    public function create(
        User $user,
        array $items,
        string $paymentMethod,
        int $provinceId,
        int $wardId,
        string $shippingAddress,
        string $shippingPhone,
        ?string $notes = null,
    ): Order {
        if ($items === []) {
            throw new \InvalidArgumentException('Đơn hàng phải có ít nhất một sản phẩm.');
        }

        foreach ($items as $item) {
            if (! isset($item['product_id'], $item['quantity']) || (int) $item['quantity'] < 1) {
                throw new \InvalidArgumentException('Số lượng sản phẩm phải lớn hơn 0.');
            }
        }

        return DB::transaction(function () use ($user, $items, $paymentMethod, $notes, $provinceId, $wardId, $shippingAddress, $shippingPhone) {
            $totalAmount = 0;
            $orderItems = [];

            foreach ($items as $item) {
                // Lock the row to prevent race conditions on stock
                $product = $this->productRepository->findForUpdate($item['product_id']);
                $totalAmount += $product->price * $item['quantity'];

                // Throws InsufficientStockException if not enough stock
                $this->productRepository->decrementStock($product, $item['quantity']);

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];
            }

            $order = $this->orderRepository->create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => OrderStatus::PENDING,
                'payment_status' => PaymentStatus::PENDING,
                'notes' => $notes,
                'shipping_address' => $shippingAddress,
                'shipping_phone' => $shippingPhone,
                'province_id' => $provinceId,
                'ward_id' => $wardId,
            ]);

            $order->payment()->create([
                'amount' => $totalAmount,
                'method' => PaymentMethod::from($paymentMethod),
                'status' => PaymentStatus::PENDING,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
                // Tăng sold_count cho sản phẩm
                Product::where('id', $item['product_id'])
                    ->increment('sold_count', $item['quantity']);
            }

            return $order->load(['items.product', 'payment']);
        });
    }

    /**
     * Cancel an order and restore stock.
     *
     * @throws \RuntimeException
     */
    public function cancel(Order $order, User $user): Order
    {
        if ($order->user_id !== $user->id) {
            abort(403);
        }

        return $this->transitionStatus($order, OrderStatus::CANCELLED);
    }

    public function updateStatusByAdmin(Order $order, OrderStatus $status): Order
    {
        return $this->transitionStatus($order, $status);
    }

    public function markPaidByAdmin(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->status !== OrderStatus::PROCESSING || $lockedOrder->isPaid()) {
                throw ValidationException::withMessages([
                    'payment' => 'Chỉ có thể xác nhận thanh toán cho đơn đã xác nhận và chưa thanh toán.',
                ]);
            }

            $payment = $lockedOrder->payment()->firstOrNew([], [
                'amount' => $lockedOrder->total_amount,
                'method' => PaymentMethod::COD,
            ]);
            $payment->status = PaymentStatus::COMPLETED;
            $payment->paid_at = now();
            $payment->save();

            $lockedOrder->update(['payment_status' => PaymentStatus::COMPLETED]);

            return $lockedOrder->fresh();
        });
    }

    private function transitionStatus(Order $order, OrderStatus $status): Order
    {
        return DB::transaction(function () use ($order, $status) {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (! $lockedOrder->status->canTransitionTo($status, $lockedOrder->isPaid())) {
                $message = in_array($status, [OrderStatus::SHIPPED, OrderStatus::DELIVERED])
                    && ! $lockedOrder->isPaid()
                    ? 'Chỉ có thể giao hàng sau khi đơn hàng đã được thanh toán.'
                    : 'Không thể chuyển đơn hàng sang trạng thái đã chọn.';

                throw ValidationException::withMessages(['status' => $message]);
            }

            if ($status === OrderStatus::CANCELLED) {
                $lockedOrder->load('items.product');

                foreach ($lockedOrder->items as $item) {
                    $this->productRepository->incrementStock($item->product, $item->quantity);
                }
            }

            $this->orderRepository->updateStatus($lockedOrder, $status);

            return $lockedOrder->fresh();
        });
    }
}
