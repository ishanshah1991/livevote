<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Generic CRUD contract that every entity-specific repository must satisfy.
 */
interface RepositoryInterface
{
    /**
     * Retrieve every record for the entity.
     *
     * @return Collection<int, Model>
     */
    public function all(): Collection;

    /**
     * Find a single record by its primary key.
     *
     * @param  int|string  $id
     * @return Model|null
     */
    public function find(int|string $id): ?Model;

    /**
     * Persist a new record.
     *
     * @param  array<string, mixed>  $attributes
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * Update the record matching the given primary key.
     *
     * @param  int|string  $id
     * @param  array<string, mixed>  $attributes
     * @return Model
     */
    public function update(int|string $id, array $attributes): Model;

    /**
     * Delete the record matching the given primary key.
     *
     * @param  int|string  $id
     * @return bool
     */
    public function delete(int|string $id): bool;
}
