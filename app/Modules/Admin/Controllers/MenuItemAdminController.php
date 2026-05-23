<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuItemAdminController extends Controller
{
    public function index(): View
    {
        $menuItems = MenuItem::orderBy('sort_order')->get();

        return view('admin::menu-items.index', compact('menuItems'));
    }

    public function create(): View
    {
        return view('admin::menu-items.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'url' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');

        MenuItem::create($data);

        return redirect()->route('admin.menu-items.index')
            ->with('success', 'Menu đã được tạo thành công.');
    }

    public function edit(MenuItem $menuItem): View
    {
        return view('admin::menu-items.edit', compact('menuItem'));
    }

    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:120'],
            'url' => ['required', 'string', 'max:255'],
            'sort_order' => ['required', 'integer'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_active'] = $request->has('is_active');

        $menuItem->update($data);

        return redirect()->route('admin.menu-items.edit', $menuItem)
            ->with('success', 'Menu đã được cập nhật.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        $menuItem->delete();

        return redirect()->route('admin.menu-items.index')
            ->with('success', 'Menu đã được xóa.');
    }
}
