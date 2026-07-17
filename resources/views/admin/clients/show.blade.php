@extends('layouts.app')

@section('page-title', 'Détails du Client')

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32($client->user->name ?? 'C')) % count($colors)];

    $subscriptionColors = [
        'basic' => ['label' => 'Basique', 'text' => 'text-slate-700', 'bg' => 'bg-slate-100', 'ring' => 'ring-slate-600/20'],
        'premium' => ['label' => 'Premium', 'text' => 'text-indigo-700', 'bg' => 'bg-indigo-50/80', 'ring' => 'ring-indigo-600/20'],
        'vip' => ['label' => 'VIP', 'text' => 'text-amber-700', 'bg' => 'bg-amber-50/80', 'ring' => 'ring-amber-600/20'],
    ];
    $subscription = $subscriptionColors[$client->subscription_type] ?? ['label' => ucfirst($client->subscription_type ?? '—'), 'text' => 'text-slate-700', 'bg' => 'bg-slate-100', 'ring' => 'ring-slate-600/20'];
@endphp

<x-ui.page-header
    title="Fiche Client"
    subtitle="Profil et suivi de {{ $client->user->name ?? 'Client' }}"
    :breadcrumbs="[['label' => 'Tableau de bord', 'route' => 'dashboard'], ['label' => 'Clients', 'route' => 'admin.clients.index'], ['label' => 'Détails']]"
