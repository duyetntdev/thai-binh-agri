<?php

namespace App\Repositories\Contracts;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Collection;

interface MenuItemRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @return Collection<int, MenuItem>
     */
    public function ordered(): Collection;

    public function createMenuItem(array $data): MenuItem;

    public function updateMenuItem(MenuItem $menuItem, array $data): MenuItem;

    public function deleteMenuItem(MenuItem $menuItem): bool;
}
