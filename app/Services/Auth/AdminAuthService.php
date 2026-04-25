<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Exceptions\DuplicateEmailException;
use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Encapsulates the business rules for admin authentication.
 *
 * Controllers stay thin by deferring to this service, which in turn uses
 * the AdminRepository for persistence.
 */
final class AdminAuthService
{
    /**
     * @param  AdminRepositoryInterface  $admins
     */
    public function __construct(private readonly AdminRepositoryInterface $admins)
    {
    }

    /**
     * Register a new admin account.
     *
     * @param  array<string, mixed>  $data  Validated payload — expects name, email, password.
     * @return Admin
     *
     * @throws DuplicateEmailException when an admin already exists with the given email.
     */
    public function register(array $data): Admin
    {
        /** @var string $email */
        $email = $data['email'];

        if ($this->admins->findByEmail($email) !== null) {
            throw new DuplicateEmailException($email);
        }

        /** @var string $password */
        $password = $data['password'];

        /** @var Admin $admin */
        $admin = $this->admins->create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return $admin;
    }

    /**
     * Attempt to authenticate an admin against the "admin" guard.
     *
     * @param  array<string, mixed>  $credentials  Expects email + password.
     * @return bool
     */
    public function attempt(array $credentials): bool
    {
        return Auth::guard('admin')->attempt($credentials);
    }
}
