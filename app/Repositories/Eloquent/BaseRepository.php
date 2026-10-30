<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Abstract base repository providing default CRUD implementations.
 * All Eloquent repositories extend this class.
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected readonly Model $model) {}

    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findByIdOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function create(array $data): Model
    {
        return DB::transaction(fn () => $this->model->newQuery()->create($data));
    }

    public function update(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data): Model {
            $model->update($data);

            return $model->fresh();
        });
    }

    public function delete(Model $model): bool
    {
        return DB::transaction(fn () => (bool) $model->delete());
    }
}
