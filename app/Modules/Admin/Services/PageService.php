<?php

namespace App\Modules\Admin\Services;

use App\Models\Page;
use App\Repositories\Contracts\PageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PageService
{
    public function __construct(private readonly PageRepositoryInterface $pageRepository) {}

    /**
     * @return Collection<int, Page>
     */
    public function all(): Collection
    {
        return $this->pageRepository->ordered();
    }

    public function create(array $data): Page
    {
        return DB::transaction(fn () => $this->pageRepository->createPage($data));
    }

    public function update(Page $page, array $data): Page
    {
        return DB::transaction(fn () => $this->pageRepository->updatePage($page, $data));
    }

    public function delete(Page $page): bool
    {
        return DB::transaction(fn () => $this->pageRepository->deletePage($page));
    }
}
