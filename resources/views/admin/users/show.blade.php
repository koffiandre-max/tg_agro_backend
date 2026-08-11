@extends('layouts.app')

@section('title', $user->name ?? 'Utilisateur')

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32($user->name ?? 'U')) % count($colors)];

    $roleLabels = [
        'admin' => 'Administrateur',
        'client' => 'Client',
        'technician' => 'Technicien',
    ];
    $roleLabel = $roleLabels[$user->role] ?? ucfirst($user->role ?? 'Utilisateur');
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <a href="{{ route('admin.users.index') }}" class="group inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 mb-2 transition-colors duration-150">
                <svg class="w-4 h-4 mr-1.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour aux utilisateurs
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">
                Profil de <span class="text-indigo-600">{{ $user->name ?? 'Utilisateur' }}</span>
            </h1>
            <p class="mt-1.5 text-sm text-slate-500 flex items-center gap-1.5">
                <span class="inline-flex h-2 w-2 rounded-full {{ $user->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                {{ $roleLabel }} · {{ $user->is_active ? 'Compte actif' : 'Compte inactif' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <x-ui.btn variant="secondary" href="{{ route('admin.users.edit', $user->id) }}" icon="edit">
                Modifier
            </x-ui.btn>
            <x-ui.btn variant="ghost" href="{{ route('admin.users.index') }}" icon="arrow-left">
                Retour
            </x-ui.btn>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Colonne profil --}}
        <div class="lg:col-span-1 space-y-6">
            <x-ui.card>
                <div class="text-center">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name ?? 'Utilisateur' }}" class="w-32 h-32 rounded-full mx-auto object-cover">
                    @else
                        <div class="w-32 h-32 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold" style="background: {{ $profileColor }}">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $user->name ?? 'Utilisateur' }}</h2>
                    <div class="flex items-center justify-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600/20">
                            {{ $roleLabel }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                    @if($user->id)
                        <p class="mt-2 text-xs text-gray-500">ID: <span class="font-mono font-semibold text-gray-700">#{{ $user->id }}</span></p>
                    @endif
                </div>

                <div class="mt-6 space-y-3 border-t border-slate-100 pt-6">
                    @if($user->email)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <span class="truncate">{{ $user->email }}</span>
                        </div>
                    @endif
                    @if($user->phone)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 003 4.5v2.25z" />
                            </svg>
                            {{ $user->phone }}
                        </div>
                    @endif
                    @if($user->created_at)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Inscrit le {{ $user->created_at->format('d/m/Y') }}
                        </div>
                    @endif
                </div>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Actions</h3>
                <div class="space-y-2">
                    <x-ui.btn variant="secondary" href="{{ route('admin.users.edit', $user->id) }}" icon="edit" class="w-full">
                        Modifier l'utilisateur
                    </x-ui.btn>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de supprimer cet utilisateur ?')">
                        @csrf
                        @method('DELETE')
                        <x-ui.btn type="submit" variant="danger" icon="trash" class="w-full">
                            Supprimer
                        </x-ui.btn>
                    </form>
                </div>
            </x-ui.card>
        </div>

        {{-- Colonne détails --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Métriques --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <x-ui.card class="flex items-center gap-4">
                    <div class="p-3.5 bg-indigo-50 text-indigo-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.25h15.002c.966 0 1.75-.784 1.75-1.75V18a5.25 5.25 0 00-10.5 0v.75c0 .966.784 1.75 1.75 1.75z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Rôle</span>
                        <span class="text-lg font-extrabold text-gray-900 mt-0.5 inline-block capitalize">{{ $user->role ?? '—' }}</span>
                    </div>
                </x-ui.card>

                @if($user->role === 'technician')
                    <x-ui.card class="flex items-center gap-4">
                        <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-2xl shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-4.42-4.41a1 1 0 011.41-1.41h0l3.29 3.29 8.59-8.59a1 1 0 011.41 1.41l-10 10z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Type de technicien</span>
                            <span class="text-lg font-extrabold text-gray-900 mt-0.5 inline-block capitalize">{{ $user->type_technicien?->label() ?? '—' }}</span>
                        </div>
                    </x-ui.card>
                @else
                    <x-ui.card class="flex items-center gap-4">
                        <div class="p-3.5 bg-purple-50 text-purple-600 rounded-2xl shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Dernière mise à jour</span>
                            <span class="text-base font-extrabold text-gray-900 mt-1 inline-block">{{ $user->updated_at?->format('d/m/Y H:i') ?? '—' }}</span>
                        </div>
                    </x-ui.card>
                @endif
            </div>

            {{-- Détails du compte --}}
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du compte</h3>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-xl bg-slate-50 p-3.5">
                        <dt class="text-xs font-medium text-gray-500">Nom complet</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $user->name ?? '—' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3.5">
                        <dt class="text-xs font-medium text-gray-500">Adresse e-mail</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 truncate">{{ $user->email ?? '—' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3.5">
                        <dt class="text-xs font-medium text-gray-500">Téléphone</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $user->phone ?? '—' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3.5">
                        <dt class="text-xs font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $user->is_active ? 'Actif' : 'Inactif' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3.5">
                        <dt class="text-xs font-medium text-gray-500">Date d'inscription</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $user->created_at?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3.5">
                        <dt class="text-xs font-medium text-gray-500">E-mail vérifié</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $user->email_verified_at ? $user->email_verified_at->format('d/m/Y') : 'Non vérifié' }}</dd>
                    </div>
                </dl>
            </x-ui.card>

            {{-- Section spécifique au client --}}
            @if($user->role === 'client' && $user->client)
                @php $client = $user->client; @endphp
                <x-ui.card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Espace Client</h3>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-3.5">
                            <dt class="text-xs font-medium text-gray-500">Code client</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 font-mono">{{ $client->code ?? '—' }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3.5">
                            <dt class="text-xs font-medium text-gray-500">Abonnement</dt>
                            <dd class="mt-1 text-sm font-semibold capitalize text-gray-900">{{ $client->subscription_type ?? '—' }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3.5">
                            <dt class="text-xs font-medium text-gray-500">Ville de résidence</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $client->city_of_residence ?? '—' }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3.5">
                            <dt class="text-xs font-medium text-gray-500">Pays</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $client->country_of_residence ?? '—' }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3.5">
                            <dt class="text-xs font-medium text-gray-500">Investissement total</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $client->total_investment ? number_format($client->total_investment, 2) . ' €' : '0.00 €' }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3.5">
                            <dt class="text-xs font-medium text-gray-500">Fin d'abonnement</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $client->subscription_expires_at?->format('d/m/Y') ?? '—' }}</dd>
                        </div>
                    </dl>

                    @if($client->farms->count() > 0)
                        <div class="mt-5 border-t border-slate-100 pt-5">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Exploitations ({{ $client->farms->count() }})</p>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach($client->farms as $farm)
                                    <div class="flex items-center justify-between rounded-lg border border-gray-200 p-3">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $farm->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $farm->location ?? '—' }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $farm->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($farm->status === 'fallow' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($farm->status ?? '—') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-ui.card>
            @endif

            {{-- Section spécifique au technicien --}}
            @if($user->role === 'technician' && $user->technician)
                @php $technician = $user->technician; @endphp
                <x-ui.card>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Espace Technicien</h3>
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-xl bg-slate-50 p-3.5">
                            <dt class="text-xs font-medium text-gray-500">Spécialité</dt>
                            <dd class="mt-1 text-sm font-semibold capitalize text-gray-900">{{ $user->type_technicien?->label() ?? '—' }}</dd>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3.5">
                            <dt class="text-xs font-medium text-gray-500">Téléphone</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $technician->phone_secondary ?? $user->phone ?? '—' }}</dd>
                        </div>
                        @if($technician->zone_intervention)
                            <div class="rounded-xl bg-slate-50 p-3.5">
                                <dt class="text-xs font-medium text-gray-500">Zone d'intervention</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $technician->zone_intervention }}</dd>
                            </div>
                        @endif
                    </dl>
                </x-ui.card>
            @endif
        </div>
    </div>
</div>
@endsection
