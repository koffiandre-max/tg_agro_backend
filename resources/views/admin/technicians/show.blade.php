@extends('layouts.app')

@section('title', 'Détails Technicien - ' . ($technician->user->name ?? 'Technicien'))

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32($technician->user->name ?? 'T')) % count($colors)];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.technicians.index') }}" class="group inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 mb-2 transition-colors duration-150">
                <svg class="w-4 h-4 mr-1.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour aux techniciens
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">
                Détails de <span class="text-indigo-600">{{ $technician->user->name ?? 'Technicien' }}</span>
            </h1>
            <p class="mt-1.5 text-sm text-slate-500 flex items-center gap-1.5">
                <span class="inline-flex h-2 w-2 rounded-full {{ $technician->is_available ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                {{ $technician->is_available ? 'Disponible' : 'Indisponible' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <x-ui.card>
                <div class="text-center">
                    @if($technician->user->avatar)
                        <img src="{{ Storage::url($technician->user->avatar) }}" alt="{{ $technician->user->name }}" class="w-32 h-32 rounded-full mx-auto object-cover">
                    @else
                        <div class="w-32 h-32 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold" style="background: {{ $profileColor }}">
                            {{ strtoupper(substr($technician->user->name ?? 'T', 0, 1)) }}
                        </div>
                    @endif
                    <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $technician->user->name ?? 'Technicien' }}</h2>
                    <div class="flex items-center justify-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 ring-1 ring-gray-600/20">
                            {{ ucfirst($technician->user->role ?? 'Technicien') }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-blue-600/20">
                            Base: {{ $technician->location_base ?? 'Non spécifiée' }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    @if($technician->user->email)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-envelope w-4 text-gray-400"></i>
                            {{ $technician->user->email }}
                        </div>
                    @endif
                    @if($technician->user->phone)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-phone w-4 text-gray-400"></i>
                            {{ $technician->user->phone }}
                        </div>
                    @endif
                    @if($technician->phone_secondary)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-phone-alt w-4 text-gray-400"></i>
                            {{ $technician->phone_secondary }}
                        </div>
                    @endif
                    @if($technician->location_base)
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt w-4 text-gray-400"></i>
                            {{ $technician->location_base }}
                        </div>
                    @endif
                </div>
            </x-ui.card>

            @if($technician->notes)
                <div class="mt-5">
                    <x-ui.card>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Notes</h3>
                        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $technician->notes }}</p>
                    </x-ui.card>
                </div>
            @endif
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.card class="flex items-center gap-4">
                    <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Missions en cours</span>
                        <span class="text-2xl font-black text-gray-900 mt-0.5 inline-block">
                            {{ $technician->current_workload }} / {{ $technician->max_concurrent_missions }}
                        </span>
                    </div>
                </x-ui.card>

                <x-ui.card class="flex items-center gap-4">
                    <div class="p-3.5 bg-blue-50 text-blue-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Exploitations</span>
                        <span class="text-2xl font-black text-gray-900 mt-0.5 inline-block">
                            {{ $technician->farms->count() }}
                        </span>
                    </div>
                </x-ui.card>

                <x-ui.card class="flex items-center gap-4">
                    <div class="p-3.5 bg-purple-50 text-purple-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Rapports</span>
                        <span class="text-2xl font-black text-gray-900 mt-0.5 inline-block">
                            {{ $technician->reports->count() }}
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
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Inscrit le</span>
                        <span class="text-lg font-black text-gray-900 mt-0.5 inline-block">
                            {{ $technician->created_at?->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                </x-ui.card>
            </div>

            {{-- Missions récentes --}}
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Missions récentes</h3>
                    <span class="text-xs text-gray-500">Total: {{ $technician->missions->count() }}</span>
                </div>
                @if($technician->missions->count() > 0)
                    <div class="space-y-2">
                        @foreach($technician->missions as $mission)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $mission->title ?? '—' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $mission->farm?->name ?? '—' }} - {{ $mission->scheduled_date?->format('d/m/Y') ?? '—' }}</p>
                                </div>
                                <span class="ml-2 inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $mission->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $mission->status === 'in_progress' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $mission->status === 'pending' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $mission->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ $mission->status ?? '—' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center py-3">Aucune mission pour le moment</p>
                @endif
            </x-ui.card>

            {{-- Actions rapides --}}
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <x-ui.btn href="{{ route('admin.technicians.missions', $technician->id) }}" variant="primary" icon="eye">
                        Voir les Missions
                    </x-ui.btn>
                    <x-ui.btn href="{{ route('admin.technicians.reports', $technician->id) }}" variant="secondary" icon="eye">
                        Voir les Rapports
                    </x-ui.btn>
                    <x-ui.btn href="{{ route('admin.technicians.edit', $technician->id) }}" variant="secondary" icon="edit" class="w-full">
                        Modifier
                    </x-ui.btn>
                    <x-ui.btn href="{{ route('admin.technicians.index') }}" variant="ghost" icon="arrow-left" class="w-full">
                        Retour
                    </x-ui.btn>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection