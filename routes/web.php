<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ExclusionController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecretRevealController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing page
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('home');

// Institutional & Legal Pages (LGPD & AdSense compliance)
Route::get('/politica-de-privacidade', function () {
    return Inertia::render('Legal/PrivacyPolicy');
})->name('privacy');

Route::get('/termos-de-uso', function () {
    return Inertia::render('Legal/TermsOfService');
})->name('terms');

// Google AdSense ads.txt verification
Route::get('/ads.txt', function () {
    $adsTxt = public_path('ads.txt');
    if (file_exists($adsTxt) && ! empty(trim(file_get_contents($adsTxt)))) {
        return response(file_get_contents($adsTxt), 200, ['Content-Type' => 'text/plain']);
    }

    $pubId = env('GOOGLE_ADSENSE_PUB_ID') ?: env('VITE_GOOGLE_ADSENSE_ID');
    if (! empty($pubId)) {
        $cleanPub = str_replace('ca-', '', $pubId);

        return response("google.com, {$cleanPub}, DIRECT, f08c47fec0942fa0\n", 200, ['Content-Type' => 'text/plain']);
    }

    return response("# ads.txt - GiftMatch\n", 200, ['Content-Type' => 'text/plain']);
});

// Public Invite Routes (No login required)
Route::get('/convite/{code}', [InviteController::class, 'show'])->name('invite.show');
Route::post('/convite/{code}', [InviteController::class, 'join'])->name('invite.join');

// Public Secret Magic Link Routes (No login required)
Route::get('/amigo/{token}', [SecretRevealController::class, 'show'])->name('secret.show');
Route::post('/amigo/{token}/reveal', [SecretRevealController::class, 'reveal'])->name('secret.reveal');
Route::put('/amigo/{token}/wishlist', [SecretRevealController::class, 'updateWishlist'])->name('secret.wishlist.update');

// Protected Organizer Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [EventController::class, 'index'])->name('dashboard');

    // Event Management
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // Draw Actions
    Route::post('/events/{event}/draw', [EventController::class, 'draw'])->name('events.draw');
    Route::post('/events/{event}/reset-draw', [EventController::class, 'resetDraw'])->name('events.reset-draw');

    // Participant Manual Management
    Route::post('/events/{event}/participants', [ParticipantController::class, 'store'])->name('events.participants.store');
    Route::delete('/events/{event}/participants/{participant}', [ParticipantController::class, 'destroy'])->name('events.participants.destroy');

    // Exclusions / Restrictions
    Route::post('/events/{event}/exclusions', [ExclusionController::class, 'store'])->name('events.exclusions.store');
    Route::delete('/events/{event}/exclusions/{exclusion}', [ExclusionController::class, 'destroy'])->name('events.exclusions.destroy');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
