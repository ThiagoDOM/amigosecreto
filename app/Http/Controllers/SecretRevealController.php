<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\SecretMessage;
use App\Services\AffiliateLinkService;
use App\Services\ParticipantSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SecretRevealController extends Controller
{
    /**
     * Display the participant's secret space and friend reveal.
     */
    public function show(Request $request, string $token, ParticipantSessionService $sessionService): Response
    {
        $participant = Participant::with(['event.user:id,name', 'drawnParticipant'])
            ->where('token', $token)
            ->firstOrFail();

        $sessionService->storeToken($participant->token, $request);

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

        $sentQuestions = $isDrawn && $participant->drawn_participant_id
            ? $participant->sentSecretMessages()
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(fn ($m) => [
                    'id' => $m->id,
                    'question' => $m->question,
                    'answer' => $m->answer,
                    'answered_at' => $m->answered_at?->format('d/m/Y H:i'),
                    'created_at' => $m->created_at->format('d/m/Y H:i'),
                ])
            : [];

        $receivedQuestions = $isDrawn
            ? $participant->receivedSecretMessages()
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(fn ($m) => [
                    'id' => $m->id,
                    'question' => $m->question,
                    'answer' => $m->answer,
                    'answered_at' => $m->answered_at?->format('d/m/Y H:i'),
                    'created_at' => $m->created_at->format('d/m/Y H:i'),
                ])
            : [];

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
                'created_at' => $participant->created_at?->format('d/m/Y H:i'),
            ],
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'theme' => $event->theme ?? 'traditional',
                'budget_limit' => $event->budget_limit,
                'event_date' => $event->event_date?->format('d/m/Y'),
                'raw_event_date' => $event->event_date?->format('Y-m-d'),
                'draw_date' => $event->draw_date?->format('d/m/Y'),
                'raw_draw_date' => $event->draw_date?->format('Y-m-d'),
                'location' => $event->location,
                'status' => $event->status,
                'is_drawn' => $isDrawn,
                'drawn_at' => $event->drawn_at?->format('d/m/Y H:i'),
                'organizer_name' => $event->user?->name ?? 'Organizador',
                'participants_count' => $event->participants()->count(),
            ],
            'secret_friend' => $secretFriendData,
            'sent_questions' => $sentQuestions,
            'received_questions' => $receivedQuestions,
            'vapid_public_key' => config('services.vapid.public_key'),
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
            $cleaned = array_values(array_filter($request->input('wishlist'), fn ($i) => ! empty($i['title'])));
            $processed = app(AffiliateLinkService::class)->processWishlist($cleaned);
            $validated['wishlist'] = json_encode($processed, JSON_UNESCAPED_UNICODE);
        }

        $participant->update($validated);

        return back()->with('success', 'Sua lista de presentes foi atualizada com sucesso!');
    }

    /**
     * Store or update Web Push subscription for participant.
     */
    public function storePushSubscription(Request $request, string $token): JsonResponse
    {
        $participant = Participant::where('token', $token)->firstOrFail();

        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'url'],
            'keys' => ['required', 'array'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
            'contentEncoding' => ['nullable', 'string'],
        ]);

        $endpoint = $validated['endpoint'];
        $endpointHash = hash('sha256', $endpoint);

        $participant->pushSubscriptions()->updateOrCreate(
            ['endpoint_hash' => $endpointHash],
            [
                'endpoint' => $endpoint,
                'public_key' => $validated['keys']['p256dh'],
                'auth_token' => $validated['keys']['auth'],
                'content_encoding' => $validated['contentEncoding'] ?? 'aes128gcm',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Notificações push vinculadas com sucesso ao participante.',
        ]);
    }

    /**
     * Remove Web Push subscription for participant.
     */
    public function destroyPushSubscription(Request $request, string $token): JsonResponse
    {
        $participant = Participant::where('token', $token)->firstOrFail();

        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'url'],
        ]);

        $endpointHash = hash('sha256', $validated['endpoint']);
        $participant->pushSubscriptions()->where('endpoint_hash', $endpointHash)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscrição push desvinculada com sucesso.',
        ]);
    }

    /**
     * Send an anonymous question to the participant's drawn secret friend.
     */
    public function sendSecretMessage(Request $request, string $token): RedirectResponse
    {
        $participant = Participant::where('token', $token)->firstOrFail();
        $event = $participant->event;

        if (! $event->isDrawn() || ! $participant->drawn_participant_id) {
            return back()->with('error', 'O sorteio precisa ser realizado para você enviar um bilhete secreto.');
        }

        $validated = $request->validate([
            'question' => ['required', 'string', 'min:3', 'max:400'],
        ]);

        $count = $participant->sentSecretMessages()->count();
        if ($count >= 10) {
            return back()->with('error', 'Você atingiu o limite de perguntas anônimas para este amigo secreto.');
        }

        $participant->sentSecretMessages()->create([
            'event_id' => $event->id,
            'recipient_participant_id' => $participant->drawn_participant_id,
            'question' => $validated['question'],
        ]);

        return back()->with('success', 'Bilhete secreto enviado com sucesso para o seu amigo secreto!');
    }

    /**
     * Answer an anonymous question received from whoever drew this participant.
     */
    public function answerSecretMessage(Request $request, string $token, SecretMessage $message): RedirectResponse
    {
        $participant = Participant::where('token', $token)->firstOrFail();

        if ($message->recipient_participant_id !== $participant->id) {
            abort(403, 'Acesso não autorizado a esta mensagem.');
        }

        $validated = $request->validate([
            'answer' => ['required', 'string', 'min:1', 'max:500'],
        ]);

        $message->update([
            'answer' => $validated['answer'],
            'answered_at' => now(),
        ]);

        return back()->with('success', 'Sua resposta foi enviada anonimamente para o seu amigo secreto!');
    }
}
