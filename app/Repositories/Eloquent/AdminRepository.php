<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;

/**
 * Eloquent-backed implementation of {@see AdminRepositoryInterface}.
 */
final class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{
    /**
     * @param  Admin  $model
     */
    public function __construct(Admin $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  string  $email
     * @return Admin|null
     */
    public function findByEmail(string $email): ?Admin
    {
        /** @var Admin|null $admin */
        $admin = $this->model->newQuery()->firstWhere('email', $email);

        return $admin;
    }
}
