<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * A poll authored by an admin and answered by users / guests.
 *
 * @property int $id
 * @property int $admin_id
 * @property string $title
 * @property string|null $description
 * @property bool $is_active
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 */
final class Poll extends Model
{
    use HasFactory;
    use SoftDeletes;

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
    protected $table = 'polls';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_id',
        'title',
        'description',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    /**
     * Get the attribute casts for the model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * Owning admin.
     *
     * @return BelongsTo<Admin, Poll>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Selectable options for this poll.
     *
     * @return HasMany<PollOption>
     */
    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }

    /**
     * Vote records cast for this poll.
     *
     * @return HasMany<Vote>
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Restrict the query to polls that are flagged active and within their date window.
     *
     * A null starts_at means "open immediately" and a null ends_at means "open indefinitely".
     *
     * @param  Builder<Poll>  $query
     * @return Builder<Poll>
     */
    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query
            ->where('is_active', true)
            ->where(function (Builder $q) use ($now): void {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $q) use ($now): void {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }
}
