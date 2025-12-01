@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-5xl">

        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold">Import/Export Excel</h1>
                <p class="text-base-content/70">{{ $event->name }}</p>
            </div>
            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-ghost">
                ← Retour à l'événement
            </a>
        </div>

        {{-- Messages de résultat --}}
        @if(session('import_results'))
            @php $results = session('import_results'); @endphp

            {{-- Catégories créées --}}
            @if(!empty($results['categories_created']))
                <div class="alert alert-info shadow-lg mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">✨ {{ count($results['categories_created']) }} catégorie(s) créée(s) automatiquement :</h3>
                        <ul class="text-sm mt-2 list-disc list-inside">
                            @foreach($results['categories_created'] as $categoryName)
                                <li>{{ $categoryName }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if($results['errors'] > 0)
                <div class="alert alert-warning shadow-lg mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">⚠ Erreurs rencontrées: {{ $results['errors'] }}</h3>
                        <ul class="text-sm mt-2 list-disc list-inside">
                            @foreach($results['error_messages'] as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        @endif

        {{-- Statistiques --}}
        <div class="stats shadow w-full mb-6 bg-base-100">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="stat-title">Total participants</div>
                <div class="stat-value text-primary">{{ $event->registrations->count() }}</div>
                <div class="stat-desc">dans la base de données</div>
            </div>

            <div class="stat">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="stat-title">Catégories</div>
                <div class="stat-value text-secondary">{{ $event->categories->count() }}</div>
                <div class="stat-desc">courses disponibles</div>
            </div>
        </div>

        {{-- Info format Excel --}}
        <div class="alert alert-info shadow-lg mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="font-bold">Format Excel requis :</h3>
                <ul class="text-sm mt-2">
                    <li>• <strong>Colonne A:</strong> ID (ignoré à l'import)</li>
                    <li>• <strong>Colonne B:</strong> Nom</li>
                    <li>• <strong>Colonne C:</strong> Prénom</li>
                    <li>• <strong>Colonne D:</strong> Sexe (M/F/X)</li>
                    <li>• <strong>Colonne E:</strong> Épreuve (doit correspondre exactement au nom de catégorie)</li>
                    <li>• <strong>Colonne F:</strong> Date de naissance (JJ-MM-AAAA)</li>
                    <li>• <strong>Colonne G:</strong> Nationalité (code 3 lettres: BEL, FRA, etc.)</li>
                    <li>• <strong>Colonne H:</strong> Club (optionnel)</li>
                    <li>• <strong>Colonne I:</strong> Dossard (optionnel)</li>
                    <li>• <strong>Colonne J:</strong> Acquité (0 ou 1)</li>
                </ul>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Export --}}
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-info" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exporter Excel
                    </h2>
                    <p class="text-sm text-base-content/70">
                        Téléchargez tous les participants au format Excel (.xlsx)
                    </p>

                    <div class="card-actions justify-end mt-4">
                        @if($event->registrations->count() > 0)
                            <a href="{{ route('admin.events.export-excel', $event) }}" class="btn btn-info w-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                📥 Télécharger Excel
                            </a>
                        @else
                            <button class="btn btn-disabled w-full" disabled>
                                Aucun participant à exporter
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Import --}}
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Importer Excel
                    </h2>
                    <p class="text-sm text-base-content/70">
                        Importez des participants depuis un fichier Excel
                    </p>

                    <form action="{{ route('admin.events.import-excel', $event) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf

                        <div class="form-control">
                            <input
                                type="file"
                                name="excel_file"
                                class="file-input file-input-bordered file-input-success w-full"
                                accept=".xlsx,.xls"
                                required>
                            @error('excel_file')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success w-full mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            📤 Importer les données
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- Zone de danger --}}
        <div class="card bg-base-100 shadow-xl mt-6">
            <div class="card-body">
                <div class="alert alert-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">⚠ Zone de danger</h3>
                        <p class="text-sm">Cette action supprimera TOUTES les inscriptions de cet événement de manière irréversible.</p>

                        <form action="{{ route('admin.events.truncate-registrations', $event) }}" method="POST" class="mt-4" onsubmit="return confirm('⚠️ ATTENTION !\n\nÊtes-vous ABSOLUMENT SÛR de vouloir supprimer TOUTES les inscriptions ?\n\nCette action est IRRÉVERSIBLE !')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                🗑️ Vider toutes les inscriptions
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
