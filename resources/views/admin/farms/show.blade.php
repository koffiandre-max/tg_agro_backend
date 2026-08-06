@extends('layouts.app')

@section('title', 'Détails de l\'Exploitation - ' . ($farm->name ?? 'Exploitation'))

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32($farm->name ?? 'F')) % count($colors)];
    $photo = $farm->photos->first();
    $imageUrl = $photo && $photo->photo_path ? asset('storage/' . $photo->photo_path) : null;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.farms.index') }}" class="group inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 mb-2 transition-colors duration-150">
                <svg class="w-4 h-4 mr-1.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour aux exploitations
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">
                Détails de <span class="text-indigo-600">{{ $farm->name }}</span>
            </h1>
            <p class="mt-1.5 text-sm text-slate-500 flex items-center gap-1.5">
                <span class="inline-flex h-2 w-2 rounded-full {{ $farm->status === 'active' ? 'bg-emerald-500' : ($farm->status === 'inactive' ? 'bg-red-500' : 'bg-amber-500') }}"></span>
                {{ $farm->statusLabel() }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <x-ui.card>
                <div class="text-center">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $farm->name }}" class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-white shadow-sm">
                    @else
                        <div class="w-32 h-32 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold" style="background: {{ $profileColor }}">
                            {{ strtoupper(substr($farm->name ?? 'F', 0, 1)) }}
                        </div>
                    @endif
                    <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $farm->name }}</h2>
                    <div class="flex items-center justify-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-50 text-slate-700 ring-1 ring-slate-600/20">
                            {{ $farm->culture_type ?? '—' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-blue-600/20">
                            {{ $farm->location ?? '—' }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    @if($farm->user)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-user w-4 text-gray-400"></i>
                            {{ $farm->user->name ?? '—' }}
                        </div>
                    @endif
                    @if($farm->assignedTechnician)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-hard-hat w-4 text-gray-400"></i>
                            {{ $farm->assignedTechnician->user->name ?? '—' }}
                        </div>
                    @endif
                    @if($farm->total_area_hectares)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-map w-4 text-gray-400"></i>
                            {{ number_format($farm->total_area_hectares, 2) }} ha
                        </div>
                    @endif
                    @if($farm->crop_stage)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-seedling w-4 text-gray-400"></i>
                            {{ $farm->crop_stage }} ({{ $farm->crop_stage_progress }}%)
                        </div>
                    @endif
                </div>
            </x-ui.card>

            @if($farm->notes)
                <div class="mt-5">
                    <x-ui.card>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Notes</h3>
                        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $farm->notes }}</p>
                    </x-ui.card>
                </div>
            @endif
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.card class="flex items-center gap-4">
                    <div class="p-3.5 bg-indigo-50 text-indigo-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Superficie</span>
                        <span class="text-2xl font-black text-gray-900 mt-0.5 inline-block">
                            {{ number_format($farm->total_area_hectares, 2) }} <span class="text-sm font-normal text-gray-500">ha</span>
                        </span>
                    </div>
                </x-ui.card>

                <x-ui.card class="flex items-center gap-4">
                    <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Statut</span>
                        <span class="text-2xl font-black text-gray-900 mt-0.5 inline-block capitalize">
                            {{ $farm->statusLabel() }}
                        </span>
                    </div>
                </x-ui.card>

                <x-ui.card class="flex items-center gap-4">
                    <div class="p-3.5 bg-amber-50 text-amber-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Dernière visite</span>
                        <span class="text-lg font-black text-gray-900 mt-0.5 inline-block">
                            {{ $farm->last_visit_date?->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                </x-ui.card>

                <x-ui.card class="flex items-center gap-4">
                    <div class="p-3.5 bg-purple-50 text-purple-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Clients</span>
                        <span class="text-2xl font-black text-gray-900 mt-0.5 inline-block">
                            {{ $farm->clients->count() }}
                        </span>
                    </div>
                </x-ui.card>
            </div>

            {{-- Fiche technique --}}
            <x-ui.card>
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

            {{-- Actions rapides --}}
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <x-ui.btn href="{{ route('admin.farms.edit', $farm->id) }}" variant="secondary" icon="edit" class="w-full">
                        Modifier
                    </x-ui.btn>
                    <x-ui.btn href="{{ route('admin.farms.index') }}" variant="ghost" icon="arrow-left" class="w-full">
                        Retour
                    </x-ui.btn>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection