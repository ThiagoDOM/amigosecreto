<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MarketingSubscriber;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted', 'required'],
            'marketing_opt_in' => ['nullable', 'boolean'],
        ], [
            'terms.accepted' => 'Você deve concordar com os Termos de Uso e a Política de Privacidade para criar sua conta.',
            'terms.required' => 'Você deve concordar com os Termos de Uso e a Política de Privacidade para criar sua conta.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->boolean('marketing_opt_in')) {
            MarketingSubscriber::subscribe(
                email: $user->email,
                name: $user->name,
                source: 'organizer_registration',
                ipAddress: $request->ip(),
            );
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
