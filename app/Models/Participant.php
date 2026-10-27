<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Participant extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'name',
        'email',
        'phone',
        'token',
        'wishlist',
        'clothing_size',
        'shoe_size',
        'likes',
        'dislikes',
        'drawn_participant_id',
        'revealed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'revealed_at' => 'datetime',
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
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Participant, $this>
     */
    public function drawnParticipant(): BelongsTo
    {
        return $this->belongsTo(Participant::class, 'drawn_participant_id');
    }

    /**
     * Exclusions configured where this participant is the giver (cannot draw excluded_participant).
     *
     * @return HasMany<ParticipantExclusion, $this>
     */
    public function exclusions(): HasMany
    {
        return $this->hasMany(ParticipantExclusion::class, 'participant_id');
    }

    /**
     * Exclusions where this participant cannot be drawn by others.
     *
     * @return HasMany<ParticipantExclusion, $this>
     */
    public function excludedBy(): HasMany
    {
        return $this->hasMany(ParticipantExclusion::class, 'excluded_participant_id');
    }

    /**
     * @return HasMany<ParticipantPushSubscription, $this>
     */
    public function pushSubscriptions(): HasMany
    {
        return $this->hasMany(ParticipantPushSubscription::class, 'participant_id');
    }

    /**
     * Secret messages sent by this participant to the person they drew.
     *
     * @return HasMany<SecretMessage, $this>
     */
    public function sentSecretMessages(): HasMany
    {
        return $this->hasMany(SecretMessage::class, 'sender_participant_id');
    }

    /**
     * Secret messages received by this participant from whoever drew them.
     *
     * @return HasMany<SecretMessage, $this>
     */
    public function receivedSecretMessages(): HasMany
    {
        return $this->hasMany(SecretMessage::class, 'recipient_participant_id');
    }

    public function isRevealed(): bool
    {
        return $this->revealed_at !== null;
    }

    /**
     * Clean phone number for WhatsApp links (digits only).
     */
    public function getCleanPhoneAttribute(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        $clean = preg_replace('/\D+/', '', $this->phone);

        // If Brazilian number without country code (e.g. 11 digits: DD9XXXXXXXX), prepend 55
        if (strlen($clean) === 10 || strlen($clean) === 11) {
            $clean = '55'.$clean;
        }

        return $clean;
    }

    /**
     * Generate unique 32-character token for magic link.
     */
    public static function generateUniqueToken(): string
    {
        do {
            $token = Str::random(32);
        } while (static::where('token', $token)->exists());

        return $token;
    }
}
