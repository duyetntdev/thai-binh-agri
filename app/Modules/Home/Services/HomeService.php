<?php

namespace App\Modules\Home\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class HomeService
{
    public function __construct(
        private readonly ProductRepositoryInterface  $productRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    /**
     * Best-selling products — ordered by sold_count desc.
     */
    public function bestSellers(int $limit = 8): Collection
    {
        return $this->productRepository->findBestSellers($limit);
    }

    /**
     * Most-viewed / "favourite" products — ordered by view_count desc.
     */
    public function mostViewed(int $limit = 8): Collection
    {
        return $this->productRepository->findMostViewed($limit);
    }

    /**
     * Products recently purchased by the authenticated user.
     * Returns empty collection for guests.
     */
    public function recentlyPurchased(int $limit = 8): Collection
    {
        if (! auth()->check()) {
            return new Collection();
        }

        return $this->productRepository->findRecentlyPurchasedByUser(auth()->id(), $limit);
    }

    /**
     * All categories with active product count (for navigation/filter).
     */
    public function categories(): Collection
    {
        return $this->categoryRepository->allWithActiveProductCount();
    }
}
