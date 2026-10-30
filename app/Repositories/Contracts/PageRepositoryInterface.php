<?php

namespace App\Repositories\Contracts;

use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;

interface PageRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @return Collection<int, Page>
     */
    public function ordered(): Collection;

    public function createPage(array $data): Page;

    public function updatePage(Page $page, array $data): Page;

    public function deletePage(Page $page): bool;
}
