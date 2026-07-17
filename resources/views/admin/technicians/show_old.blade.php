@extends('layouts.app')

@section('page-title', 'Détails du Technicien')

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32($technician->user->name ?? 'T')) % count($colors)];
@endphp

<x-ui.page-header
    title="Détails du Technicien"
    subtitle="Informations et suivi de {{ $technician->user->name ?? 'ce technicien' }}"
    :breadcrumbs="[['label' => 'Tableau de bord', 'route' => 'dashboard'], ['label' => 'Techniciens', 'route' => 'admin.technicians.index'], ['label' => 'Détails']]"
>
    <x-slot:actions>
        <x-ui.btn variant="secondary" href="{{ route('admin.technicians.edit', $technician->id) }}" icon="edit">
            Modifier
        </x-ui.btn>
        <x-ui.btn variant="ghost" href="{{ route('admin.technicians.index') }}" icon="arrow-left">
            Retour
        </x-ui.btn>
    </x-slot:actions>
</x-ui.page-header>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    {{-- Colonne centrale : Informations personnelles --}}
    <div class="xl:col-span-2 space-y-6">
        <x-ui.card>
            <div class="text-center">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-2xl text-2xl font-bold text-white shadow-sm" style="background:{{ $profileColor }}">
                    {{ strtoupper(substr($technician->user->name ?? 'T', 0, 1)) }}
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $technician->user->name ?? 'Technicien' }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $technician->user->email ?? '' }}</p>
                <div class="mt-3 flex justify-center gap-2">
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ $technician->user->role ?? 'Technicien' }}
                    </span>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold" style="{{ $technician->is_available ? 'background:#ecfdf5;color:#047857' : 'background:#f3f4f6;color:#6b7280' }}">
                        {{ $technician->is_available ? 'Disponible' : 'Indisponible' }}
                    </span>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3 border-t border-gray-100 pt-5">
                <div class="rounded-xl bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Téléphone principal</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 truncate">{{ $technician->user->phone ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Téléphone secondaire</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 truncate">{{ $technician->phone_secondary ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Base d'opération</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 truncate">{{ $technician->location_base ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Missions simultanées max</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $technician->max_concurrent_missions ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Date d'inscription</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $technician->created_at?->format('d/m/Y') ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">Dernière mise à jour</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $technician->updated_at?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
            </div>

            @if($technician->notes)
            <div class="mt-5 border-t border-gray-100 pt-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Notes</h3>
                <p class="text-sm text-gray-600 bg-gray-50 rounded-lg p-3">{{ $technician->notes }}</p>
            </div>
            @endif
        </x-ui.card>
    </div>

    {{-- Barre latérale droite --}}
    <div class="space-y-6">
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Charge de travail</h3>
            </x-slot:header>
            @php
                $loadPercent = min(($technician->current_workload / max($technician->max_concurrent_missions, 1)) * 100, 100);
                $loadColor = $loadPercent < 40 ? 'bg-green-500' : ($loadPercent < 70 ? 'bg-amber-500' : 'bg-red-500');
                $loadLabel = $loadPercent < 40 ? 'Charge faible - Disponible pour de nouvelles missions' : ($loadPercent < 70 ? 'Charge modérée' : 'Charge élevée');
            @endphp
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Missions en cours</span>
                    <span class="text-sm font-bold text-gray-900">{{ $technician->current_workload }} / {{ $technician->max_concurrent_missions }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="{{ $loadColor }} h-2 rounded-full transition-all duration-500" style="width: {{ $loadPercent }}%"></div>
                </div>
                <p class="text-xs text-gray-400">{{ $loadLabel }}</p>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <div class="flex items-center justify-between w-full">
                    <h3 class="text-base font-semibold text-gray-900">Missions récentes</h3>
                    @php
                        $missionsCount = \App\Models\Mission::where('technician_id', $technician->id)->count();
                    @endphp
                    <span class="text-xs text-gray-500">Total: {{ $missionsCount }}</span>
                </div>
            </x-slot:header>
            @php
                $recentMissions = \App\Models\Mission::where('technician_id', $technician->id)
                    ->with('farm')
                    ->latest()
                    ->take(3)
                    ->get();
            @endphp
            <div class="space-y-2">
                @if($recentMissions->count() > 0)
                    @foreach($recentMissions as $mission)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $mission->title }}</p>
                                <p class="text-xs text-gray-500">{{ $mission->farm?->name ?? '-' }} - {{ $mission->scheduled_date?->format('d/m/Y') }}</p>
                            </div>
                            <span class="ml-2 inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $mission->status === 'completed' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $mission->status === 'in_progress' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $mission->status === 'pending' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $mission->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ $mission->status }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-400 text-center py-3">Aucune mission pour le moment</p>
                @endif
            </div>
        </x-ui.card>
    </div>
</div>
@endsection
