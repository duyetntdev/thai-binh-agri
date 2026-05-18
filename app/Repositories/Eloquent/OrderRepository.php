<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function paginateForUser(User $user, int $perPage = 10): LengthAwarePaginator
    {
        return $user->orders()
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate($perPage);
    }

    public function paginateAll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Order::with(['user', 'items', 'payment'])->latest();

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (! empty($filters['search'])) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('email', 'like', '%' . $filters['search'] . '%'));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function findRecent(int $limit = 10): Collection
    {
        return Order::with(['user', 'items'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function countByStatus(OrderStatus $status): int
    {
        return Order::where('status', $status->value)->count();
    }

    public function countToday(): int
    {
        return Order::whereDate('created_at', today())->count();
    }

    public function sumRevenue(): float
    {
        return (float) Order::where('payment_status', PaymentStatus::COMPLETED->value)
            ->sum('total_amount');
    }

    public function revenueByMonth(int $months = 6): array
    {
        return Order::where('payment_status', PaymentStatus::COMPLETED->value)
            ->where('created_at', '>=', now()->subMonths($months))
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total'),
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    public function updateStatus(Order $order, OrderStatus $status): Order
    {
        $order->update(['status' => $status]);

        return $order->fresh();
    }
}
