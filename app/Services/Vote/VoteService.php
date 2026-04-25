<?php

declare(strict_types=1);

namespace App\Services\Vote;

use App\Events\VoteCast;
use App\Exceptions\AlreadyVotedException;
use App\Exceptions\InvalidOptionException;
use App\Exceptions\PollClosedException;
use App\Models\PollOption;
use App\Models\Vote;
use App\Repositories\Contracts\PollRepositoryInterface;
use App\Repositories\Contracts\VoteRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class VoteService
{
    public function __construct(
        private readonly VoteRepositoryInterface  $votes,
        private readonly PollRepositoryInterface  $polls,
    ) {}

    /**
     * Cast a vote on behalf of an authenticated user or a guest (identified by IP).
     *
     * @param  int      $pollId
     * @param  int      $optionId
     * @param  int|null $userId    Null for guests.
     * @param  string   $ip
     * @param  string|null $userAgent
     * @return Vote
     *
     * @throws PollClosedException
     * @throws InvalidOptionException
     * @throws AlreadyVotedException
     */
    public function castVote(
        int $pollId,
        int $optionId,
        ?int $userId,
        string $ip,
        ?string $userAgent = null,
    ): Vote {
        // Step 1: load poll and verify it is open
        $poll = $this->polls->findWithOptions($pollId);

        if ($poll === null || ! $this->isPollOpen($poll)) {
            throw new PollClosedException($pollId);
        }

        // Step 2: verify the option belongs to this poll
        $optionBelongs = $poll->options->contains('id', $optionId);

        if (! $optionBelongs) {
            throw new InvalidOptionException($optionId, $pollId);
        }

        // Step 3: duplicate-vote check
        if ($userId !== null) {
            if ($this->votes->hasUserVoted($pollId, $userId)) {
                throw new AlreadyVotedException($pollId);
            }
        } else {
            if ($this->votes->hasIpVoted($pollId, $ip)) {
                throw new AlreadyVotedException($pollId);
            }
        }

        // Step 4: persist the vote and increment the denormalised count
        try {
            $vote = DB::transaction(function () use ($pollId, $optionId, $userId, $ip, $userAgent): Vote {
                $vote = $this->votes->createVote([
                    'poll_id'        => $pollId,
                    'poll_option_id' => $optionId,
                    'user_id'        => $userId,
                    'ip_address'     => $ip,
                    'user_agent'     => $userAgent,
                    'voted_at'       => Carbon::now(),
                ]);

                PollOption::where('id', $optionId)->increment('votes_count');

                return $vote;
            });
        } catch (QueryException $e) {
            // DB-level unique constraint violated — treat as duplicate vote
            if (in_array($e->errorInfo[0] ?? '', ['23000', '23505'], true)) {
                throw new AlreadyVotedException($pollId, 0, $e);
            }

            throw $e;
        }

        // Step 5: broadcast for real-time updates (Task 6)
        VoteCast::dispatch($vote);

        return $vote;
    }

    private function isPollOpen(\App\Models\Poll $poll): bool
    {
        if (! $poll->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($poll->starts_at !== null && $poll->starts_at->gt($now)) {
            return false;
        }

        if ($poll->ends_at !== null && $poll->ends_at->lt($now)) {
            return false;
        }

        return true;
    }
}
