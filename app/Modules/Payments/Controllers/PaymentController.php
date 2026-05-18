<?php

namespace App\Modules\Payments\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use App\Modules\Payments\Services\VnpayService;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly VnpayService              $vnpayService,
        private readonly PaymentRepositoryInterface $paymentRepository,
        private readonly OrderRepositoryInterface   $orderRepository,
    ) {}

    /**
     * Initiate payment for an order.
     */
    public function initiate(Order $order): RedirectResponse|View
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->isPaid()) {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Đơn hàng này đã được thanh toán.');
        }

        $payment = $this->paymentRepository->firstOrCreateForOrder($order, [
            'amount' => $order->total_amount,
            'method' => PaymentMethod::VNPAY->value,
            'status' => PaymentStatus::PENDING->value,
        ]);

        if ($payment->method === PaymentMethod::COD) {
            return view('payments::cod-confirm', compact('order', 'payment'));
        }

        return redirect()->away(
            $this->vnpayService->buildPaymentUrl($order, request()->ip())
        );
    }

    /**
     * Handle VNPay return callback.
     */
    public function callback(Request $request): RedirectResponse
    {
        if (! $this->vnpayService->verifyCallback($request)) {
            return redirect()->route('products.index')
                ->with('error', 'Phản hồi thanh toán không hợp lệ.');
        }

        $order   = $this->orderRepository->findByIdOrFail($this->vnpayService->extractOrderId($request));
        $payment = $this->paymentRepository->findByOrder($order);

        if (! $payment) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Không tìm thấy thông tin thanh toán.');
        }

        if ($this->vnpayService->isSuccessful($request)) {
            $this->paymentRepository->markCompleted($payment, $request->query('vnp_TransactionNo'));
            $this->orderRepository->update($order, ['payment_status' => PaymentStatus::COMPLETED]);

            return redirect()->route('payments.result', $order)
                ->with('success', 'Thanh toán thành công!');
        }

        $this->paymentRepository->markFailed($payment);

        return redirect()->route('payments.result', $order)
            ->with('error', 'Thanh toán thất bại. Vui lòng thử lại.');
    }

    /**
     * Show payment result page.
     */
    public function result(Order $order): View
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'payment']);

        return view('payments::result', compact('order'));
    }
}
