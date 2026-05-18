<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get paginated orders for a specific user, newest first.
     */
    public function paginateForUser(User $user, int $perPage = 10): LengthAwarePaginator;

    /**
     * Get paginated orders for admin with optional filters.
     *
     * @param array{status?: string, payment_status?: string, search?: string} $filters
     */
    public function paginateAll(array $filters = [], int $perPage = 20): LengthAwarePaginator;

    /**
     * Get the most recent orders with user and items loaded.
     */
    public function findRecent(int $limit = 10): Collection;

    /**
     * Count orders by status.
     */
    public function countByStatus(OrderStatus $status): int;

    /**
     * Count orders created today.
     */
    public function countToday(): int;

    /**
     * Sum total_amount for all paid orders.
     */
    public function sumRevenue(): float;

    /**
     * Get monthly revenue for the last N months.
     *
     * @return array<int, array{year: int, month: int, total: float}>
     */
    public function revenueByMonth(int $months = 6): array;

    /**
     * Update the status of an order.
     */
    public function updateStatus(Order $order, OrderStatus $status): Order;
}
