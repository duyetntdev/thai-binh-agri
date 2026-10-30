<?php

namespace App\Modules\Admin\Services;

use App\Models\MenuItem;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MenuItemService
{
    public function __construct(private readonly MenuItemRepositoryInterface $menuItemRepository) {}

    /**
     * @return Collection<int, MenuItem>
     */
    public function all(): Collection
    {
        return $this->menuItemRepository->ordered();
    }

    public function create(array $data): MenuItem
    {
        return DB::transaction(fn () => $this->menuItemRepository->createMenuItem($data));
    }

    public function update(MenuItem $menuItem, array $data): MenuItem
    {
        return DB::transaction(fn () => $this->menuItemRepository->updateMenuItem($menuItem, $data));
    }

    public function delete(MenuItem $menuItem): bool
    {
        return DB::transaction(fn () => $this->menuItemRepository->deleteMenuItem($menuItem));
    }
}
