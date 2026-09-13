@extends('layouts.app')

@section('title', 'Espace Technicien')
@section('page-title', 'Mon Espace Technicien')

@section('content')
<div class="space-y-6 p-6">
    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-green-500 to-teal-600 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold mb-2">Bonjour, {{ auth()->user()?->name ?? 'Technicien' }} !</h2>
        <p class="text-green-100">{{ now()->translatedFormat('l j F Y') }} — voici vos missions et activités</p>
        @if ($stats['photos_pending'] > 0)
            <p class="mt-2 inline-block bg-white/20 text-white text-xs px-3 py-1 rounded-full">
                {{ $stats['photos_pending'] }} photo(s) en attente de validation
            </p>
        @endif
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Missions du Jour</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['missions_today'] }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 17.816l-3.42-3.42a1 1 0 00-1.42 0L3 16.25V5.25a2.25 2.25 0 012.25-2.25h10.5A2.25 2.25 0 0118 5.25v11a2.25 2.25 0 01-2.25 2.25H5.25"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Missions en Cours</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['missions_in_progress'] }}</p>
                </div>
                <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Rapports Déposés</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['reports'] }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Saisies de Données</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['data_entries'] }}</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM14 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM4 16a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25v2.25A2.25 2.25 0 016.75 20.25H4.5A2.25 2.25 0 012.25 18v-2.25z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Missions du Jour --}}
    <div class="bg-white rounded-xl p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Missions du Jour</h2>
            <a href="{{ route('admin.technitian.missions') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Voir tout</a>
        </div>
        <div class="space-y-3">
            @forelse ($missionsToday as $mission)
                <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $mission->title }}</p>
                            <p class="text-xs text-gray-500 mt-1 truncate">
                                {{ $mission->farm?->name ?? 'Exploitation' }}
                                @if ($mission->farm?->location) | {{ $mission->farm->location }} @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $mission->scheduled_date ? $mission->scheduled_date->format('d/m/Y') : 'Non planifiée' }}
                                @if ($mission->farm?->total_area_hectares) | {{ number_format((float) $mission->farm->total_area_hectares, 0, ',', ' ') }} ha @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if ($mission->status === 'pending')
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs rounded-full font-medium">En attente</span>
                            <button data-status-url="{{ route('admin.technitian.missions.status', $mission->id) }}" data-status="in_progress" data-csrf="{{ csrf_token() }}"
                                    class="mission-action h-9 px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                                Démarrer
                            </button>
                        @elseif ($mission->status === 'in_progress')
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">En cours</span>
                            <button data-status-url="{{ route('admin.technitian.missions.status', $mission->id) }}" data-status="completed" data-csrf="{{ csrf_token() }}"
                                    class="mission-action h-9 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                                Terminer
                            </button>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full font-medium">{{ ucfirst($mission->status) }}</span>
                        @endif
                        <a href="{{ route('admin.technitian.missions.show', $mission->id) }}"
                           class="h-9 px-4 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium inline-flex items-center">
                            Détails
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">Aucune mission planifiée aujourd'hui.</p>
            @endforelse
        </div>
    </div>

{{-- PART3 --}}

    {{-- Missions à venir --}}
    <div class="bg-white rounded-xl p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Missions à venir</h2>
            <a href="{{ route('admin.technitian.missions') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Toutes mes missions</a>
        </div>
        <div class="space-y-3">
            @forelse ($upcomingMissions as $mission)
                <div class="flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4 min-w-0">
                        <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $mission->title }}</p>
                            <p class="text-xs text-gray-500 mt-1 truncate">
                                {{ $mission->farm?->name ?? 'Exploitation' }}
                                @if ($mission->farm?->location) | {{ $mission->farm->location }} @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $mission->scheduled_date ? $mission->scheduled_date->format('d/m/Y') : 'Non planifiée' }}
                                @if ($mission->farm?->total_area_hectares) | {{ number_format((float) $mission->farm->total_area_hectares, 0, ',', ' ') }} ha @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if ($mission->status === 'pending')
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs rounded-full font-medium">En attente</span>
                            <button data-status-url="{{ route('admin.technitian.missions.status', $mission->id) }}" data-status="in_progress" data-csrf="{{ csrf_token() }}"
                                    class="mission-action h-9 px-4 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                                Démarrer
                            </button>
                        @elseif ($mission->status === 'in_progress')
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">En cours</span>
                            <button data-status-url="{{ route('admin.technitian.missions.status', $mission->id) }}" data-status="completed" data-csrf="{{ csrf_token() }}"
                                    class="mission-action h-9 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                                Terminer
                            </button>
                        @endif
                        <a href="{{ route('admin.technitian.missions.show', $mission->id) }}"
                           class="h-9 px-4 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium inline-flex items-center">
                            Détails
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">Aucune mission à venir.</p>
            @endforelse
        </div>
    </div>

{{-- PART4 --}}

    {{-- Actions Rapides --}}
    <div class="bg-white rounded-xl p-6 border border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Actions Rapides</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.technitian.reports.create') }}" class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-300 hover:border-blue-500 hover:bg-blue-50 transition-colors">
                <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">Déposer un Rapport</p>
                    <p class="text-xs text-gray-500">Nouveau rapport</p>
                </div>
            </a>

            <a href="{{ route('admin.technitian.photos.create') }}" class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-300 hover:border-green-500 hover:bg-green-50 transition-colors">
                <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">Ajouter Photos</p>
                    <p class="text-xs text-gray-500">Upload photos terrain</p>
                </div>
            </a>

            <a href="{{ route('admin.technitian.data.create') }}" class="flex items-center gap-3 p-4 rounded-lg border-2 border-dashed border-gray-300 hover:border-purple-500 hover:bg-purple-50 transition-colors">
                <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM14 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM4 16a2.25 2.25 0 012.25-2.25h2.25a2.25 2.25 0 012.25 2.25v2.25A2.25 2.25 0 016.75 20.25H4.5A2.25 2.25 0 012.25 18v-2.25z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-800">Saisir Données</p>
                    <p class="text-xs text-gray-500">Données agronomiques</p>
                </div>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.mission-action').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const url = btn.dataset.statusUrl;
            const status = btn.dataset.status;
            const csrf = btn.dataset.csrf || document.querySelector('meta[name="csrf-token"]')?.content || '';
            if (!url) return;
            btn.disabled = true;
            const label = btn.textContent.trim();
            btn.textContent = '...';
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (window.NotificationManager) {
                    window.NotificationManager.show(data.message || 'Mission mise à jour.', 'success');
                }
                setTimeout(function () { window.location.reload(); }, 800);
            })
            .catch(function () {
                btn.disabled = false;
                btn.textContent = label;
                if (window.NotificationManager) {
                    window.NotificationManager.show('Erreur lors de la mise à jour.', 'error');
                }
            });
        });
    });
});
</script>
@endpush
@endsection