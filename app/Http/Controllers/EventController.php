<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\DrawService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    /**
     * Display a listing of the organizer's events.
     */
    public function index(Request $request): Response
    {
        $events = $request->user()
            ->events()
            ->withCount(['participants', 'participants as revealed_count' => function ($query): void {
                $query->whereNotNull('revealed_at');
            }])
            ->latest()
            ->get()
            ->map(fn (Event $event): array => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'code' => $event->code,
                'budget_limit' => $event->budget_limit,
                'draw_date' => $event->draw_date?->format('Y-m-d'),
                'event_date' => $event->event_date?->format('Y-m-d'),
                'location' => $event->location,
                'status' => $event->status,
                'drawn_at' => $event->drawn_at?->format('d/m/Y H:i'),
                'participants_count' => $event->participants_count,
                'revealed_count' => $event->revealed_count,
                'invite_url' => route('invite.show', ['code' => $event->code]),
            ]);

        return Inertia::render('Dashboard', [
            'events' => $events,
        ]);
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): Response
    {
        return Inertia::render('Events/Create');
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'budget_limit' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'draw_date' => ['nullable', 'date'],
            'event_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $event = $request->user()->events()->create([
            ...$validated,
            'code' => Event::generateUniqueCode(),
            'status' => 'open',
        ]);

        return redirect()->route('events.show', $event)
            ->with('success', 'Evento criado com sucesso! Compartilhe o link de convite com os participantes.');
    }

    /**
     * Display the specified event dashboard.
     */
    public function show(Request $request, Event $event): Response
    {
        abort_if($event->user_id !== $request->user()->id, 403);

        $event->load([
            'participants' => fn ($q) => $q->orderBy('name'),
            'exclusions.participant',
            'exclusions.excludedParticipant',
        ]);

        $participants = $event->participants->map(fn ($p): array => [
            'id' => $p->id,
            'name' => $p->name,
            'email' => $p->email,
            'phone' => $p->phone,
            'clean_phone' => $p->clean_phone,
            'wishlist' => $p->wishlist,
            'clothing_size' => $p->clothing_size,
            'shoe_size' => $p->shoe_size,
            'likes' => $p->likes,
            'dislikes' => $p->dislikes,
            'revealed_at' => $p->revealed_at?->format('d/m/Y H:i'),
            'is_revealed' => $p->isRevealed(),
            'magic_url' => route('secret.show', ['token' => $p->token]),
            'created_at' => $p->created_at->format('d/m/Y H:i'),
            // Only expose drawn friend if event is drawn (organizer view can see for troubleshooting)
            'drawn_friend' => $event->isDrawn() && $p->drawn_participant_id ? [
                'id' => $p->drawnParticipant?->id,
                'name' => $p->drawnParticipant?->name,
            ] : null,
        ]);

        $exclusions = $event->exclusions->map(fn ($ex): array => [
            'id' => $ex->id,
            'participant_id' => $ex->participant_id,
            'participant_name' => $ex->participant?->name,
            'excluded_participant_id' => $ex->excluded_participant_id,
            'excluded_participant_name' => $ex->excludedParticipant?->name,
        ]);

        return Inertia::render('Events/Show', [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'code' => $event->code,
                'budget_limit' => $event->budget_limit,
                'draw_date' => $event->draw_date?->format('Y-m-d'),
                'event_date' => $event->event_date?->format('Y-m-d'),
                'location' => $event->location,
                'status' => $event->status,
                'drawn_at' => $event->drawn_at?->format('d/m/Y H:i'),
                'is_drawn' => $event->isDrawn(),
                'can_draw' => $event->canDraw(),
                'invite_url' => route('invite.show', ['code' => $event->code]),
            ],
            'participants' => $participants,
            'exclusions' => $exclusions,
        ]);
    }

    /**
     * Update the event details.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        abort_if($event->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'budget_limit' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'draw_date' => ['nullable', 'date'],
            'event_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $event->update($validated);

        return back()->with('success', 'Evento atualizado com sucesso!');
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Request $request, Event $event): RedirectResponse
    {
        abort_if($event->user_id !== $request->user()->id, 403);

        $event->delete();

        return redirect()->route('dashboard')->with('success', 'Evento excluído com sucesso.');
    }

    /**
     * Trigger the Secret Santa draw.
     */
    public function draw(Request $request, Event $event, DrawService $drawService): RedirectResponse
    {
        abort_if($event->user_id !== $request->user()->id, 403);

        try {
            $drawService->draw($event);

            $notifiedCount = $event->participants()
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->count();

            $successMessage = $notifiedCount > 0
                ? "🎉 Sorteio realizado com sucesso! Notificações enviadas por e-mail para {$notifiedCount} participante(s)."
                : '🎉 Sorteio realizado com sucesso! Os links secretos já estão prontos para envio.';

            return back()->with('success', $successMessage);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reset the draw to allow changes and re-drawing.
     */
    public function resetDraw(Request $request, Event $event, DrawService $drawService): RedirectResponse
    {
        abort_if($event->user_id !== $request->user()->id, 403);

        $drawService->reset($event);

        return back()->with('success', 'Sorteio resetado. Agora você pode ajustar os participantes ou restrições.');
    }
}
