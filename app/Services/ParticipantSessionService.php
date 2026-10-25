<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ParticipantSessionService
{
    public const COOKIE_NAME = 'participant_tokens';

    public const COOKIE_LIFETIME_MINUTES = 259200; // 180 days

    /**
     * Retrieve all saved participant tokens from the request cookie or current queue.
     *
     * @return array<string>
     */
    public function getTokens(Request $request): array
    {
        $raw = $request->cookie(self::COOKIE_NAME);

        // If queued in the current request lifecycle, prefer queued value
        $queued = Cookie::queued(self::COOKIE_NAME);
        if ($queued !== null) {
            $raw = $queued->getValue();
        }

        if (empty($raw)) {
            return [];
        }

        if (is_array($raw)) {
            return array_values(array_filter(array_unique($raw)));
        }

        $decoded = json_decode((string) $raw, true);

        if (! is_array($decoded)) {
            return [];
        }

        return array_values(array_filter(array_unique(array_map('strval', $decoded))));
    }

    /**
     * Save a participant token to the cookie queue.
     */
    public function storeToken(string $token, ?Request $request = null): void
    {
        $token = trim($token);
        if ($token === '') {
            return;
        }

        $existing = $request ? $this->getTokens($request) : [];
        if (! in_array($token, $existing, true)) {
            array_unshift($existing, $token);
        }

        // Limit to 20 tokens to avoid cookie overflow
        $tokens = array_slice(array_values(array_unique($existing)), 0, 20);

        Cookie::queue(self::COOKIE_NAME, json_encode($tokens), self::COOKIE_LIFETIME_MINUTES);
    }

    /**
     * Remove a participant token from the cookie queue.
     */
    public function forgetToken(string $token, ?Request $request = null): void
    {
        $existing = $request ? $this->getTokens($request) : [];
        $remaining = array_values(array_diff($existing, [$token]));

        if (empty($remaining)) {
            Cookie::queue(Cookie::forget(self::COOKIE_NAME));
        } else {
            Cookie::queue(self::COOKIE_NAME, json_encode($remaining), self::COOKIE_LIFETIME_MINUTES);
        }
    }

    /**
     * Clear all participant sessions.
     */
    public function forgetAll(): void
    {
        Cookie::queue(Cookie::forget(self::COOKIE_NAME));
    }

    /**
     * Get all active participations that have not expired.
     * Session is active until at least 3 days after event date.
     *
     * @return array<array<string, mixed>>
     */
    public function getActiveParticipations(Request $request): array
    {
        $tokens = $this->getTokens($request);

        if (empty($tokens)) {
            return [];
        }

        $participants = Participant::with(['event.user:id,name'])
            ->whereIn('token', $tokens)
            ->get();

        $active = [];
        $now = now();

        foreach ($participants as $participant) {
            $event = $participant->event;

            if (! $event) {
                continue;
            }

            // Expiration rule: active until at least 3 days after event_date
            if ($event->event_date) {
                $cutoff = $event->event_date->copy()->endOfDay()->addDays(3);
                if ($now->isAfter($cutoff)) {
                    continue;
                }
            } elseif ($event->draw_date) {
                // If no event_date is configured, keep active until 30 days after draw_date
                $cutoff = $event->draw_date->copy()->endOfDay()->addDays(30);
                if ($now->isAfter($cutoff)) {
                    continue;
                }
            }

            $active[] = [
                'token' => $participant->token,
                'participant_id' => $participant->id,
                'participant_name' => $participant->name,
                'event_id' => $event->id,
                'event_title' => $event->title,
                'event_code' => $event->code,
                'event_date' => $event->event_date?->format('d/m/Y'),
                'draw_date' => $event->draw_date?->format('d/m/Y'),
                'is_drawn' => $event->isDrawn(),
                'organizer_name' => $event->user?->name ?? 'Organizador',
                'magic_url' => route('secret.show', ['token' => $participant->token]),
            ];
        }

        return $active;
    }

    /**
     * Find an active participant token for a given event, if any.
     */
    public function getActiveTokenForEvent(Request $request, Event $event): ?string
    {
        $tokens = $this->getTokens($request);

        if (empty($tokens)) {
            return null;
        }

        // Check if event is already past the 3 days cutoff
        if ($event->event_date) {
            $cutoff = $event->event_date->copy()->endOfDay()->addDays(3);
            if (now()->isAfter($cutoff)) {
                return null;
            }
        } elseif ($event->draw_date) {
            $cutoff = $event->draw_date->copy()->endOfDay()->addDays(30);
            if (now()->isAfter($cutoff)) {
                return null;
            }
        }

        $participant = Participant::where('event_id', $event->id)
            ->whereIn('token', $tokens)
            ->latest('id')
            ->first();

        return $participant?->token;
    }
}
