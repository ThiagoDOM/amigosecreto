<?php

namespace App\Http\Controllers;

use App\Services\ParticipantSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ParticipantSessionController extends Controller
{
    /**
     * Clear participant session for a specific token or all tokens.
     */
    public function forget(Request $request, ParticipantSessionService $sessionService): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['nullable', 'string'],
        ]);

        if (! empty($validated['token'])) {
            $sessionService->forgetToken($validated['token'], $request);
        } else {
            $sessionService->forgetAll();
        }

        $referer = (string) $request->header('referer');
        if (str_contains($referer, '/amigo/')) {
            return redirect()->route('home')->with('success', 'Você saiu da sessão deste evento. Guarde seu link exclusivo para acessar novamente quando quiser.');
        }

        return back()->with('success', 'Sessão do evento encerrada neste dispositivo com sucesso.');
    }
}
