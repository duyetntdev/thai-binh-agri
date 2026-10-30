<?php

namespace App\Repositories\Contracts;

use App\Models\Blog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BlogRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateActive(int $perPage = 9): LengthAwarePaginator;

    public function paginateAll(int $perPage = 20): LengthAwarePaginator;

    public function findActiveBySlug(string $slug): Blog;

    public function uniqueSlugForTitle(string $title, ?Blog $except = null): string;

    public function createBlog(array $data): Blog;

    public function updateBlog(Blog $blog, array $data): Blog;

    public function deleteBlog(Blog $blog): bool;
}
