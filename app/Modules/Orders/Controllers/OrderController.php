<?php

namespace App\Modules\Orders\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Modules\Orders\Requests\StoreOrderRequest;
use App\Modules\Orders\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(): View
    {
        $orders = $this->orderService->listForUser(auth()->user());

        return view('orders::index', compact('orders'));
    }

    public function show(Order $order): View
    {
        // Ensure the order belongs to the authenticated user (or admin)
        if ($order->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403);
        }

        $order->load(['items.product', 'payment', 'user']);

        return view('orders::show', compact('order'));
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $order = $this->orderService->create(
            user: auth()->user(),
            items: $request->input('items'),
            paymentMethod: $request->input('payment_method'),
            notes: $request->input('notes'),
        );

        return redirect()->route('orders.show', $order)
            ->with('success', 'Đặt hàng thành công! Mã đơn hàng: #' . $order->id);
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->orderService->cancel($order, auth()->user());

        return redirect()->route('orders.show', $order)
            ->with('success', 'Đơn hàng đã được hủy.');
    }
}
