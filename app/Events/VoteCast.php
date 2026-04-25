<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class VoteCast implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Poll $poll,
        public readonly PollOption $option,
        public readonly int $newCount,
    ) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [new Channel("poll.{$this->poll->id}")];
    }

    public function broadcastAs(): string
    {
        return 'vote.cast';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'poll_id'     => $this->poll->id,
            'option_id'   => $this->option->id,
            'votes_count' => $this->newCount,
            'percentages' => $this->computePercentages(),
        ];
    }

    /**
     * @return array<int, array{option_id: int, label: string, votes_count: int, percentage: float}>
     */
    private function computePercentages(): array
    {
        $options = $this->poll->options()->get();
        $total   = $options->sum('votes_count');

        return $options->map(static function (PollOption $opt) use ($total): array {
            return [
                'option_id'   => $opt->id,
                'label'       => $opt->option_text,
                'votes_count' => $opt->votes_count,
                'percentage'  => $total > 0 ? round($opt->votes_count / $total * 100, 1) : 0.0,
            ];
        })->values()->all();
    }
}
