@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold">{{ $event->name }}</h1>
                <p class="text-base-content/70">Configuration de l'événement</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-ghost">
                ← Retour
            </a>
        </div>

        {{-- Tabs --}}
        <div class="tabs tabs-boxed mb-6">
            <a class="tab tab-active" data-tab="info">Informations</a>
            <a class="tab" data-tab="categories">Catégories</a>
            <a class="tab" data-tab="registrations">Inscriptions</a>
            <a class="tab" data-tab="export">Export</a>
        </div>

        {{-- Tab: Informations --}}
        <div id="tab-info" class="tab-content">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title mb-4">Informations de l'événement</h2>

                    <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Nom de l'événement</span>
                            </label>
                            <input
                                type="text"
                                name="name"
                                value="{{ $event->name }}"
                                class="input input-bordered"
                                required>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Description</span>
                            </label>
                            <textarea
                                name="description"
                                class="textarea textarea-bordered"
                                rows="3">{{ $event->description }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Date de l'événement</span>
                                </label>
                                <input
                                    type="date"
                                    name="event_date"
                                    value="{{ $event->event_date->format('Y-m-d') }}"
                                    class="input input-bordered"
                                    required>
                            </div>

                            <div class="form-control">
                                <label class="label cursor-pointer">
                                    <span class="label-text">Événement actif</span>
                                    <input
                                        type="checkbox"
                                        name="is_active"
                                        class="toggle toggle-success"
                                        {{ $event->is_active ? 'checked' : '' }}>
                                </label>
                            </div>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Logo</span>
                            </label>
                            <input
                                type="file"
                                name="logo"
                                class="file-input file-input-bordered"
                                accept="image/*">
                            @if($event->logo)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($event->logo) }}" class="h-20" alt="Logo actuel">
                                </div>
                            @endif
                        </div>

                        <div class="flex gap-4 justify-end">
                            <button type="submit" class="btn btn-primary">
                                Sauvegarder
                            </button>
                        </div>
                    </form>

                    <div class="divider"></div>

                    <div class="alert alert-warning">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h3 class="font-bold">Zone de danger</h3>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Toutes les inscriptions seront perdues.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-error btn-sm mt-2">
                                    Supprimer l'événement
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab: Catégories --}}
        <div id="tab-categories" class="tab-content hidden">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title mb-4">Catégories de courses</h2>

                    {{-- Liste des catégories --}}
                    <div class="overflow-x-auto mb-6">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Code</th>
                                <th>Prix</th>
                                <th>Max participants</th>
                                <th>Inscriptions</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($event->categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td><code>{{ $category->code }}</code></td>
                                    <td>{{ $category->price ? number_format($category->price, 2) . '€' : '-' }}</td>
                                    <td>{{ $category->max_participants ?? 'Illimité' }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $category->registrations->count() }}</span>
                                    </td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge badge-success">Actif</span>
                                        @else
                                            <span class="badge badge-ghost">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.events.categories.destroy', [$event, $category]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-error btn-xs" onclick="return confirm('Supprimer cette catégorie ?')">
                                                Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Aucune catégorie créée</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="divider">Ajouter une catégorie</div>

                    {{-- Formulaire d'ajout --}}
                    <form action="{{ route('admin.events.categories.store', $event) }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Nom de la catégorie</span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    placeholder="Ex: Ultra 3000"
                                    class="input input-bordered"
                                    required>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Code unique</span>
                                </label>
                                <input
                                    type="text"
                                    name="code"
                                    placeholder="Ex: ultra_3000"
                                    class="input input-bordered"
                                    required>
                                <label class="label">
                                    <span class="label-text-alt">Uniquement lettres, chiffres et underscores</span>
                                </label>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Prix (€)</span>
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="price"
                                    placeholder="25.00"
                                    class="input input-bordered">
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Participants maximum</span>
                                </label>
                                <input
                                    type="number"
                                    name="max_participants"
                                    placeholder="Laisser vide = illimité"
                                    class="input input-bordered">
                            </div>
                        </div>

                        <div class="flex justify-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Ajouter la catégorie
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tab: Inscriptions --}}
        <div id="tab-registrations" class="tab-content hidden">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title mb-4">Liste des inscriptions ({{ $event->registrations->count() }})</h2>

                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                            <tr>
                                <th>Dossard</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Catégorie</th>
                                <th>Acquité</th>
                                <th>Inscrit le</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($event->registrations as $registration)
                                <tr>
                                    <td>
                                        @if($registration->bib_number)
                                            <span class="badge badge-primary">{{ $registration->bib_number }}</span>
                                        @else
                                            <span class="badge badge-ghost">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $registration->nom }}</td>
                                    <td>{{ $registration->prenom }}</td>
                                    <td>{{ $registration->category->name }}</td>
                                    <td>
                                        @if($registration->is_paid)
                                            <span class="badge badge-success">✓</span>
                                        @else
                                            <span class="badge badge-error">✗</span>
                                        @endif
                                    </td>
                                    <td>{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Aucune inscription</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab: Export --}}
        <div id="tab-export" class="tab-content hidden">
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title mb-4">Export des données</h2>

                    <div class="alert alert-info mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Export au format Excel (.xlsx) de toutes les inscriptions</span>
                    </div>

                    <a href="{{ route('admin.events.export', $event) }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Télécharger Excel
                    </a>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Gestion des tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();

                // Retirer active de tous les tabs
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('tab-active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));

                // Activer le tab cliqué
                this.classList.add('tab-active');
                const tabName = this.dataset.tab;
                document.getElementById(`tab-${tabName}`).classList.remove('hidden');
            });
        });
    </script>

@endsection
