<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Base contract for all repositories.
 * Provides standard CRUD operations.
 */
interface BaseRepositoryInterface
{
    public function findById(int $id): ?Model;

    public function findByIdOrFail(int $id): Model;

    public function all(): Collection;

    public function create(array $data): Model;

    public function update(Model $model, array $data): Model;

    public function delete(Model $model): bool;
}
