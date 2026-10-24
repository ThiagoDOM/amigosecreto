<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SecretRevealController extends Controller
{
    /**
     * Display the participant's secret space and friend reveal.
     */
    public function show(string $token): Response
    {
        $participant = Participant::with(['event.user:id,name', 'drawnParticipant'])
            ->where('token', $token)
            ->firstOrFail();

        $event = $participant->event;
        $isDrawn = $event->isDrawn();
        $isRevealed = $participant->isRevealed();

        $secretFriendData = null;

        if ($isDrawn && $participant->drawnParticipant) {
            $friend = $participant->drawnParticipant;

            // Only send secret friend details to client if revealed or as revealable payload
            $secretFriendData = [
                'ready_to_reveal' => true,
                'name' => $friend->name,
                'wishlist' => $friend->wishlist,
                'clothing_size' => $friend->clothing_size,
                'shoe_size' => $friend->shoe_size,
                'likes' => $friend->likes,
                'dislikes' => $friend->dislikes,
            ];
        }

        return Inertia::render('Secret/Show', [
            'participant' => [
                'id' => $participant->id,
                'name' => $participant->name,
                'token' => $participant->token,
                'email' => $participant->email,
                'phone' => $participant->phone,
                'wishlist' => $participant->wishlist,
                'clothing_size' => $participant->clothing_size,
                'shoe_size' => $participant->shoe_size,
                'likes' => $participant->likes,
                'dislikes' => $participant->dislikes,
                'is_revealed' => $isRevealed,
                'revealed_at' => $participant->revealed_at?->format('d/m/Y H:i'),
            ],
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'budget_limit' => $event->budget_limit,
                'event_date' => $event->event_date?->format('d/m/Y'),
                'draw_date' => $event->draw_date?->format('d/m/Y'),
                'location' => $event->location,
                'status' => $event->status,
                'is_drawn' => $isDrawn,
                'organizer_name' => $event->user?->name ?? 'Organizador',
                'participants_count' => $event->participants()->count(),
            ],
            'secret_friend' => $secretFriendData,
        ]);
    }

    /**
     * Mark the participant as having revealed their secret friend.
     */
    public function reveal(Request $request, string $token): RedirectResponse
    {
        $participant = Participant::where('token', $token)->firstOrFail();

        if (! $participant->isRevealed()) {
            $participant->update([
                'revealed_at' => now(),
            ]);
        }

        return back()->with('success', 'Amigo secreto revelado!');
    }

    /**
     * Allow participant to update their own wishlist and preferences.
     */
    public function updateWishlist(Request $request, string $token): RedirectResponse
    {
        $participant = Participant::where('token', $token)->firstOrFail();

        $validated = $request->validate([
            'wishlist' => ['nullable'],
            'clothing_size' => ['nullable', 'string', 'max:50'],
            'shoe_size' => ['nullable', 'string', 'max:50'],
            'likes' => ['nullable', 'string', 'max:500'],
            'dislikes' => ['nullable', 'string', 'max:500'],
        ]);

        if (is_array($request->input('wishlist'))) {
            $validated['wishlist'] = json_encode(
                array_values(array_filter($request->input('wishlist'), fn ($i) => ! empty($i['title']))),
                JSON_UNESCAPED_UNICODE
            );
        }

        $participant->update($validated);

        return back()->with('success', 'Sua lista de presentes foi atualizada com sucesso!');
    }
}
