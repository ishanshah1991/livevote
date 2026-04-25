<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Admin;

/**
 * Admin-specific repository contract.
 */
interface AdminRepositoryInterface extends RepositoryInterface
{
    /**
     * Locate an admin by email address.
     *
     * @param  string  $email
     * @return Admin|null
     */
    public function findByEmail(string $email): ?Admin;
}
