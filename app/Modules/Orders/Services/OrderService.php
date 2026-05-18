<?php

namespace App\Modules\Orders\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface   $orderRepository,
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
     * @param array<int, array{product_id: int, quantity: int}> $items
     *
     * @throws InsufficientStockException
     * @throws \Throwable
     */
    public function create(User $user, array $items, string $paymentMethod, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($user, $items, $notes) {
            $totalAmount = 0;
            $orderItems  = [];

            foreach ($items as $item) {
                // Lock the row to prevent race conditions on stock
                $product      = $this->productRepository->findForUpdate($item['product_id']);
                $totalAmount += $product->price * $item['quantity'];

                // Throws InsufficientStockException if not enough stock
                $this->productRepository->decrementStock($product, $item['quantity']);

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                ];
            }

            $order = $this->orderRepository->create([
                'user_id'        => $user->id,
                'total_amount'   => $totalAmount,
                'status'         => OrderStatus::PENDING,
                'payment_status' => PaymentStatus::PENDING,
                'notes'          => $notes,
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
                // Tăng sold_count cho sản phẩm
                \App\Models\Product::where('id', $item['product_id'])
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

        if (! $order->canBeCancelled()) {
            throw new \RuntimeException('Đơn hàng không thể hủy ở trạng thái hiện tại.');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $this->productRepository->incrementStock($item->product, $item->quantity);
            }

            $this->orderRepository->updateStatus($order, OrderStatus::CANCELLED);
        });

        return $order->fresh();
    }
}
