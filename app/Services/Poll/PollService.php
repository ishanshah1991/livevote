<?php

declare(strict_types=1);

namespace App\Services\Poll;

use App\Exceptions\PollNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Models\Poll;
use App\Repositories\Contracts\PollRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class PollService
{
    public function __construct(
        private readonly PollRepositoryInterface $polls,
    ) {}

    /**
     * Create a new poll with its options inside a single transaction.
     *
     * @param  int  $adminId
     * @param  array<string, mixed>  $data  Keys: title, description, options[], starts_at, ends_at
     * @return Poll
     */
    public function createPoll(int $adminId, array $data): Poll
    {
        return DB::transaction(function () use ($adminId, $data): Poll {
            /** @var Poll $poll */
            $poll = $this->polls->create([
                'admin_id'    => $adminId,
                'title'       => $data['title'],
                'description' => $data['description'] ?? null,
                'starts_at'   => $data['starts_at'] ?? null,
                'ends_at'     => $data['ends_at'] ?? null,
            ]);

            foreach ($data['options'] as $index => $optionText) {
                $poll->options()->create([
                    'option_text'   => $optionText,
                    'display_order' => $index,
                ]);
            }

            return $poll;
        });
    }

    /**
     * @param  int  $adminId
     * @return Collection<int, Poll>
     */
    public function getAdminPolls(int $adminId): Collection
    {
        return $this->polls->findByAdminId($adminId);
    }

    /**
     * @param  int  $id
     * @return Poll
     *
     * @throws PollNotFoundException
     */
    public function getPollWithOptions(int $id): Poll
    {
        $poll = $this->polls->findWithOptions($id);

        if ($poll === null) {
            throw new PollNotFoundException($id);
        }

        return $poll;
    }

    /**
     * @param  int  $id
     * @return Poll
     *
     * @throws PollNotFoundException
     */
    public function getPollResults(int $id): Poll
    {
        $poll = $this->polls->findWithResults($id);

        if ($poll === null) {
            throw new PollNotFoundException($id);
        }

        return $poll;
    }

    /**
     * Toggle a poll's active state, verifying ownership first.
     *
     * @param  int  $pollId
     * @param  int  $adminId
     * @return bool  The new is_active value.
     *
     * @throws PollNotFoundException
     * @throws UnauthorizedException
     */
    public function togglePollActive(int $pollId, int $adminId): bool
    {
        $poll = $this->polls->find($pollId);

        if ($poll === null) {
            throw new PollNotFoundException($pollId);
        }

        if ($poll->admin_id !== $adminId) {
            throw new UnauthorizedException('You are not authorized to modify this poll.');
        }

        return $this->polls->toggleActive($pollId);
    }

    /**
     * @param  Poll  $poll
     * @return string
     */
    public function generateShareableLink(Poll $poll): string
    {
        return route('poll.show', $poll);
    }
}
