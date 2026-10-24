<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ParticipantExclusion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExclusionController extends Controller
{
    /**
     * Add a restriction between two participants.
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        abort_if($event->user_id !== $request->user()->id, 403);

        if ($event->isDrawn()) {
            return back()->with('error', 'O sorteio já foi realizado. Resete o sorteio para configurar restrições.');
        }

        $validated = $request->validate([
            'participant_id' => [
                'required',
                'integer',
                Rule::exists('participants', 'id')->where('event_id', $event->id),
            ],
            'excluded_participant_id' => [
                'required',
                'integer',
                'different:participant_id',
                Rule::exists('participants', 'id')->where('event_id', $event->id),
            ],
            'bidirectional' => ['nullable', 'boolean'],
        ]);

        // Check if already exists
        ParticipantExclusion::firstOrCreate([
            'event_id' => $event->id,
            'participant_id' => $validated['participant_id'],
            'excluded_participant_id' => $validated['excluded_participant_id'],
        ]);

        if (! empty($validated['bidirectional'])) {
            ParticipantExclusion::firstOrCreate([
                'event_id' => $event->id,
                'participant_id' => $validated['excluded_participant_id'],
                'excluded_participant_id' => $validated['participant_id'],
            ]);
        }

        return back()->with('success', 'Restrição configurada com sucesso!');
    }

    /**
     * Remove a restriction.
     */
    public function destroy(Request $request, Event $event, ParticipantExclusion $exclusion): RedirectResponse
    {
        abort_if($event->user_id !== $request->user()->id, 403);
        abort_if($exclusion->event_id !== $event->id, 404);

        if ($event->isDrawn()) {
            return back()->with('error', 'O sorteio já foi realizado. Resete o sorteio para alterar restrições.');
        }

        $exclusion->delete();

        return back()->with('success', 'Restrição removida com sucesso.');
    }
}
