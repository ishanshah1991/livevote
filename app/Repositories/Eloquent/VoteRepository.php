<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Vote;
use App\Repositories\Contracts\VoteRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class VoteRepository implements VoteRepositoryInterface
{
    public function __construct(
        private readonly Vote $model,
    ) {}

    public function hasUserVoted(int $pollId, int $userId): bool
    {
        return $this->model->newQuery()
            ->where('poll_id', $pollId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function hasIpVoted(int $pollId, string $ip): bool
    {
        return $this->model->newQuery()
            ->where('poll_id', $pollId)
            ->where('ip_address', $ip)
            ->exists();
    }

    public function createVote(array $data): Vote
    {
        /** @var Vote */
        return $this->model->newQuery()->create($data);
    }

    public function getVoteCountByOption(int $pollId): Collection
    {
        return DB::table('votes')
            ->select('poll_option_id', DB::raw('COUNT(*) as votes_count'))
            ->where('poll_id', $pollId)
            ->groupBy('poll_option_id')
            ->get();
    }
}
