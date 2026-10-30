<?php

namespace App\Repositories\Eloquent;

use App\Models\MenuItem;
use App\Repositories\Contracts\MenuItemRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class MenuItemRepository extends BaseRepository implements MenuItemRepositoryInterface
{
    public function __construct(MenuItem $model)
    {
        parent::__construct($model);
    }

    public function ordered(): Collection
    {
        return MenuItem::query()->orderBy('sort_order')->get();
    }

    public function createMenuItem(array $data): MenuItem
    {
        return MenuItem::create($data);
    }

    public function updateMenuItem(MenuItem $menuItem, array $data): MenuItem
    {
        $menuItem->update($data);

        return $menuItem->refresh();
    }

    public function deleteMenuItem(MenuItem $menuItem): bool
    {
        return (bool) $menuItem->delete();
    }
}
