<?php

namespace App\Modules\Blog\Services;

use App\Models\Blog;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BlogService
{
    public function __construct(private readonly BlogRepositoryInterface $blogRepository) {}

    public function published(int $perPage = 9): LengthAwarePaginator
    {
        return $this->blogRepository->paginateActive($perPage);
    }

    public function findPublishedBySlug(string $slug): Blog
    {
        return $this->blogRepository->findActiveBySlug($slug);
    }

    public function uniqueSlugForTitle(string $title, ?Blog $except = null): string
    {
        return $this->blogRepository->uniqueSlugForTitle($title, $except);
    }

    public function allForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return $this->blogRepository->paginateAll($perPage);
    }

    public function create(array $data): Blog
    {
        return $this->blogRepository->createBlog($data);
    }

    public function update(Blog $blog, array $data): Blog
    {
        return $this->blogRepository->updateBlog($blog, $data);
    }

    public function delete(Blog $blog): bool
    {
        return $this->blogRepository->deleteBlog($blog);
    }
}
