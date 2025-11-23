<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - {{ $event->name }}</title>

    {{-- Tailwind + DaisyUI --}}
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- HTMX --}}
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
</head>
<body class="bg-base-200 min-h-screen">

<div class="container mx-auto px-4 py-8 max-w-4xl">

    {{-- Header avec logo --}}
    <div class="card bg-base-100 shadow-xl mb-6">
        <div class="card-body text-center">
            @if($event->logo)
                <img src="{{ Storage::url($event->logo) }}" alt="{{ $event->name }}" class="h-24 mx-auto mb-4">
            @endif
            <h1 class="card-title text-3xl justify-center">{{ $event->name }}</h1>
            <p class="text-base-content/70">{{ $event->description }}</p>
            <div class="badge badge-primary">{{ $event->event_date->format('d/m/Y') }}</div>
        </div>
    </div>

    {{-- Zone des messages (succès/erreurs) --}}
    <div id="messages" class="mb-6"></div>

    {{-- Formulaire d'inscription --}}
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title mb-4">Formulaire d'inscription</h2>

            <form
                hx-post="{{ route('event.register.store', $event->slug) }}"
                hx-target="#messages"
                hx-swap="innerHTML"
                hx-on::after-request="if(event.detail.successful && !event.detail.xhr.responseText.includes('alert-error')) this.reset();">

                @csrf

                {{-- Nom & Prénom --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Nom <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="text"
                            name="nom"
                            placeholder="Dupont"
                            class="input input-bordered"
                            required>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Prénom <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="text"
                            name="prenom"
                            placeholder="Jean"
                            class="input input-bordered"
                            required>
                    </div>
                </div>

                {{-- Genre --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Genre <span class="text-error">*</span></span>
                    </label>
                    <div class="flex gap-4">
                        <label class="label cursor-pointer gap-2">
                            <input type="radio" name="sexe" value="M" class="radio radio-primary" checked>
                            <span class="label-text">Homme</span>
                        </label>
                        <label class="label cursor-pointer gap-2">
                            <input type="radio" name="sexe" value="F" class="radio radio-primary">
                            <span class="label-text">Femme</span>
                        </label>
                        <label class="label cursor-pointer gap-2">
                            <input type="radio" name="sexe" value="X" class="radio radio-primary">
                            <span class="label-text">Autre</span>
                        </label>
                    </div>
                </div>

                {{-- Date de naissance & Nationalité --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Date de naissance <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="date"
                            name="date_naissance"
                            class="input input-bordered"
                            required>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Nationalité <span class="text-error">*</span></span>
                        </label>
                        <select name="nationalite" class="select select-bordered" required>
                            <option value="">Sélectionner</option>
                            <option value="BEL" selected>🇧🇪 Belgique</option>
                            <option value="FRA">🇫🇷 France</option>
                            <option value="NLD">🇳🇱 Pays-Bas</option>
                            <option value="DEU">🇩🇪 Allemagne</option>
                            <option value="LUX">🇱🇺 Luxembourg</option>
                            <option value="CHE">🇨🇭 Suisse</option>
                            {{-- Ajouter les autres pays --}}
                        </select>
                    </div>
                </div>

                {{-- Catégorie (Course) --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Course <span class="text-error">*</span></span>
                    </label>
                    <select name="category_id" class="select select-bordered" required>
                        <option value="">Sélectionner une course</option>
                        @foreach($event->categories as $category)
                            <option value="{{ $category->id }}"
                                    @if($category->isFull) disabled @endif>
                                {{ $category->name }}
                                @if($category->price)
                                    - {{ number_format($category->price, 2) }}€
                                @endif
                                @if($category->max_participants)
                                    ({{ $category->remaining_spaces }} places restantes)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Club --}}
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text">Club</span>
                    </label>
                    <input
                        type="text"
                        name="club"
                        placeholder="Nom du club (optionnel)"
                        class="input input-bordered">
                </div>

                {{-- Boutons --}}
                <div class="flex gap-4 justify-end">
                    <button type="reset" class="btn btn-ghost">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Valider l'inscription
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- Lien discret vers admin --}}
    @guest
        <div class="text-center mt-8 opacity-30 text-xs">
            <a href="{{ route('login') }}" class="link link-hover">Administration</a>
        </div>
    @endguest

</div>

</body>
</html>
