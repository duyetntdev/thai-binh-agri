<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageAdminController extends Controller
{
    public function index(): View
    {
        $pages = Page::orderBy('slug')->get();

        return view('admin::pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin::pages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'alpha_dash', 'unique:pages,slug'],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');

        Page::create($data);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Nội dung trang đã được tạo.');
    }

    public function edit(Page $page): View
    {
        return view('admin::pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:150', 'alpha_dash', 'unique:pages,slug,' . $page->id],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');

        $page->update($data);

        return redirect()->route('admin.pages.edit', $page)
            ->with('success', 'Nội dung trang đã được cập nhật.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Nội dung trang đã được xóa.');
    }
}
