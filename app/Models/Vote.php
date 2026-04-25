<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A single vote cast against a poll option, by either a logged-in user or a guest (IP-tracked).
 *
 * @property int $id
 * @property int $poll_id
 * @property int $poll_option_id
 * @property int|null $user_id
 * @property string $ip_address
 * @property string|null $user_agent
 * @property Carbon $voted_at
 */
final class Vote extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'pgsql';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'votes';

    /**
     * Disable Laravel's automatic created_at / updated_at columns — only voted_at exists.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'poll_id',
        'poll_option_id',
        'user_id',
        'ip_address',
        'user_agent',
        'voted_at',
    ];

    /**
     * Get the attribute casts for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'voted_at' => 'datetime',
        ];
    }

    /**
     * Poll this vote belongs to.
     *
     * @return BelongsTo<Poll, Vote>
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Specific option chosen by the voter.
     *
     * @return BelongsTo<PollOption, Vote>
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(PollOption::class, 'poll_option_id');
    }

    /**
     * Authenticated user who cast the vote, when not a guest.
     *
     * @return BelongsTo<User, Vote>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
