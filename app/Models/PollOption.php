<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A single answer choice belonging to a poll.
 *
 * @property int $id
 * @property int $poll_id
 * @property string $option_text
 * @property int $display_order
 * @property int $votes_count
 */
final class PollOption extends Model
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
    protected $table = 'poll_options';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'poll_id',
        'option_text',
        'display_order',
        'votes_count',
    ];

    /**
     * Get the attribute casts for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'votes_count' => 'integer',
        ];
    }

    /**
     * Owning poll.
     *
     * @return BelongsTo<Poll, PollOption>
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(Poll::class);
    }

    /**
     * Vote records pointing at this option.
     *
     * @return HasMany<Vote>
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}
