@extends('layouts.app')

@section('title', 'Détails Technicien - ' . ($technician->user->name ?? 'Technicien'))
@section('page-title', 'Détails du Technicien')

@section('content')
@php
    $colors = ['#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#10b981', '#f59e0b'];
    $profileColor = $colors[abs(crc32($technician->user->name ?? 'T')) % count($colors)];

    $workloadPercent = min(($technician->current_workload / max($technician->max_concurrent_missions, 1)) * 100, 100);
    $workloadColor = $workloadPercent < 40 ? 'bg-emerald-500' : ($workloadPercent < 70 ? 'bg-amber-500' : 'bg-red-500');
    $workloadLabel = $workloadPercent < 40 ? 'Charge faible - Disponible pour de nouvelles missions' :
                    ($workloadPercent < 70 ? 'Charge modérée' : 'Charge élevée - Proche de la capacité maximale');
@endphp

{{-- Header --}}
<div class="mb-8">
    <a href="{{ route('admin.technicians.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4 transition-colors">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Retour aux techniciens
    </a>
    <h1 class="text-2xl font-bold text-gray-900">{{ $technician->user->name ?? 'Technicien' }}</h1>
    <p class="mt-1 text-gray-500">Détails du technicien et suivi de ses missions</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- Colonne principale (2/3) --}}
    <div class="xl:col-span-2 space-y-6">
        {{-- Carte principale - Profil --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="flex items-start gap-6">
                    {{-- Avatar --}}
                    <div class="relative shrink-0">
                        <div class="h-24 w-24 rounded-2xl bg-white shadow-sm flex items-center justify-center overflow-hidden border-4 border-gray-100">
                            @if($technician->user->avatar)
                                <img src="{{ Storage::url($technician->user->avatar) }}"
                                     alt="{{ $technician->user->name }}"
                                     class="h-full w-full object-cover">
                            @else
                                <span class="text-3xl font-bold" style="color: {{ $profileColor }}">
                                    {{ strtoupper(substr($technician->user->name ?? 'T', 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        {{-- Badge de statut --}}
                        <div class="absolute -top-1 -right-1">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{
                                $technician->is_available ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'
                            }}">
                                {{ $technician->is_available ? 'Disponible' : 'Indisponible' }}
                            </span>
                        </div>
                    </div>

                    {{-- Infos principales --}}
                    <div class="flex-1 min-w-0">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $technician->user->name ?? 'Technicien' }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $technician->user->email ?? '—' }}</p>

                        {{-- Badges --}}
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-full bg-gray-50 px-2.5 py-0.5 text-xs font-medium text-gray-700">
                                {{ ucfirst($technician->user->role ?? 'Technicien') }}
                            </span>
                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                                Base: {{ $technician->location_base ?? 'Non spécifiée' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informations de contact --}}
            <div class="border-t border-gray-100 px-6 py-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-xs font-medium text-gray-500">Téléphone principal</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $technician->user->phone ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-xs font-medium text-gray-500">Téléphone secondaire</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $technician->phone_secondary ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-xs font-medium text-gray-500">Missions max simultanées</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $technician->max_concurrent_missions ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-xs font-medium text-gray-500">Date d'inscription</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $technician->created_at?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($technician->notes)
            <div class="border-t border-gray-100 px-6 py-5">
                <p class="text-xs font-medium text-gray-500 mb-2">Notes</p>
                <p class="text-sm text-gray-600 bg-gray-50 rounded-xl p-4 leading-relaxed">{{ $technician->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Carte - Charge de travail --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Charge de travail</h3>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Charge actuelle</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900">
                                {{ $technician->current_workload }} / {{ $technician->max_concurrent_missions }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pourcentage</p>
                            <p class="mt-1 text-2xl font-bold {{ $workloadPercent < 70 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ number_format($workloadPercent, 0) }}%
                            </p>
                        </div>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="{{ $workloadColor }} h-2 rounded-full transition-all duration-500"
                             style="width: {{ $workloadPercent }}%"></div>
                    </div>

                    <p class="text-sm text-gray-500">{{ $workloadLabel }}</p>
                </div>
            </div>
        </div>

        {{-- Carte - Actions rapides --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('admin.technicians.missions', $technician->id) }}" class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Voir les Missions
                    </a>
                    <a href="{{ route('admin.technicians.reports', $technician->id) }}" class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Voir les Rapports
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Colonne droite (1/3) - Sidebar --}}
    <div class="space-y-6">
        {{-- Carte - Statistiques --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>

                @php
                    $missionsCount = \App\Models\Mission::where('technician_id', $technician->id)->count();
                    $completedMissions = \App\Models\Mission::where('technician_id', $technician->id)
                        ->where('status', 'completed')->count();
                    $farmsCount = \App\Models\Farm::where('assigned_technician_id', $technician->id)->count();
                    $reportsCount = \App\Models\Report::where('technician_id', $technician->user_id)->count();
                @endphp

                <div class="grid grid-cols-2 gap-4">
                    <div class="rounded-xl bg-gray-50 p-4">
                        <p class="text-xs font-medium text-gray-500">Missions totales</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">{{ $missionsCount }}</p>
                    </div>
                    <div class="rounded-xl bg-emerald-50 p-4">
                        <p class="text-xs font-medium text-emerald-600">Terminées</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-700">{{ $completedMissions }}</p>
                    </div>
                    <div class="rounded-xl bg-purple-50 p-4">
                        <p class="text-xs font-medium text-purple-600">Exploitations</p>
                        <p class="mt-1 text-2xl font-bold text-purple-700">{{ $farmsCount }}</p>
                    </div>
                    <div class="rounded-xl bg-blue-50 p-4">
                        <p class="text-xs font-medium text-blue-600">Rapports</p>
                        <p class="mt-1 text-2xl font-bold text-blue-700">{{ $reportsCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Carte - Informations compte --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations compte</h3>

                <div class="space-y-3">
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Inscription</p>
                            <p class="text-xs text-gray-500">Date de création du compte</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ $technician->user->created_at?->format('d/m/Y') ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Dernière activité</p>
                            <p class="text-xs text-gray-500">Dernière mise à jour</p>
                        </div>
                        <span class="text-xs text-gray-400">{{ $technician->user->updated_at?->diffForHumans() ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Carte - Actions --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>

                <div class="space-y-2">
                    <a href="{{ route('admin.technicians.edit', $technician->id) }}" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Modifier le technicien
                    </a>

                    <a href="{{ route('admin.technicians.index') }}" class="flex items-center justify-center gap-2 w-full px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection