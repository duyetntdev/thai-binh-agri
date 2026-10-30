<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Modules\Admin\Requests\StoreMenuItemRequest;
use App\Modules\Admin\Requests\UpdateMenuItemRequest;
use App\Modules\Admin\Services\MenuItemService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MenuItemAdminController extends Controller
{
    public function __construct(private readonly MenuItemService $menuItemService) {}

    public function index(): View
    {
        $menuItems = $this->menuItemService->all();

        return view('admin::menu-items.index', compact('menuItems'));
    }

    public function create(): View
    {
        return view('admin::menu-items.create');
    }

    public function store(StoreMenuItemRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['is_active'] = $request->has('is_active');

        $this->menuItemService->create($data);

        return redirect()->route('admin.menu-items.index')
            ->with('success', 'Menu đã được tạo thành công.');
    }

    public function edit(MenuItem $menuItem): View
    {
        return view('admin::menu-items.edit', compact('menuItem'));
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): RedirectResponse
    {
        $data = $request->validated();

        $data['is_active'] = $request->has('is_active');

        $this->menuItemService->update($menuItem, $data);

        return redirect()->route('admin.menu-items.edit', $menuItem)
            ->with('success', 'Menu đã được cập nhật.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $this->menuItemService->delete($menuItem);

        return redirect()->route('admin.menu-items.index')
            ->with('success', 'Menu đã được xóa.');
    }
}
