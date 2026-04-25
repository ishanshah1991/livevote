<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Poll;
use App\Repositories\Contracts\PollRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class PollRepository extends BaseRepository implements PollRepositoryInterface
{
    public function __construct(Poll $model)
    {
        parent::__construct($model);
    }

    /**
     * @param  int  $adminId
     * @return Collection<int, Poll>
     */
    public function findByAdminId(int $adminId): Collection
    {
        /** @var Collection<int, Poll> */
        return $this->model->newQuery()
            ->where('admin_id', $adminId)
            ->withCount('votes')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * @param  int  $id
     * @return Poll|null
     */
    public function findWithOptions(int $id): ?Poll
    {
        /** @var Poll|null */
        return $this->model->newQuery()
            ->with(['options' => fn ($q) => $q->orderBy('display_order')])
            ->find($id);
    }

    /**
     * @param  int  $id
     * @return Poll|null
     */
    public function findWithResults(int $id): ?Poll
    {
        /** @var Poll|null */
        return $this->model->newQuery()
            ->with(['options' => fn ($q) => $q->orderBy('display_order')])
            ->find($id);
    }

    /**
     * @param  int  $id
     * @return bool
     */
    public function toggleActive(int $id): bool
    {
        /** @var Poll|null $poll */
        $poll = $this->model->newQuery()->find($id);

        if ($poll === null) {
            return false;
        }

        $poll->is_active = ! $poll->is_active;
        $poll->save();

        return $poll->is_active;
    }
}
