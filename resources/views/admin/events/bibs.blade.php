@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold">Gestion des dossards</h1>
                <p class="text-base-content/70">{{ $event->name }} - {{ $event->event_date->format('d/m/Y') }}</p>
            </div>
            <a href="{{ route('admin.events.index') }}" class="btn btn-ghost">
                ← Retour aux événements
            </a>
        </div>

        {{-- Messages --}}
        <div id="messages" class="mb-6"></div>

        {{-- Statistiques rapides --}}
        <div class="stats shadow mb-6 w-full">
            <div class="stat">
                <div class="stat-title">Total inscriptions</div>
                <div class="stat-value">{{ $registrations->count() }}</div>
            </div>
            <div class="stat">
                <div class="stat-title">Dossards attribués</div>
                <div class="stat-value text-success">{{ $registrations->whereNotNull('bib_number')->count() }}</div>
            </div>
            <div class="stat">
                <div class="stat-title">Paiements reçus</div>
                <div class="stat-value text-primary">{{ $registrations->where('is_paid', true)->count() }}</div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">

                {{-- Recherche de participant --}}
                <div class="form-control mb-6">
                    <label class="label">
                        <span class="label-text font-semibold">Rechercher un participant</span>
                    </label>
                    <input
                        type="text"
                        id="search_participant"
                        placeholder="Tapez le nom, prénom ou numéro de dossard..."
                        class="input input-bordered input-lg"
                        autocomplete="off">

                    <div id="search-results" class="mt-2"></div>
                </div>

                {{-- Info participant sélectionné --}}
                <div id="participant-info" class="alert alert-info mb-6 hidden">
                    <div id="participant-details"></div>
                </div>

                {{-- Formulaire de mise à jour --}}
                {{-- Remplacez TOUT le formulaire par ceci --}}
                <form id="update-form" onsubmit="handleSubmit(event)">
                    @csrf

                    <input type="hidden" id="registration_id" name="registration_id">

                    {{-- Tous vos champs restent identiques --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        {{-- Numéro de dossard --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Numéro de dossard <span class="text-error">*</span></span>
                            </label>
                            <input
                                type="number"
                                id="bib_number"
                                name="bib_number"
                                placeholder="Ex: 123"
                                class="input input-bordered input-error"
                                required>
                        </div>

                        {{-- Nom --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Nom <span class="text-error">*</span></span>
                            </label>
                            <input
                                type="text"
                                id="nom"
                                name="nom"
                                class="input input-bordered"
                                required>
                        </div>

                        {{-- Prénom --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Prénom <span class="text-error">*</span></span>
                            </label>
                            <input
                                type="text"
                                id="prenom"
                                name="prenom"
                                class="input input-bordered"
                                required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        {{-- Genre --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Genre</span>
                            </label>
                            <div class="flex gap-2">
                                <label class="label cursor-pointer gap-1">
                                    <input type="radio" id="sexe_m" name="sexe" value="M" class="radio radio-sm">
                                    <span class="label-text text-xs">H</span>
                                </label>
                                <label class="label cursor-pointer gap-1">
                                    <input type="radio" id="sexe_f" name="sexe" value="F" class="radio radio-sm">
                                    <span class="label-text text-xs">F</span>
                                </label>
                                <label class="label cursor-pointer gap-1">
                                    <input type="radio" id="sexe_x" name="sexe" value="X" class="radio radio-sm">
                                    <span class="label-text text-xs">X</span>
                                </label>
                            </div>
                        </div>

                        {{-- Date de naissance --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Date de naissance</span>
                            </label>
                            <input
                                type="date"
                                id="date_naissance"
                                name="date_naissance"
                                class="input input-bordered input-sm">
                        </div>

                        {{-- Catégorie --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Course</span>
                            </label>
                            <select id="category_id" name="category_id" class="select select-bordered select-sm">
                                @foreach($event->categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Club --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Club</span>
                            </label>
                            <input
                                type="text"
                                id="club"
                                name="club"
                                class="input input-bordered input-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        {{-- Nationalité --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Nationalité</span>
                            </label>
                            <select id="nationalite" name="nationalite" class="select select-bordered select-sm">
                                <option value="BEL" selected>🇧🇪 Belgique</option>
                                <option value="FRA">🇫🇷 France</option>
                                <option value="NLD">🇳🇱 Pays-Bas</option>
                            </select>
                        </div>

                        {{-- Code UCI --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Code UCI</span>
                            </label>
                            <input
                                type="text"
                                id="code_uci"
                                name="code_uci"
                                class="input input-bordered input-sm">
                        </div>

                        {{-- Paiement --}}
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <span class="label-text">Paiement reçu</span>
                                <input
                                    type="checkbox"
                                    id="is_paid"
                                    name="is_paid"
                                    class="toggle toggle-success">
                            </label>
                        </div>
                    </div>

                    {{-- Boutons --}}
                    <div class="flex gap-4 justify-end">
                        <button type="button" class="btn btn-ghost" onclick="resetForm()">
                            Réinitialiser
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let searchTimeout = null;
        let currentRegistrationId = null;

        // Recherche avec Fetch API
        document.getElementById('search_participant').addEventListener('input', function(e) {
            const term = e.target.value;
            const resultsDiv = document.getElementById('search-results');

            clearTimeout(searchTimeout);

            if (term.length < 2) {
                resultsDiv.innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch("{{ route('admin.events.search', $event->id) }}?search=" + encodeURIComponent(term))
                    .then(response => response.text())
                    .then(html => {
                        resultsDiv.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                    });
            }, 300);
        });

        function selectParticipant(data) {
            currentRegistrationId = data.id;

            document.getElementById('registration_id').value = data.id;
            document.getElementById('nom').value = data.nom;
            document.getElementById('prenom').value = data.prenom;
            document.getElementById('date_naissance').value = data.date_naissance;
            document.getElementById('category_id').value = data.category_id;
            document.getElementById('club').value = data.club || '';
            document.getElementById('nationalite').value = data.nationalite || 'BEL';
            document.getElementById('code_uci').value = data.code_uci || '';
            document.getElementById('bib_number').value = data.bib_number || '';
            document.getElementById('is_paid').checked = data.is_paid;

            document.getElementById('sexe_' + data.sexe.toLowerCase()).checked = true;

            document.getElementById('participant-info').classList.remove('hidden');
            document.getElementById('participant-details').innerHTML = `
            <div>
                <strong class="text-lg">${data.nom} ${data.prenom}</strong><br>
                <span class="text-sm">Course: ${data.category_name}</span><br>
                ${data.bib_number ? `<span class="badge badge-primary">Dossard: ${data.bib_number}</span>` : '<span class="badge badge-ghost">Pas de dossard</span>'}
                ${data.is_paid ? '<span class="badge badge-success ml-2">✓ Acquité</span>' : '<span class="badge badge-error ml-2">✗ Non acquité</span>'}
            </div>
        `;

            document.getElementById('submit-btn').disabled = false;

            // Vider la recherche
            document.getElementById('search_participant').value = '';
            document.getElementById('search-results').innerHTML = '';

            document.getElementById('bib_number').focus();
        }

        function handleSubmit(event) {
            event.preventDefault();

            if (!currentRegistrationId) {
                alert('Veuillez sélectionner un participant');
                return;
            }

            const form = document.getElementById('update-form');
            const formData = new FormData(form);
            const messagesDiv = document.getElementById('messages');

            // Désactiver le bouton
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading loading-spinner"></span> Mise à jour...';

            fetch("{{ url('admin/events/' . $event->id . '/registrations') }}/" + currentRegistrationId, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    nom: formData.get('nom'),
                    prenom: formData.get('prenom'),
                    sexe: formData.get('sexe'),
                    date_naissance: formData.get('date_naissance'),
                    category_id: formData.get('category_id'),
                    club: formData.get('club'),
                    nationalite: formData.get('nationalite'),
                    code_uci: formData.get('code_uci'),
                    bib_number: formData.get('bib_number'),
                    is_paid: document.getElementById('is_paid').checked ? 1 : 0
                })
            })
                .then(response => {
                    if (!response.ok) throw new Error('Erreur de mise à jour');
                    return response.text();
                })
                .then(html => {
                    messagesDiv.innerHTML = html;

                    // Réactiver le bouton
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Mettre à jour
            `;

                    // Vider après succès
                    setTimeout(() => {
                        messagesDiv.innerHTML = '';
                        resetForm();
                    }, 2000);
                })
                .catch(error => {
                    messagesDiv.innerHTML = `<div class="alert alert-error"><span>Erreur: ${error.message}</span></div>`;
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Mettre à jour';
                });
        }

        function resetForm() {
            currentRegistrationId = null;
            document.getElementById('update-form').reset();
            document.getElementById('participant-info').classList.add('hidden');
            document.getElementById('submit-btn').disabled = true;

            // NE PAS toucher au champ de recherche pour garder l'événement actif
            // Juste vider visuellement
            const searchInput = document.getElementById('search_participant');
            const searchResults = document.getElementById('search-results');

            // Enlever l'événement focus qui pourrait bloquer
            searchInput.blur();

            // Vider proprement
            searchInput.value = '';
            searchResults.innerHTML = '';
        }
    </script>

@endsection
