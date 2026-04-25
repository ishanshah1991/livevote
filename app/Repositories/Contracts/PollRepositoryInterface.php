<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract for poll-specific query operations.
 */
interface PollRepositoryInterface extends RepositoryInterface
{
    /**
     * Return all polls owned by the given admin, newest first.
     *
     * @param  int  $adminId
     * @return Collection<int, Poll>
     */
    public function findByAdminId(int $adminId): Collection;

    /**
     * Find a poll and eager-load its options ordered by display_order.
     *
     * @param  int  $id
     * @return Poll|null
     */
    public function findWithOptions(int $id): ?Poll;

    /**
     * Find a poll and eager-load its options with their cached vote counts.
     *
     * @param  int  $id
     * @return Poll|null
     */
    public function findWithResults(int $id): ?Poll;

    /**
     * Flip the is_active flag on the given poll and return the new value.
     *
     * @param  int  $id
     * @return bool  The new is_active value; false when the poll is not found.
     */
    public function toggleActive(int $id): bool;
}
