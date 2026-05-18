<?php

namespace App\Modules\Admin\Services;

use App\Models\OrderStatus;
use App\Models\UserRole;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

class DashboardService
{
    public function __construct(
        private readonly OrderRepositoryInterface   $orderRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly UserRepositoryInterface    $userRepository,
    ) {}

    /**
     * Get all stats needed for the admin dashboard.
     */
    public function stats(): array
    {
        return [
            'total_revenue'    => $this->orderRepository->sumRevenue(),
            'orders_today'     => $this->orderRepository->countToday(),
            'pending_orders'   => $this->orderRepository->countByStatus(OrderStatus::PENDING),
            'total_products'   => count($this->productRepository->all()),
            'low_stock'        => count($this->productRepository->findLowStock()),
            'total_customers'  => $this->userRepository->countByRole(UserRole::CUSTOMER),
            'recent_orders'    => $this->orderRepository->findRecent(10),
            'revenue_by_month' => $this->orderRepository->revenueByMonth(6),
        ];
    }
}
