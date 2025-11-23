<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FormArthur') }} - Admin - @yield('title', 'Dashboard')</title>

    {{-- Tailwind + DaisyUI --}}
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.4.19/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>

    {{-- HTMX --}}
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>

    <style>
        /* Animations personnalisées */
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }

        .sidebar-open {
            animation: slideIn 0.3s ease-out;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-base-200">

{{-- Navbar --}}
<div class="navbar bg-base-100 shadow-lg">
    <div class="flex-1">
        <button id="sidebar-toggle" class="btn btn-ghost btn-square lg:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost text-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            FormArthur Admin
        </a>
    </div>

    <div class="flex-none">
        {{-- Bouton retour vers le site public --}}
        <a href="{{ route('home') }}" class="btn btn-ghost btn-sm" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            Voir le site
        </a>

        {{-- Menu utilisateur --}}
        <div class="dropdown dropdown-end">
            <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                <div class="w-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                    <span class="text-xl">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
            </label>
            <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                <li class="menu-title">
                    <span>{{ Auth::user()->name }}</span>
                </li>
                <li><a href="{{ route('admin.events.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Événements
                    </a></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Déconnexion
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="drawer lg:drawer-open">
    <input id="sidebar-drawer" type="checkbox" class="drawer-toggle" />

    {{-- Contenu principal --}}
    <div class="drawer-content">
        {{-- Messages flash --}}
        @if(session('success'))
            <div class="alert alert-success shadow-lg mx-4 mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error shadow-lg mx-4 mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error shadow-lg mx-4 mt-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="font-bold">Erreurs :</h3>
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Contenu de la page --}}
        @yield('content')
    </div>

    {{-- Sidebar --}}
    <div class="drawer-side">
        <label for="sidebar-drawer" class="drawer-overlay"></label>
        <aside class="bg-base-100 w-64 min-h-screen">
            <div class="sticky top-0">
                {{-- Logo/Titre dans la sidebar --}}
                <div class="p-4 bg-primary text-primary-content">
                    <h2 class="text-xl font-bold flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                        Navigation
                    </h2>
                </div>

                {{-- Menu --}}
                <ul class="menu p-4 space-y-2">
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.events.index') }}" class="{{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Événements
                        </a>
                    </li>

                    <div class="divider"></div>

                    <li class="menu-title">
                        <span>Raccourcis</span>
                    </li>

                    <li>
                        <a href="{{ route('admin.events.create') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nouvel événement
                        </a>
                    </li>
                </ul>

                {{-- Stats rapides --}}
                <div class="p-4">
                    <div class="stats stats-vertical shadow w-full">
                        <div class="stat">
                            <div class="stat-title text-xs">Événements actifs</div>
                            <div class="stat-value text-primary text-2xl">
                                {{ \App\Models\Event::where('is_active', true)->count() }}
                            </div>
                        </div>
                        <div class="stat">
                            <div class="stat-title text-xs">Total inscriptions</div>
                            <div class="stat-value text-secondary text-2xl">
                                {{ \App\Models\Registration::count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
    // Toggle sidebar sur mobile
    document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
        document.getElementById('sidebar-drawer').checked = !document.getElementById('sidebar-drawer').checked;
    });

    // Auto-fermer les alertes après 5 secondes
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>

@stack('scripts')
</body>
</html>

{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="card w-full max-w-md bg-base-100 shadow-2xl">
            <div class="card-body">
                <h2 class="card-title text-3xl justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Connexion Admin
                </h2>

                @if($errors->any())
                    <div class="alert alert-error mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Email</span>
                        </label>
                        <input
                            type="email"
                            name="email"
                            placeholder="admin@formarthur.com"
                            class="input input-bordered"
                            value="{{ old('email') }}"
                            required
                            autofocus>
                    </div>

                    <div class="form-control mb-6">
                        <label class="label">
                            <span class="label-text">Mot de passe</span>
                        </label>
                        <input
                            type="password"
                            name="password"
                            placeholder="••••••••"
                            class="input input-bordered"
                            required>
                    </div>

                    <div class="form-control mb-6">
                        <label class="label cursor-pointer justify-start gap-2">
                            <input type="checkbox" name="remember" class="checkbox checkbox-primary checkbox-sm">
                            <span class="label-text">Se souvenir de moi</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Se connecter
                    </button>
                </form>

                <div class="divider">OU</div>

                <a href="{{ route('home') }}" class="btn btn-ghost w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour au site
                </a>
            </div>
        </div>
    </div>
@endsection
