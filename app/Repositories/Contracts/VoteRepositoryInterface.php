<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Vote;
use Illuminate\Support\Collection;

interface VoteRepositoryInterface
{
    /**
     * Check whether an authenticated user has already voted in this poll.
     *
     * @param  int  $pollId
     * @param  int  $userId
     * @return bool
     */
    public function hasUserVoted(int $pollId, int $userId): bool;

    /**
     * Check whether an IP address has already voted in this poll.
     *
     * @param  int     $pollId
     * @param  string  $ip
     * @return bool
     */
    public function hasIpVoted(int $pollId, string $ip): bool;

    /**
     * Persist a new vote record — raw insert only, no business logic.
     *
     * @param  array<string, mixed>  $data
     * @return Vote
     */
    public function createVote(array $data): Vote;

    /**
     * Return vote counts grouped by poll_option_id for a given poll.
     *
     * @param  int  $pollId
     * @return Collection<int, object>
     */
    public function getVoteCountByOption(int $pollId): Collection;
}
