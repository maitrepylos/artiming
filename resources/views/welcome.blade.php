<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FormArthur - Inscriptions événements sportifs</title>

    {{-- Tailwind + DaisyUI --}}
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-primary/10 to-secondary/10 min-h-screen">

{{-- Navbar --}}
<div class="navbar bg-base-100/80 backdrop-blur-sm sticky top-0 z-50 shadow-lg">
    <div class="flex-1">
        <a href="{{ route('home') }}" class="btn btn-ghost text-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            FormArthur
        </a>
    </div>
    <div class="flex-none">
        @auth
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm">
                Admin
            </a>
        @else
            <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">
                Connexion
            </a>
        @endauth
    </div>
</div>

<div class="container mx-auto px-4 py-12 max-w-7xl">

    {{-- Hero Section --}}
    <div class="text-center mb-12">
        <h1 class="text-5xl font-bold mb-4 bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
            Inscriptions aux événements sportifs
        </h1>
        <p class="text-xl text-base-content/70">
            Choisissez votre événement et inscrivez-vous en quelques clics
        </p>
    </div>

    {{-- Stats rapides --}}
    @if($events->count() > 0)
        <div class="stats shadow w-full mb-12 bg-base-100">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="stat-title">Événements actifs</div>
                <div class="stat-value text-primary">{{ $events->count() }}</div>
            </div>

            <div class="stat">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="stat-title">Participants inscrits</div>
                <div class="stat-value text-secondary">{{ \App\Models\Registration::count() }}</div>
            </div>

            <div class="stat">
                <div class="stat-figure text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="stat-title">Courses disponibles</div>
                <div class="stat-value text-accent">{{ \App\Models\Category::count() }}</div>
            </div>
        </div>
    @endif

    {{-- Liste des événements --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($events as $event)
            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                @if($event->logo)
                    <figure class="px-10 pt-10">
                        <img src="{{ Storage::url($event->logo) }}" alt="{{ $event->name }}" class="rounded-xl h-32 object-contain">
                    </figure>
                @else
                    <figure class="px-10 pt-10">
                        <div class="w-32 h-32 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </figure>
                @endif

                <div class="card-body">
                    <h2 class="card-title">
                        {{ $event->name }}
                        @if($event->event_date->isFuture())
                            <div class="badge badge-success">À venir</div>
                        @else
                            <div class="badge badge-ghost">Passé</div>
                        @endif
                    </h2>

                    <p class="text-base-content/70">{{ Str::limit($event->description, 100) }}</p>

                    {{-- Date et infos --}}
                    <div class="flex items-center gap-2 text-sm text-base-content/70 mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $event->event_date->format('d/m/Y') }}
                    </div>

                    {{-- Stats événement --}}
                    <div class="flex gap-4 mt-4">
                        <div class="stat bg-base-200 rounded-lg p-3">
                            <div class="stat-title text-xs">Catégories</div>
                            <div class="stat-value text-sm">{{ $event->categories->count() }}</div>
                        </div>
                        <div class="stat bg-base-200 rounded-lg p-3">
                            <div class="stat-title text-xs">Inscrits</div>
                            <div class="stat-value text-sm">{{ $event->registrations->count() }}</div>
                        </div>
                    </div>

                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('event.register', $event->slug) }}" class="btn btn-primary w-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            S'inscrire
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body text-center py-12">
                        <div class="flex justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">Aucun événement disponible</h3>
                        <p class="text-base-content/70 mb-6">
                            Aucun événement n'est actuellement ouvert aux inscriptions.
                            <br>
                            Revenez bientôt pour découvrir nos prochains événements !
                        </p>

                        @auth
                            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                                Créer un événement
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- CTA Section --}}
    @if($events->count() > 0)
        <div class="card bg-gradient-to-r from-primary to-secondary text-primary-content shadow-2xl mt-12">
            <div class="card-body text-center">
                <h2 class="card-title text-3xl justify-center mb-4">
                    Prêt à relever le défi ?
                </h2>
                <p class="text-lg mb-6">
                    Inscrivez-vous dès maintenant à l'un de nos événements et rejoignez des centaines de participants passionnés !
                </p>
                <div class="flex gap-4 justify-center flex-wrap">
                    @foreach($events->take(3) as $event)
                        <a href="{{ route('event.register', $event->slug) }}" class="btn btn-accent">
                            {{ $event->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

</div>

{{-- Footer --}}
<footer class="footer footer-center p-10 bg-base-100 text-base-content mt-12">
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        <p class="font-bold">
            FormArthur <br/>
            Gestion d'inscriptions sportives
        </p>
        <p>© {{ date('Y') }} - Tous droits réservés</p>
    </div>
    <div>
        <div class="grid grid-flow-col gap-4">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="link link-hover">Administration</a>
            @else
                <a href="{{ route('login') }}" class="link link-hover">Connexion</a>
            @endauth
        </div>
    </div>
</footer>

</body>
</html>
