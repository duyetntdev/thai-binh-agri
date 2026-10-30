<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Modules\Admin\Requests\StorePageRequest;
use App\Modules\Admin\Requests\UpdatePageRequest;
use App\Modules\Admin\Services\PageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PageAdminController extends Controller
{
    public function __construct(private readonly PageService $pageService) {}

    public function index(): View
    {
        $pages = $this->pageService->all();

        return view('admin::pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin::pages.create');
    }

    public function store(StorePageRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['is_active'] = $request->has('is_active');

        $this->pageService->create($data);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Nội dung trang đã được tạo.');
    }

    public function edit(Page $page): View
    {
        return view('admin::pages.edit', compact('page'));
    }

    public function update(UpdatePageRequest $request, Page $page): RedirectResponse
    {
        $data = $request->validated();

        $data['is_active'] = $request->has('is_active');

        $this->pageService->update($page, $data);

        return redirect()->route('admin.pages.edit', $page)
            ->with('success', 'Nội dung trang đã được cập nhật.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->pageService->delete($page);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Nội dung trang đã được xóa.');
    }
}
