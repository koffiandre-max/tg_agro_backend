@extends('layouts.app')

@section('title', 'Détails de l\'Exploitation - ' . ($farm->name ?? 'Exploitation'))

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32($farm->name ?? 'F')) % count($colors)];
    $photo = $farm->photos?->first();
    $imageUrl = $photo && $photo->photo_path ? asset('storage/' . $photo->photo_path) : null;
    
    $statusClasses = match($farm->status ?? 'default') {
        'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'inactive' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
        default => 'bg-amber-50 text-amber-700 ring-amber-600/20'
    };

    $statusDot = match($farm->status ?? 'default') {
        'active' => 'bg-emerald-500',
        'inactive' => 'bg-rose-500',
        default => 'bg-amber-500'
    };
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    {{-- En-tête / Navigation --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200/80">
        <div>
            <a href="{{ route('admin.farms.index') }}" class="inline-flex items-center text-xs font-semibold uppercase tracking-wider text-slate-500 hover:text-indigo-600 transition-colors duration-150 mb-3">
                <svg class="w-4 h-4 mr-1 text-slate-400 group-hover:text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour aux exploitations
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    {{ $farm->name }}
                </h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ring-1 ring-inset {{ $statusClasses }}">
                    <span class="h-2 w-2 rounded-full {{ $statusDot }}"></span>
                    {{ $farm->statusLabel() }}
                </span>
            </div>
        </div>

        {{-- Actions rapides en-tête --}}
        <div class="flex items-center gap-3">
            <x-ui.btn href="{{ route('admin.farms.edit', $farm->id) }}" variant="secondary" class="shadow-sm">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Modifier
            </x-ui.btn>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Colonne gauche : Profil de l'exploitation --}}
        <div class="lg:col-span-1 space-y-6">
            <x-ui.card class="overflow-hidden border border-slate-200/80 shadow-sm rounded-2xl">
                {{-- Banner Décoratif --}}
                <div class="h-24 bg-gradient-to-r from-emerald-600 to-teal-700 -mx-6 -mt-6"></div>
                
                <div class="relative text-center px-4 -mt-12">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $farm->name }}" class="w-24 h-24 rounded-2xl mx-auto object-cover ring-4 ring-white shadow-md">
                    @else
                        <div class="w-24 h-24 rounded-2xl mx-auto flex items-center justify-center text-white text-3xl font-bold ring-4 ring-white shadow-md" style="background: {{ $profileColor }}">
                            {{ strtoupper(substr($farm->name ?? 'F', 0, 1)) }}
                        </div>
                    @endif

                    <h2 class="mt-4 text-xl font-bold text-slate-900">{{ $farm->name }}</h2>
                    
                    <div class="flex flex-wrap items-center justify-center gap-2 mt-2">
                        @if($farm->culture_type)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                                {{ $farm->culture_type }}
                            </span>
                        @endif
                        @if($farm->location)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700">
                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $farm->location }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Informations clés --}}
                <div class="mt-6 pt-6 border-t border-slate-100 space-y-4 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center text-slate-500">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Propriétaire
                        </span>
                        <span class="font-semibold text-slate-800">{{ $farm->user->name ?? '—' }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center text-slate-500">
                            <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Technicien
                        </span>
                        <span class="font-semibold text-slate-800">{{ $farm->assignedTechnician->user->name ?? '—' }}</span>
                    </div>

                    @if($farm->total_area_hectares)
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center text-slate-500">
                                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                Superficie
                            </span>
                            <span class="font-semibold text-slate-800">{{ number_format($farm->total_area_hectares, 2) }} ha</span>
                        </div>
                    @endif

                    @if($farm->crop_stage)
                        <div class="pt-2">
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-slate-500">Stade : {{ $farm->crop_stage }}</span>
                                <span class="text-emerald-600">{{ $farm->crop_stage_progress }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $farm->crop_stage_progress }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </x-ui.card>
        </div>

        {{-- Colonne droite : Statistiques & Contenu principal --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Statistiques clés --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <x-ui.card class="border border-slate-200/80 shadow-sm p-4">
                    <div class="p-2.5 w-fit bg-indigo-50 text-indigo-600 rounded-xl mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                    </div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Superficie</span>
                    <span class="text-xl font-bold text-slate-900 mt-1 block">
                        {{ number_format($farm->total_area_hectares, 2) }} <span class="text-xs font-normal text-slate-500">ha</span>
                    </span>
                </x-ui.card>

                <x-ui.card class="border border-slate-200/80 shadow-sm p-4">
                    <div class="p-2.5 w-fit bg-emerald-50 text-emerald-600 rounded-xl mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Statut</span>
                    <span class="text-xl font-bold text-slate-900 mt-1 block capitalize">
                        {{ $farm->statusLabel() }}
                    </span>
                </x-ui.card>

                <x-ui.card class="border border-slate-200/80 shadow-sm p-4">
                    <div class="p-2.5 w-fit bg-amber-50 text-amber-600 rounded-xl mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Dernière visite</span>
                    <span class="text-base font-bold text-slate-900 mt-1 block">
                        {{ $farm->last_visit_date?->format('d/m/Y') ?? '—' }}
                    </span>
                </x-ui.card>

                <x-ui.card class="border border-slate-200/80 shadow-sm p-4">
                    <div class="p-2.5 w-fit bg-purple-50 text-purple-600 rounded-xl mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Clients</span>
                    <span class="text-xl font-bold text-slate-900 mt-1 block">
                        {{ $farm->clients?->count() ?? 0 }}
                    </span>
                </x-ui.card>
            </div>

            {{-- Fiche technique --}}
            <x-ui.card class="border border-slate-200/80 shadow-sm rounded-2xl">
                <x-slot:header>
                    <div class="flex items-center gap-2 pb-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="text-base font-bold text-slate-900">Fiche technique</h3>
                    </div>
                </x-slot:header>

                <dl class="divide-y divide-slate-100 text-sm">
                    <div class="grid grid-cols-1 sm:grid-cols-3 py-3.5 gap-1">
                        <dt class="font-medium text-slate-500">Type de culture</dt>
                        <dd class="sm:col-span-2 text-slate-900 font-semibold capitalize">{{ $farm->culture_type ?? '—' }}</dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 py-3.5 gap-1">
                        <dt class="font-medium text-slate-500">Propriétaire référent</dt>
                        <dd class="sm:col-span-2 text-slate-900 font-semibold flex items-center gap-2">
                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                {{ strtoupper(substr($farm->user->name ?? 'U', 0, 1)) }}
                            </span>
                            {{ $farm->user->name ?? '—' }}
                        </dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 py-3.5 gap-1">
                        <dt class="font-medium text-slate-500">Récolte prévisionnelle</dt>
                        <dd class="sm:col-span-2 text-slate-900 font-semibold">
                            {{ $farm->expected_harvest_date?->format('d/m/Y') ?? 'Non planifiée' }}
                        </dd>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 py-3.5 gap-1">
                        <dt class="font-medium text-slate-500">Dernier passage d'évaluation</dt>
                        <dd class="sm:col-span-2 text-slate-900 font-semibold">
                            {{ $farm->last_visit_date?->format('d/m/Y') ?? 'Aucune visite récente enregistrée' }}
                        </dd>
                    </div>
                </dl>
            </x-ui.card>

            {{-- Notes / Observations (Unique & Stylisé) --}}
            @if($farm->notes)
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200/80">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                        </svg>
                        Observations & Notes de suivi
                    </h4>
                    <p class="text-sm text-slate-700 leading-relaxed font-normal whitespace-pre-line">{{ $farm->notes }}</p>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection