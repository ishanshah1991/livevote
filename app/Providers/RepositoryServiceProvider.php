<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\PollRepositoryInterface;
use App\Repositories\Eloquent\AdminRepository;
use App\Repositories\Eloquent\PollRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Binds repository interfaces to their concrete Eloquent implementations.
 */
final class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    private const BINDINGS = [
        AdminRepositoryInterface::class => AdminRepository::class,
        PollRepositoryInterface::class  => PollRepository::class,
    ];

    /**
     * Register repository bindings in the container.
     *
     * @return void
     */
    public function register(): void
    {
        foreach (self::BINDINGS as $contract => $implementation) {
            $this->app->bind($contract, $implementation);
        }
    }

    /**
     * @return void
     */
    public function boot(): void
    {
    }
}
