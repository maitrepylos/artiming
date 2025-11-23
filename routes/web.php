<?php

// routes/web.php

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Publiques (Inscription)
|--------------------------------------------------------------------------
*/

// Page d'accueil - Liste des événements actifs
Route::get('/', function () {
    $events = \App\Models\Event::active()
        ->orderBy('event_date', 'desc')
        ->get();
    return view('welcome', compact('events'));
})->name('home');

// Formulaire d'inscription pour un événement spécifique
Route::get('/event/{slug}', [RegistrationController::class, 'show'])
    ->name('event.register');

// Traiter l'inscription (HTMX)
Route::post('/event/{slug}', [RegistrationController::class, 'store'])
    ->name('event.register.store');

/*
|--------------------------------------------------------------------------
| Routes Admin (Authentification requise)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', function () {
        return redirect()->route('admin.events.index');
    })->name('dashboard');

    // Gestion des événements
    Route::resource('events', EventController::class);

    // Gestion des dossards pour un événement
    Route::get('/events/{event}/bibs', [EventController::class, 'manageBibs'])
        ->name('events.bibs');

    // Rechercher des participants (HTMX)
    Route::get('/events/{slug}/search', [RegistrationController::class, 'search'])
        ->name('events.search');

    // Mettre à jour un participant (dossard, paiement)
    Route::put('/events/{slug}/registrations/{id}', [RegistrationController::class, 'update'])
        ->name('events.update-bib');

    // Export Excel
    Route::get('/events/{event}/export', [EventController::class, 'export'])
        ->name('events.export');

    // Gestion des catégories
    Route::post('/events/{event}/categories', [CategoryController::class, 'store'])
        ->name('events.categories.store');

    Route::put('/events/{event}/categories/{category}', [CategoryController::class, 'update'])
        ->name('events.categories.update');

    Route::delete('/events/{event}/categories/{category}', [CategoryController::class, 'destroy'])
        ->name('events.categories.destroy');
});

/*
|--------------------------------------------------------------------------
| Authentification
|--------------------------------------------------------------------------
*/

// Si tu utilises Laravel Breeze ou Jetstream, ces routes sont déjà définies
// Sinon, voici une implémentation simple :

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('admin');
    }

    return back()->withErrors([
        'email' => 'Les identifiants sont incorrects.',
    ])->onlyInput('email');
})->name('login.post');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');
