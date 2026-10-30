<?php

namespace App\Modules\Blog\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class BlogThumbnailStorage
{
    public function store(UploadedFile $thumbnail): string
    {
        $path = $thumbnail->store('blogs', 'public');

        if ($path === false) {
            throw new RuntimeException('Không thể lưu ảnh. Vui lòng thử lại.');
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if (! $path || ! str_starts_with($path, 'blogs/')) {
            return;
        }

        $disk = Storage::disk('public');

        if ($disk->exists($path) && ! $disk->delete($path)) {
            throw new RuntimeException('Không thể xóa ảnh bài viết.');
        }
    }
}
