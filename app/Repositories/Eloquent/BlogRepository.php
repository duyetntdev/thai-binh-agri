<?php

namespace App\Repositories\Eloquent;

use App\Models\Blog;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    public function __construct(Blog $model)
    {
        parent::__construct($model);
    }

    public function paginateActive(int $perPage = 9): LengthAwarePaginator
    {
        return Blog::active()
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function paginateAll(int $perPage = 20): LengthAwarePaginator
    {
        return Blog::latest()->paginate($perPage)->withQueryString();
    }

    public function findActiveBySlug(string $slug): Blog
    {
        return Blog::active()->where('slug', $slug)->firstOrFail();
    }

    public function uniqueSlugForTitle(string $title, ?Blog $except = null): string
    {
        $baseSlug = Str::slug($title) ?: 'bai-viet';
        $slug = $baseSlug;
        $suffix = 1;

        while (Blog::where('slug', $slug)
            ->when($except, fn ($query) => $query->where('id', '!=', $except->getKey()))
            ->exists()) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        return $slug;
    }

    public function createBlog(array $data): Blog
    {
        return Blog::create($data);
    }

    public function updateBlog(Blog $blog, array $data): Blog
    {
        $blog->update($data);

        return $blog->refresh();
    }

    public function deleteBlog(Blog $blog): bool
    {
        return (bool) $blog->delete();
    }
}
