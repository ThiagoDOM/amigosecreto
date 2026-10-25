<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\MarketingSubscriber;
use App\Models\Participant;
use App\Services\AffiliateLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InviteController extends Controller
{
    /**
     * Show the public event invitation and join page.
     */
    public function show(string $code): Response
    {
        $event = Event::with('user:id,name')
            ->withCount('participants')
            ->where('code', $code)
            ->firstOrFail();

        return Inertia::render('Invite/Show', [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'code' => $event->code,
                'budget_limit' => $event->budget_limit,
                'draw_date' => $event->draw_date?->format('d/m/Y'),
                'event_date' => $event->event_date?->format('d/m/Y'),
                'location' => $event->location,
                'status' => $event->status,
                'is_drawn' => $event->isDrawn(),
                'organizer_name' => $event->user?->name ?? 'Organizador',
                'participants_count' => $event->participants_count,
            ],
        ]);
    }

    /**
     * Handle participant joining via the invite link.
     */
    public function join(Request $request, string $code): RedirectResponse
    {
        $event = Event::where('code', $code)->firstOrFail();

        if ($event->isDrawn()) {
            return back()->with('error', 'As inscrições para este amigo secreto já foram encerradas pois o sorteio foi realizado!');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'wishlist' => ['nullable'],
            'clothing_size' => ['nullable', 'string', 'max:50'],
            'shoe_size' => ['nullable', 'string', 'max:50'],
            'likes' => ['nullable', 'string', 'max:500'],
            'dislikes' => ['nullable', 'string', 'max:500'],
            'marketing_opt_in' => ['nullable', 'boolean'],
        ]);

        $marketingOptIn = $request->boolean('marketing_opt_in');
        unset($validated['marketing_opt_in']);

        if (is_array($request->input('wishlist'))) {
            $cleaned = array_values(array_filter($request->input('wishlist'), fn ($i) => ! empty($i['title'])));
            $processed = app(AffiliateLinkService::class)->processWishlist($cleaned);
            $validated['wishlist'] = json_encode($processed, JSON_UNESCAPED_UNICODE);
        }

        $participant = $event->participants()->create([
            ...$validated,
            'user_id' => $request->user()?->id,
            'token' => Participant::generateUniqueToken(),
        ]);

        if ($marketingOptIn && ! empty($participant->email)) {
            MarketingSubscriber::subscribe(
                email: $participant->email,
                name: $participant->name,
                source: 'participant_invite',
                ipAddress: $request->ip(),
            );
        }

        return redirect()->route('secret.show', ['token' => $participant->token])
            ->with('just_registered', true)
            ->with('success', '🎉 Você entrou no Amigo Secreto com sucesso! Guarde este link nos seus favoritos ou WhatsApp.');
    }
}
