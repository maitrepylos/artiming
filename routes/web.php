<?php

// routes/web.php

use App\Http\Controllers\Admin\FormFieldController;
use App\Http\Controllers\Admin\ImportExportController;
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

    // Dans le groupe Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // Import/Export Excel
    Route::get('/events/{event}/import-export', [ImportExportController::class, 'index'])
        ->name('events.import-export');

    Route::get('/events/{event}/export-excel', [ImportExportController::class, 'export'])
        ->name('events.export-excel');

    Route::post('/events/{event}/import-excel', [ImportExportController::class, 'import'])
        ->name('events.import-excel');

    Route::delete('/events/{event}/truncate', [ImportExportController::class, 'truncate'])
        ->name('events.truncate-registrations');

    // Gestion des champs de formulaire personnalisés
Route::post('/events/{event}/form-fields', [FormFieldController::class, 'store'])
    ->name('events.form-fields.store');

Route::put('/events/{event}/form-fields/{formField}', [FormFieldController::class, 'update'])
    ->name('events.form-fields.update');

Route::delete('/events/{event}/form-fields/{formField}', [FormFieldController::class, 'destroy'])
    ->name('events.form-fields.destroy');

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
