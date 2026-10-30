<?php

namespace App\Repositories\Eloquent;

use App\Models\Page;
use App\Repositories\Contracts\PageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    public function __construct(Page $model)
    {
        parent::__construct($model);
    }

    public function ordered(): Collection
    {
        return Page::query()->orderBy('slug')->get();
    }

    public function createPage(array $data): Page
    {
        return Page::create($data);
    }

    public function updatePage(Page $page, array $data): Page
    {
        $page->update($data);

        return $page->refresh();
    }

    public function deletePage(Page $page): bool
    {
        return (bool) $page->delete();
    }
}
