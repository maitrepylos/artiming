@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Gestion des événements</h1>
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nouvel événement
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <div class="flex justify-between items-start">
                            <h2 class="card-title">{{ $event->name }}</h2>
                            @if($event->is_active)
                                <div class="badge badge-success">Actif</div>
                            @else
                                <div class="badge badge-ghost">Inactif</div>
                            @endif
                        </div>

                        <p class="text-sm text-base-content/70">
                            {{ $event->event_date->format('d/m/Y') }}
                        </p>

                        <div class="stats stats-vertical bg-base-200 shadow mt-4">
                            <div class="stat py-2">
                                <div class="stat-title text-xs">Inscriptions</div>
                                <div class="stat-value text-2xl">{{ $event->registrations->count() }}</div>
                            </div>
                            <div class="stat py-2">
                                <div class="stat-title text-xs">Catégories</div>
                                <div class="stat-value text-2xl">{{ $event->categories->count() }}</div>
                            </div>
                        </div>

                        <div class="card-actions justify-end mt-4">
                            <div class="join">
                                <a href="{{ $event->public_url }}" target="_blank" class="btn btn-sm btn-ghost join-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.events.bibs', $event) }}" class="btn btn-sm btn-primary join-item">
                                    Dossards
                                </a>
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-secondary join-item">
                                    Éditer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="alert alert-info">
                        <span>Aucun événement créé. Commencez par créer votre premier événement !</span>
                    </div>
                </div>
            @endforelse
        </div>

    </div>
@endsection
