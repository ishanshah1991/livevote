<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Shared Eloquent implementation that concrete repositories extend.
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * Underlying Eloquent model used for query construction.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection<int, Model>
     */
    public function all(): Collection
    {
        return $this->model->newQuery()->get();
    }

    /**
     * @param  int|string  $id
     * @return Model|null
     */
    public function find(int|string $id): ?Model
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->model->newQuery()->create($attributes);
    }

    /**
     * @param  int|string  $id
     * @param  array<string, mixed>  $attributes
     * @return Model
     *
     * @throws RuntimeException when the record does not exist.
     */
    public function update(int|string $id, array $attributes): Model
    {
        $record = $this->find($id);

        if ($record === null) {
            throw new RuntimeException(sprintf('Record %s not found in %s.', (string) $id, $this->model::class));
        }

        $record->fill($attributes)->save();

        return $record;
    }

    /**
     * @param  int|string  $id
     * @return bool
     */
    public function delete(int|string $id): bool
    {
        $record = $this->find($id);

        if ($record === null) {
            return false;
        }

        return (bool) $record->delete();
    }
}
