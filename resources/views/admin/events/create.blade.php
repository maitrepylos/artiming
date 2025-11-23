@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-4xl">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold">Créer un nouvel événement</h1>
                <p class="text-base-content/70">Remplissez les informations de l'événement</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-ghost">
                ← Retour
            </a>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">

                <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Nom de l'événement --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Nom de l'événement <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            placeholder="Ex: Marathon de Bruxelles 2025"
                            class="input input-bordered @error('name') input-error @enderror"
                            value="{{ old('name') }}"
                            required>
                        @error('name')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                        <label class="label">
                            <span class="label-text-alt">Un slug sera automatiquement généré à partir du nom</span>
                        </label>
                    </div>

                    {{-- Description --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Description</span>
                        </label>
                        <textarea
                            name="description"
                            placeholder="Décrivez votre événement..."
                            class="textarea textarea-bordered h-24 @error('description') textarea-error @enderror"
                            rows="4">{{ old('description') }}</textarea>
                        @error('description')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    {{-- Date de l'événement --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Date de l'événement <span class="text-error">*</span></span>
                        </label>
                        <input
                            type="date"
                            name="event_date"
                            class="input input-bordered @error('event_date') input-error @enderror"
                            value="{{ old('event_date', now()->addMonth()->format('Y-m-d')) }}"
                            required>
                        @error('event_date')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                    </div>

                    {{-- Logo --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Logo de l'événement</span>
                        </label>
                        <input
                            type="file"
                            name="logo"
                            class="file-input file-input-bordered w-full @error('logo') file-input-error @enderror"
                            accept="image/*">
                        @error('logo')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                        @enderror
                        <label class="label">
                            <span class="label-text-alt">Format recommandé : PNG ou JPG, max 2 MB</span>
                        </label>
                    </div>

                    {{-- Événement actif --}}
                    <div class="form-control mb-6">
                        <label class="label cursor-pointer justify-start gap-4">
                            <input
                                type="checkbox"
                                name="is_active"
                                class="toggle toggle-success"
                                {{ old('is_active', true) ? 'checked' : '' }}>
                            <div>
                                <span class="label-text font-semibold">Événement actif</span>
                                <br>
                                <span class="label-text-alt text-base-content/70">
                                Si activé, l'événement sera visible sur la page publique
                            </span>
                            </div>
                        </label>
                    </div>

                    {{-- Info box --}}
                    <div class="alert alert-info mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="font-bold">Après la création</h3>
                            <div class="text-sm">
                                Vous pourrez ajouter des catégories de courses et configurer le formulaire d'inscription.
                            </div>
                        </div>
                    </div>

                    {{-- Boutons --}}
                    <div class="flex gap-4 justify-end">
                        <a href="{{ route('admin.events.index') }}" class="btn btn-ghost">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Créer l'événement
                        </button>
                    </div>

                </form>

            </div>
        </div>

        {{-- Guide rapide --}}
        <div class="mt-8">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        Comment ça marche ?
                    </h3>

                    <div class="steps steps-vertical lg:steps-horizontal mt-4">
                        <div class="step step-primary">
                            <div class="text-left">
                                <h4 class="font-bold">1. Créer l'événement</h4>
                                <p class="text-sm text-base-content/70">Nom, date et logo</p>
                            </div>
                        </div>
                        <div class="step step-primary">
                            <div class="text-left">
                                <h4 class="font-bold">2. Ajouter les catégories</h4>
                                <p class="text-sm text-base-content/70">10km, Marathon, Ultra...</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="text-left">
                                <h4 class="font-bold">3. Partager le lien</h4>
                                <p class="text-sm text-base-content/70">Les participants s'inscrivent</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="text-left">
                                <h4 class="font-bold">4. Gérer les dossards</h4>
                                <p class="text-sm text-base-content/70">Le jour de la course</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
