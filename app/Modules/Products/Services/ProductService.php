<?php

namespace App\Modules\Products\Services;

use App\Models\Product;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface  $productRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    /**
     * Get paginated active products with optional filters.
     *
     * @param array{category?: string, search?: string, sort?: string} $filters
     */
    public function list(array $filters = [], int $perPage = 16): LengthAwarePaginator
    {
        return $this->productRepository->paginateActive($filters, $perPage);
    }

    /**
     * Get a single active product by slug.
     */
    public function findBySlug(string $slug): Product
    {
        return $this->productRepository->findActiveBySlug($slug);
    }

    /**
     * Get related products from the same category.
     */
    public function related(Product $product, int $limit = 4): Collection
    {
        return $this->productRepository->findRelated($product, $limit);
    }

    /**
     * Get all categories with active product count.
     */
    public function categories(): Collection
    {
        return $this->categoryRepository->allWithActiveProductCount();
    }

    /**
     * Increment view_count when a product detail page is visited.
     */
    public function trackView(Product $product): void
    {
        $this->productRepository->incrementViewCount($product);
    }
}
