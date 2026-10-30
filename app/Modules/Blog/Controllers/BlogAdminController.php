<?php

namespace App\Modules\Blog\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Modules\Blog\Services\BlogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BlogAdminController extends Controller
{
    public function __construct(private readonly BlogService $blogService) {}

    public function index(): View
    {
        $blogs = $this->blogService->allForAdmin();

        return view('blog::admin.index', compact('blogs'));
    }

    public function create(): View
    {
        return view('blog::admin.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['is_active' => $request->boolean('is_active')]);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_active' => ['required', 'boolean'],
        ]);
        $data['slug'] = $this->blogService->uniqueSlugForTitle($data['title']);
        $data['thumbnail'] = null;

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail')->store('blogs', 'public');

            if ($thumbnail === false) {
                return back()->withInput()->withErrors([
                    'thumbnail' => 'Không thể lưu ảnh. Vui lòng thử lại.',
                ]);
            }

            $data['thumbnail'] = $thumbnail;
        }

        $blog = $this->blogService->create($data);

        return redirect()->route('admin.blogs.edit', $blog)
            ->with('success', 'Bài viết đã được tạo thành công.');
    }

    public function edit(Blog $blog): View
    {
        return view('blog::admin.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $previousThumbnail = $blog->thumbnail;
        $request->merge(['is_active' => $request->boolean('is_active')]);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_active' => ['required', 'boolean'],
        ]);
        $data['slug'] = $data['title'] === $blog->title
            ? $blog->slug
            : $this->blogService->uniqueSlugForTitle($data['title'], $blog);

        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail')->store('blogs', 'public');

            if ($thumbnail === false) {
                return back()->withInput()->withErrors([
                    'thumbnail' => 'Không thể lưu ảnh. Vui lòng thử lại.',
                ]);
            }

            $data['thumbnail'] = $thumbnail;
        } else {
            $data['thumbnail'] = $blog->thumbnail;
        }

        $this->blogService->update($blog, $data);

        if ($request->hasFile('thumbnail') && $previousThumbnail && str_starts_with($previousThumbnail, 'blogs/')) {
            Storage::disk('public')->delete($previousThumbnail);
        }

        return redirect()->route('admin.blogs.edit', $blog)
            ->with('success', 'Bài viết đã được cập nhật.');
    }

    public function destroy(Blog $blog): RedirectResponse
    {
        $this->blogService->delete($blog);

        if ($blog->thumbnail && str_starts_with($blog->thumbnail, 'blogs/')) {
            Storage::disk('public')->delete($blog->thumbnail);
        }

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Bài viết đã được xóa.');
    }
}
