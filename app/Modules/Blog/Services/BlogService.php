<?php

namespace App\Modules\Blog\Services;

use App\Models\Blog;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

class BlogService
{
    public function __construct(
        private readonly BlogRepositoryInterface $blogRepository,
        private readonly BlogThumbnailStorage $thumbnailStorage,
    ) {}

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

    /** @throws Throwable */
    public function create(array $data, ?UploadedFile $thumbnail = null): Blog
    {
        $path = $thumbnail ? $this->thumbnailStorage->store($thumbnail) : null;
        $data['thumbnail'] = $path;

        try {
            return DB::transaction(fn () => $this->blogRepository->createBlog($data));
        } catch (Throwable $exception) {
            $this->thumbnailStorage->delete($path);

            throw $exception;
        }
    }

    /** @throws Throwable */
    public function update(Blog $blog, array $data, ?UploadedFile $thumbnail = null): Blog
    {
        $previousThumbnail = $blog->thumbnail;
        $newThumbnail = $thumbnail ? $this->thumbnailStorage->store($thumbnail) : null;
        $data['thumbnail'] = $newThumbnail ?? $previousThumbnail;

        try {
            $updatedBlog = DB::transaction(fn () => $this->blogRepository->updateBlog($blog, $data));
            if ($newThumbnail) {
                DB::afterCommit(fn () => $this->thumbnailStorage->delete($previousThumbnail));
            }

            return $updatedBlog;
        } catch (Throwable $exception) {
            $this->thumbnailStorage->delete($newThumbnail);

            throw $exception;
        }
    }

    /** @throws Throwable */
    public function delete(Blog $blog): bool
    {
        $thumbnail = $blog->thumbnail;
        $deleted = DB::transaction(fn () => $this->blogRepository->deleteBlog($blog));

        if ($deleted) {
            DB::afterCommit(fn () => $this->thumbnailStorage->delete($thumbnail));
        }

        return $deleted;
    }
}
