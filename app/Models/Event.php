<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'code',
        'budget_limit',
        'draw_date',
        'event_date',
        'location',
        'status',
        'drawn_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'draw_date' => 'date',
            'event_date' => 'date',
            'drawn_at' => 'datetime',
            'budget_limit' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Participant, $this>
     */
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * @return HasMany<ParticipantExclusion, $this>
     */
    public function exclusions(): HasMany
    {
        return $this->hasMany(ParticipantExclusion::class);
    }

    public function isDrawn(): bool
    {
        return $this->status === 'drawn' && $this->drawn_at !== null;
    }

    public function canDraw(): bool
    {
        return $this->participants()->count() >= 3;
    }

    /**
     * Generate an 8-character unique alphanumeric code.
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
