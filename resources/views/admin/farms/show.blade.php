@extends('layouts.app')

@section('page-title', 'Détails de l\'Exploitation')

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32($farm->name ?? 'F')) % count($colors)];
    $photo = $farm->photos->first();
    $imageUrl = $photo && $photo->photo_path ? asset('storage/' . $photo->photo_path) : null;
@endphp

<x-ui.page-header
    title="Détails de l'Exploitation"
    subtitle="Informations et activités de {{ $farm->name }}"
    :breadcrumbs="[['label' => 'Tableau de bord', 'route' => 'dashboard'], ['label' => 'Exploitations', 'route' => 'admin.farms.index'], ['label' => 'Détails']]"
>
    <x-slot:actions>
        <x-ui.btn variant="secondary" href="{{ route('admin.farms.edit', $farm->id) }}" icon="edit">
            Modifier
        </x-ui.btn>
        <x-ui.btn variant="ghost" href="{{ route('admin.farms.index') }}" icon="arrow-left">
            Retour
        </x-ui.btn>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    
    {{-- Colonne principale --}}
    <div class="xl:col-span-2 space-y-6">
        
        {{-- Bannière & Profil Hero --}}
        <x-ui.card class="overflow-hidden !p-0 border border-slate-200 shadow-sm">
            {{-- Image ou dégradé de couverture --}}
            <div class="relative h-44 w-full bg-slate-100">
                @if($imageUrl)
                    <img src="{{ $imageUrl }}" alt="{{ $farm->name }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/50 to-transparent"></div>
                @else
                    <div class="w-full h-full opacity-85" style="background: linear-gradient(135deg, {{ $profileColor }} 0%, #1e1b4b 100%)"></div>
                @endif
                
                {{-- Badge de statut absolu --}}
                <div class="absolute top-4 right-4">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold bg-white/95 text-slate-800 shadow-sm backdrop-blur {{ $farm->statusBadgeClasses() }}">
                        {{ ucfirst($farm->status) }}
                    </span>
                </div>
            </div>

            {{-- Infos chevauchantes --}}
            <div class="px-6 pb-6 relative">
                <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4 -mt-12 mb-4">
                    <div class="h-24 w-24 rounded-2xl border-4 border-white bg-white shadow-md overflow-hidden flex items-center justify-center shrink-0">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $farm->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="h-full w-full flex items-center justify-center text-3xl font-black text-white" style="background: {{ $profileColor }}">
                                {{ strtoupper(substr($farm->name ?? 'F', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight truncate">{{ $farm->name }}</h2>
                        <p class="text-sm text-slate-500 flex items-center gap-1.5 mt-0.5">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $farm->location }}
                        </p>
                    </div>
                </div>
            </div>
        </x-ui.card>

        {{-- Métriques Clés --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Carte Superficie --}}
            <x-ui.card class="flex items-center gap-4 border border-slate-200/80 shadow-sm">
                <div class="p-3.5 bg-indigo-50 text-indigo-600 rounded-2xl shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Superficie Totale</span>
                    <span class="text-2xl font-black text-slate-900 mt-0.5 inline-block">
                        {{ number_format($farm->total_area_hectares, 2) }} <span class="text-sm font-normal text-slate-500">ha</span>
                    </span>
                </div>
            </x-ui.card>

            {{-- Carte Progression --}}
            <x-ui.card class="flex flex-col justify-center border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-2">
                    <span class="uppercase tracking-wider text-slate-400">Avancement de culture</span>
                    <span class="text-indigo-600 text-sm font-extrabold">{{ $farm->crop_stage_progress }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: {{ $farm->crop_stage_progress }}%"></div>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 capitalize flex items-center gap-1">
                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                    Stade : {{ $farm->crop_stage ?? 'non défini' }}
                </p>
            </x-ui.card>
        </div>

        {{-- Fiche Technique (Description List moderne) --}}
        <x-ui.card class="border border-slate-200/80 shadow-sm">
            <x-slot:header>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="text-base font-bold text-slate-900">Fiche technique</h3>
                </div>
            </x-slot:header>

            <dl class="divide-y divide-slate-100 text-sm">
                <div class="grid grid-cols-1 sm:grid-cols-3 py-4 gap-1">
                    <dt class="font-semibold text-slate-500">Type de culture</dt>
                    <dd class="sm:col-span-2 text-slate-900 font-medium capitalize">{{ $farm->culture_type ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 py-4 gap-1">
                    <dt class="font-semibold text-slate-500">Propriétaire référent</dt>
                    <dd class="sm:col-span-2 text-slate-900 font-medium flex items-center gap-2">
                        <span class="inline-block h-6 w-6 rounded-full bg-slate-100 text-slate-700 text-center leading-6 text-[10px] font-bold">
                            {{ strtoupper(substr($farm->user->name ?? 'U', 0, 1)) }}
                        </span>
                        {{ $farm->user->name ?? '—' }}
                    </dd>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 py-4 gap-1">
                    <dt class="font-semibold text-slate-500">Récolte prévisionnelle</dt>
                    <dd class="sm:col-span-2 text-slate-900 font-medium">
                        {{ $farm->expected_harvest_date?->format('d/m/Y') ?? 'Non planifiée' }}
                    </dd>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 py-4 gap-1">
                    <dt class="font-semibold text-slate-500">Dernier passage d'évaluation</dt>
                    <dd class="sm:col-span-2 text-slate-900 font-medium">
                        {{ $farm->last_visit_date?->format('d/m/Y') ?? 'Aucune visite récente enregistrée' }}
                    </dd>
                </div>
            </dl>
        </x-ui.card>

        {{-- Notes / Observations --}}
        @if($farm->notes)
            <div class="p-5 bg-indigo-50/40 rounded-2xl border border-indigo-100/50">
                <h4 class="text-xs font-bold text-indigo-900 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Observations & Notes de suivi
                </h4>
                <p class="text-sm text-slate-700 leading-relaxed font-medium whitespace-pre-line">{{ $farm->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Colonne latérale (Sidebar) --}}
    <div class="space-y-6">
        
        {{-- Statistiques & Activités --}}
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
                {{-- Rapports --}}
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">Rapports de visite</span>
                    </div>
                    <span class="text-sm font-bold text-slate-900 bg-white border border-slate-200 px-2.5 py-0.5 rounded-lg shadow-sm">
                        {{ $farm->reports->count() }}
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
                        <span class="text-sm font-semibold text-slate-700">Photos archivées</span>
                    </div>
                    <span class="text-sm font-bold text-slate-900 bg-white border border-slate-200 px-2.5 py-0.5 rounded-lg shadow-sm">
                        {{ $farm->photos->count() }}
                    </span>
                </div>

                {{-- Clients --}}
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100/50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">Clients reliés</span>
                    </div>
                    <span class="text-sm font-bold text-slate-900 bg-white border border-slate-200 px-2.5 py-0.5 rounded-lg shadow-sm">
                        {{ $farm->clients->count() }}
                    </span>
                </div>
            </div>
        </x-ui.card>

        {{-- Historique (Timeline) --}}
        <x-ui.card class="border border-slate-200/80 shadow-sm">
            <x-slot:header>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-base font-bold text-slate-900">Historique d'administration</h3>
                </div>
            </x-slot:header>
            
            <div class="relative border-l-2 border-slate-100 ml-3 pl-5 space-y-5">
                <div class="relative">
                    <span class="absolute -left-[26px] top-1 flex h-3 w-3 items-center justify-center rounded-full bg-indigo-600 ring-4 ring-white"></span>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mise à jour</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $farm->updated_at?->diffForHumans() ?? '—' }}</p>
                </div>
                <div class="relative">
                    <span class="absolute -left-[26px] top-1 flex h-3 w-3 items-center justify-center rounded-full bg-slate-300 ring-4 ring-white"></span>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Enregistrement</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">Le {{ $farm->created_at?->format('d/m/Y') ?? '—' }}</p>
                </div>
            </div>
        </x-ui.card>

        {{-- Actions Administrateur d'appoint --}}
        @if(auth()->check() && auth()->user()->role === 'admin')
        <x-ui.card class="border border-slate-200/80 shadow-sm">
            <x-slot:header>
                <h3 class="text-base font-bold text-slate-900">Actions d'administration</h3>
            </x-slot:header>
            <div class="space-y-2.5">
                <a href="{{ route('admin.clients.show', $farm->user->id ?? '') }}" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-950 transition-all shadow-sm">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Consulter la fiche client
                </a>
            </div>
        </x-ui.card>
        @endif
    </div>
</div>
@endsection