>
    <x-slot:actions>
        <x-ui.btn variant="secondary" href="{{ route('admin.clients.edit', $client->id) }}" icon="edit">
            Modifier
        </x-ui.btn>
        <x-ui.btn variant="ghost" href="{{ route('admin.clients.index') }}" icon="arrow-left">
            Retour
        </x-ui.btn>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    
    {{-- Colonne principale --}}
    <div class="xl:col-span-2 space-y-6">
        
        {{-- Profile Hero Card --}}
        <x-ui.card class="overflow-hidden !p-0 border border-slate-200 shadow-sm">
            {{-- Bannière abstraite --}}
            <div class="relative h-40 w-full" style="background: linear-gradient(135deg, {{ $profileColor }} 0%, #0f172a 100%)">
                {{-- Badges de statut --}}
                <div class="absolute top-4 right-4 flex items-center gap-2 pointer-events-none">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold backdrop-blur bg-white/90 ring-1 ring-inset {{ $subscription['ring'] }} {{ $subscription['text'] }} {{ $subscription['bg'] }}">
                        {{ $subscription['label'] }}
                    </span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold backdrop-blur shadow-sm" style="{{ $client->user->is_active ?? true ? 'background:rgba(236,253,245,0.95);color:#047857' : 'background:rgba(243,244,246,0.95);color:#4b5563' }}">
                        {{ ($client->user->is_active ?? true) ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
            </div>

            {{-- Avatar & Infos utilisateur --}}
            <div class="px-6 pb-6 relative">
                <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4 -mt-12 mb-2">
                    <div class="h-24 w-24 rounded-2xl border-4 border-white bg-white shadow-md overflow-hidden flex items-center justify-center shrink-0">
                        <div class="h-full w-full flex items-center justify-center text-3xl font-black text-white" style="background: {{ $profileColor }}">
                            {{ strtoupper(substr($client->user->name ?? 'C', 0, 1)) }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight truncate">{{ $client->user->name ?? 'Client' }}</h2>
                        <p class="text-sm text-slate-500 flex items-center gap-1.5 mt-0.5">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ $client->user->email ?? 'Aucun e-mail renseigné' }}
                        </p>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Métriques Économiques et Abonnement --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Investissement total --}}
            <x-ui.card class="flex items-center gap-4 border border-slate-200/80 shadow-sm">
                <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-2xl shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Investissement engagé</span>
                    <span class="text-2xl font-black text-slate-900 mt-0.5 inline-block">
                        {{ $client->total_investment ? number_format($client->total_investment, 2) . ' €' : '0.00 €' }}
                    </span>
                </div>
            </x-ui.card>

            {{-- Expiration abonnement --}}
            <x-ui.card class="flex items-center gap-4 border border-slate-200/80 shadow-sm">
                <div class="p-3.5 bg-indigo-50 text-indigo-600 rounded-2xl shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Fin d'Abonnement</span>
                    <span class="text-base font-extrabold text-slate-900 mt-1 inline-block">
                        {{ $client->subscription_expires_at?->format('d/m/Y') ?? 'Non planifiée' }}
                    </span>
                </div>
            </x-ui.card>
        </div>

        {{-- Fiche de Coordonnées --}}
        <x-ui.card class="border border-slate-200/80 shadow-sm">
            <x-slot:header>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 0-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M21 12h-4" />
                    </svg>
                    <h3 class="text-base font-bold text-slate-900">Coordonnées personnelles</h3>
                </div>
            </x-slot:header>

            <dl class="divide-y divide-slate-100 text-sm">
                <div class="grid grid-cols-1 sm:grid-cols-3 py-4 gap-1">
                    <dt class="font-semibold text-slate-500">Téléphone</dt>
                    <dd class="sm:col-span-2 text-slate-900 font-semibold">{{ $client->phone ?? $client->user->phone ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 py-4 gap-1">
                    <dt class="font-semibold text-slate-500">Ville de résidence</dt>
                    <dd class="sm:col-span-2 text-slate-900 font-medium capitalize">{{ $client->city_of_residence ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 py-4 gap-1">
                    <dt class="font-semibold text-slate-500">Pays de résidence</dt>
                    <dd class="sm:col-span-2 text-slate-900 font-medium capitalize">{{ $client->country_of_residence ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 py-4 gap-1">
                    <dt class="font-semibold text-slate-500">Pays d'origine</dt>
                    <dd class="sm:col-span-2 text-slate-900 font-medium capitalize">{{ $client->country_of_origin ?? '—' }}</dd>
                </div>
            </dl>
        </x-ui.card>

        {{-- Notes & Observations --}}
        @if($client->notes)
            <div class="p-5 bg-indigo-50/40 rounded-2xl border border-indigo-100/50">
                <h4 class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Notes & Observations Client
                </h4>
                <p class="text-sm text-slate-700 leading-relaxed font-medium whitespace-pre-line">{{ $client->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Colonne latérale (Sidebar) --}}
    <div class="space-y-6">
        
        {{-- Statistiques --}}
        <x-ui.card class="border border-slate-200/80 shadow-sm">
            <x-slot:header>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    <h3 class="text-base font-bold text-slate-900">Activité & Statistiques</h3>
                </div>
            </x-slot:header>

            <div class="space-y-3.5">
                {{-- Exploitations --}}
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">Exploitations</span>
                    </div>
                    <span class="text-sm font-bold text-slate-900 bg-white border border-slate-200 px-2.5 py-0.5 rounded-lg shadow-sm">
                        {{ $client->farms->count() }}
                    </span>
                </div>

                {{-- Rapports --}}
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">Rapports d'évaluation</span>
                    </div>
                    <span class="text-sm font-bold text-slate-900 bg-white border border-slate-200 px-2.5 py-0.5 rounded-lg shadow-sm">
                        {{ $client->reports->count() }}
                    </span>
                </div>

                {{-- Photos --}}
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a1 1 0 011.414 0L16 17m0 0l2.586-2.586a1 1 0 011.414 0L22 17V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">Galerie photo</span>
                    </div>
                    <span class="text-sm font-bold text-slate-900 bg-white border border-slate-200 px-2.5 py-0.5 rounded-lg shadow-sm">
                        {{ $client->photos->count() }}
                    </span>
                </div>

                {{-- Abonnements d'origine --}}
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">Abonnements liés</span>
                    </div>
                    <span class="text-sm font-bold text-slate-900 bg-white border border-slate-200 px-2.5 py-0.5 rounded-lg shadow-sm">
                        {{ $client->subscriptions->count() }}
                    </span>
                </div>
            </div>
        </x-ui.card>

        {{-- Activité compte (Timeline) --}}
        <x-ui.card class="border border-slate-200/80 shadow-sm">
            <x-slot:header>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-base font-bold text-slate-900">Suivi d'activité</h3>
                </div>
            </x-slot:header>
            
            <div class="relative border-l-2 border-slate-100 ml-3 pl-5 space-y-5">
                <div class="relative">
                    <span class="absolute -left-[26px] top-1 flex h-3 w-3 items-center justify-center rounded-full bg-indigo-600 ring-4 ring-white"></span>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dernière activité</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $client->user->updated_at?->diffForHumans() ?? '—' }}</p>
                </div>
                <div class="relative">
                    <span class="absolute -left-[26px] top-1 flex h-3 w-3 items-center justify-center rounded-full bg-slate-300 ring-4 ring-white"></span>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Création du compte</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">Le {{ $client->user->created_at?->format('d/m/Y') ?? '—' }}</p>
                </div>
            </div>
        </x-ui.card>

        {{-- Actions d'administration --}}
        <x-ui.card class="border border-slate-200/80 shadow-sm">
            <x-slot:header>
                <h3 class="text-base font-bold text-slate-900">Actions d'administration</h3>
            </x-slot:header>
            <div class="space-y-2.5">
                {{-- Voir les fermes --}}
                <a href="{{ route('admin.clients.farms', $client->id) }}" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Consulter les fermes
                </a>
                
                {{-- Modifier --}}
                <a href="{{ route('admin.clients.edit', $client->id) }}" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-950 transition-colors shadow-sm">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier la fiche client
                </a>

                {{-- Supprimer --}}
                <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement ce client ?')" class="pt-2 border-t border-slate-100">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold text-rose-600 bg-rose-50 border border-rose-200/60 rounded-xl hover:bg-rose-100 hover:text-rose-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Supprimer le compte
                    </button>
                </form>
            </div>
        </x-ui.card>
    </div>
</div>
@endsection