<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Modules\Admin\Requests\UpdateOrderStatusRequest;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderAdminController extends Controller
{
    public function __construct(private readonly OrderRepositoryInterface $orderRepository) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'payment_status', 'search']);
        $orders = $this->orderRepository->paginateAll($filters, 20);

        return view('admin::orders.index', compact('orders', 'filters'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'payment', 'user']);

        return view('admin::orders.show', compact('order'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $status = OrderStatus::from($request->input('status'));
        $this->orderRepository->updateStatus($order, $status);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }
}
