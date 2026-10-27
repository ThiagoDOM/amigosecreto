<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecretMessage extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'event_id',
        'sender_participant_id',
        'recipient_participant_id',
        'question',
        'answer',
        'answered_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'answered_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Event, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @return BelongsTo<Participant, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'sender_participant_id');
    }

    /**
     * @return BelongsTo<Participant, $this>
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'recipient_participant_id');
    }
}
