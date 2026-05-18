<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all categories with the count of their active products.
     */
    public function allWithActiveProductCount(): Collection;

    /**
     * Find a category by its slug.
     */
    public function findBySlug(string $slug): ?Category;
}
