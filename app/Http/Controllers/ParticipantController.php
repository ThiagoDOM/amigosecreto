<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use App\Services\AffiliateLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    /**
     * Store a newly created participant manually added by the organizer.
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        abort_if($event->user_id !== $request->user()->id, 403);

        if ($event->isDrawn()) {
            return back()->with('error', 'Não é possível adicionar participantes com o sorteio já realizado. Resete o sorteio primeiro.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'wishlist' => ['nullable'],
            'clothing_size' => ['nullable', 'string', 'max:50'],
            'shoe_size' => ['nullable', 'string', 'max:50'],
            'likes' => ['nullable', 'string', 'max:500'],
            'dislikes' => ['nullable', 'string', 'max:500'],
        ]);

        if (is_array($request->input('wishlist'))) {
            $cleaned = array_values(array_filter($request->input('wishlist'), fn ($i) => ! empty($i['title'])));
            $processed = app(AffiliateLinkService::class)->processWishlist($cleaned);
            $validated['wishlist'] = json_encode($processed, JSON_UNESCAPED_UNICODE);
        }

        $event->participants()->create([
            ...$validated,
            'token' => Participant::generateUniqueToken(),
        ]);

        return back()->with('success', 'Participante adicionado com sucesso!');
    }

    /**
     * Remove the specified participant from the event.
     */
    public function destroy(Request $request, Event $event, Participant $participant): RedirectResponse
    {
        abort_if($event->user_id !== $request->user()->id, 403);
        abort_if($participant->event_id !== $event->id, 404);

        if ($event->isDrawn()) {
            return back()->with('error', 'O sorteio já foi realizado! Para remover um participante, resete o sorteio primeiro.');
        }

        $participant->delete();

        return back()->with('success', 'Participante removido com sucesso.');
    }
}
