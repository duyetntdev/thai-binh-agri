<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get paginated active products with optional filters.
     *
     * @param array{category?: string, search?: string, sort?: string} $filters
     */
    public function paginateActive(array $filters = [], int $perPage = 16): LengthAwarePaginator;

    /**
     * Find an active product by its slug.
     */
    public function findActiveBySlug(string $slug): Product;

    /**
     * Get related products from the same category, excluding the given product.
     */
    public function findRelated(Product $product, int $limit = 4): Collection;

    /**
     * Find a product by ID with a pessimistic lock (for stock operations).
     */
    public function findForUpdate(int $id): Product;

    /**
     * Decrement stock by the given quantity.
     */
    public function decrementStock(Product $product, int $quantity): void;

    /**
     * Increment stock by the given quantity.
     */
    public function incrementStock(Product $product, int $quantity): void;

    /**
     * Get products with stock at or below the threshold.
     */
    public function findLowStock(int $threshold = 10): Collection;

    /**
     * Get best-selling products ordered by sold_count desc.
     */
    public function findBestSellers(int $limit = 8): Collection;

    /**
     * Get most-viewed products ordered by view_count desc.
     */
    public function findMostViewed(int $limit = 8): Collection;

    /**
     * Get products recently purchased by a user (from their delivered orders).
     *
     * @return Collection<int, Product>
     */
    public function findRecentlyPurchasedByUser(int $userId, int $limit = 8): Collection;

    /**
     * Increment the view_count of a product by 1.
     */
    public function incrementViewCount(Product $product): void;
}
