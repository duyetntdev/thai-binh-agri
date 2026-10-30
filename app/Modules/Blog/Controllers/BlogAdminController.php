<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Modules\Blog\Requests\StoreBlogRequest;
use App\Modules\Blog\Requests\UpdateBlogRequest;
use App\Modules\Blog\Services\BlogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class BlogAdminController extends Controller
{
    public function __construct(private readonly BlogService $blogService) {}

    /** @throws Throwable */
    public function index(): View
    {
        $blogs = $this->blogService->allForAdmin();

        return view('blog::admin.index', compact('blogs'));
    }

    /** @throws Throwable */
    public function create(): View
    {
        return view('blog::admin.create');
    }

    /** @throws Throwable */
    public function store(StoreBlogRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->blogService->uniqueSlugForTitle($data['title']);

        $blog = $this->blogService->create($data, $request->file('thumbnail'));

        return redirect()->route('admin.blogs.edit', $blog)
            ->with('success', 'Bài viết đã được tạo thành công.');
    }

    /** @throws Throwable */
    public function edit(Blog $blog): View
    {
        return view('blog::admin.edit', compact('blog'));
    }

    /** @throws Throwable */
    public function update(UpdateBlogRequest $request, Blog $blog): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $data['title'] === $blog->title
            ? $blog->slug
            : $this->blogService->uniqueSlugForTitle($data['title'], $blog);

        $this->blogService->update($blog, $data, $request->file('thumbnail'));

        return redirect()->route('admin.blogs.edit', $blog)
            ->with('success', 'Bài viết đã được cập nhật.');
    }

    /** @throws Throwable */
    public function destroy(Blog $blog): RedirectResponse
    {
        $this->blogService->delete($blog);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Bài viết đã được xóa.');
    }
}
