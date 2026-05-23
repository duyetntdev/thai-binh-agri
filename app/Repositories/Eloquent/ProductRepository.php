<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function paginateActive(array $filters = [], int $perPage = 16): LengthAwarePaginator
    {
        $query = Product::with('category')->active()->inStock();

        if (! empty($filters['category'])) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $filters['category']));
        }

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        match ($filters['sort'] ?? null) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default      => $query->latest(),
        };

        return $query->paginate($perPage)->withQueryString();
    }

    public function paginateAll(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Product::with('category');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['category'])) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $filters['category']));
        }

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        $query->latest();

        return $query->paginate($perPage)->withQueryString();
    }

    public function findActiveBySlug(string $slug): Product
    {
        return Product::with('category')
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
    }

    public function findRelated(Product $product, int $limit = 4): Collection
    {
        return Product::with('category')
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function findForUpdate(int $id): Product
    {
        return Product::lockForUpdate()->findOrFail($id);
    }

    public function decrementStock(Product $product, int $quantity): void
    {
        if ($product->stock < $quantity) {
            throw new InsufficientStockException($product->name, $quantity, $product->stock);
        }

        $product->decrement('stock', $quantity);
    }

    public function incrementStock(Product $product, int $quantity): void
    {
        $product->increment('stock', $quantity);
    }

    public function findLowStock(int $threshold = 10): Collection
    {
        return Product::where('stock', '<=', $threshold)
            ->where('status', 'active')
            ->orderBy('stock')
            ->get();
    }

    public function findBestSellers(int $limit = 8): Collection
    {
        return Product::with('category')
            ->active()
            ->inStock()
            ->bestSeller()
            ->limit($limit)
            ->get();
    }

    public function findMostViewed(int $limit = 8): Collection
    {
        return Product::with('category')
            ->active()
            ->inStock()
            ->mostViewed()
            ->limit($limit)
            ->get();
    }

    public function findRecentlyPurchasedByUser(int $userId, int $limit = 8): Collection
    {
        return Product::with('category')
            ->active()
            ->whereHas('orderItems.order', function ($q) use ($userId) {
                $q->where('user_id', $userId)
                  ->where('status', \App\Models\OrderStatus::DELIVERED->value);
            })
            ->orderByDesc(
                \App\Models\OrderItem::select('order_items.created_at')
                    ->whereColumn('product_id', 'products.id')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.user_id', $userId)
                    ->latest('order_items.created_at')
                    ->limit(1)
            )
            ->limit($limit)
            ->get();
    }

    public function incrementViewCount(Product $product): void
    {
        $product->increment('view_count');
    }
}
