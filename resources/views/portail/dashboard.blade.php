@extends('layouts.app')

@section('title', 'Mon Espace Client')
@section('page-title', 'Mon Espace')

@section('content')
<div class="space-y-6 p-6">
    {{-- Welcome Card --}}
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white">
        <h2 class="text-2xl font-bold mb-2">Bienvenue, {{ auth()->user()->name }} !</h2>
        <p class="text-indigo-100">Voici le résumé de vos exploitations agricoles</p>
    </div>

    {{-- Prix du marché (produits vivriers - Côte d'Ivoire) --}}
    {{-- <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-5">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="flex items-center gap-2.5">
                <span class="flex size-9 items-center justify-center rounded-lg bg-emerald-600 text-white shadow-sm">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                </span>
                <div>
                    <h3 class="text-sm font-semibold text-emerald-900">Prix du marché</h3>
                    <p class="text-xs text-emerald-700/70">Produits vivriers · Côte d'Ivoire</p>
                </div>
            </div>
            @if($marketPrices->isNotEmpty())
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/70 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-200">
                    <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Mis à jour le {{ $marketPrices->max('recorded_at')?->format('d/m/Y') ?? '—' }}
                </span>
            @endif
        </div>

        @if($marketPrices->isEmpty())
            <p class="mt-4 text-sm text-emerald-700/70">Aucun prix de référence disponible pour le moment.</p>
        @else
            <div class="mt-4 flex flex-wrap gap-2.5">
                @foreach($marketPrices as $price)
                    <div class="flex items-center gap-2 rounded-lg border border-emerald-200 bg-white px-3 py-2 shadow-sm">
                        <span class="text-sm font-medium text-slate-800">{{ $price->product_name }}</span>
                        <span class="text-sm font-bold text-emerald-700">
                            {{ number_format($price->price_per_unit, 0, ',', ' ') }} <span class="text-xs font-normal text-emerald-600/80">{{ $price->currency ?? 'FCFA' }}/{{ $price->unit ?? 'kg' }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
            <p class="mt-3 text-xs text-emerald-700/60">
                Source : {{ $marketPrices->first()->source ?? '—' }} · Région : {{ $marketPrices->first()->region ?? '—' }}
            </p>
        @endif
    </div> --}}

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Mes Exploitations</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $farms->count() }}</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Rapports Disponibles</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $reportsCount }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Photos</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $photosCount }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a2.25 2.25 0 002.25-2.25V6a2.25 2.25 0 00-2.25-2.25H3.75A2.25 2.25 0 001.5 6v12a2.25 2.25 0 002.25 2.25z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Messages Non Lus</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $unreadMessages }}</p>
                </div>
                <div class="h-12 w-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    @if($farms->isNotEmpty())
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
        <x-ui.chart title="Répartition par culture" id="chart-culture" type="doughnut" center />
        <x-ui.chart title="Statut des exploitations" id="chart-status" type="doughnut" center />
        <x-ui.chart title="Estimation des récoltes / Naissances" id="chart-harvest" type="bar" />
        <x-ui.chart title="Effectif du cheptel" id="chart-livestock" type="bar" />
    </div>
    @else
    <div class="bg-white rounded-xl p-12 border border-gray-200 text-center">
        <p class="text-sm text-gray-500">Vous n'avez aucune exploitation enregistrée pour le moment.</p>
    </div>
    @endif

    {{-- Derniers Rapports --}}
    <div class="bg-white rounded-xl p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Derniers Rapports</h2>
            <a href="{{ route('admin.portail.reports') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Voir tout</a>
        </div>
        <div class="space-y-3">
            @forelse($reports as $report)
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $report->title }}</p>
                            <p class="text-xs text-gray-500">{{ $report->farm?->name ?? 'Exploitation inconnue' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Validé</span>
                        <a href="{{ route('admin.portail.reports.download', $report) }}" class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-gray-100 text-gray-600" title="Télécharger">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Aucun rapport disponible pour le moment.</p>
            @endforelse
        </div>
    </div>
    </div>
@endsection

@push('scripts')
@if($farms->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { usePointStyle: true, pointStyle: 'circle', padding: 16, font: { size: 11 } }
            }
        }
    };

    const farmNames = @json($farmNames);
    const farmProgress = @json($farmProgress);
    const cultureTypes = @json($cultureTypes);
    const farmStatuses = @json($farmStatuses);
    const harvestEstimates = @json($harvestEstimates);
    const livestockBirths = @json($livestockBirths);
    const livestockTotals = @json($livestockTotals);

    const cultureLabels = Object.keys(cultureTypes);
    const cultureData = Object.values(cultureTypes);
    const cultureColors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

    const statusLabels = Object.keys(farmStatuses).map(s => s === 'active' ? 'Actif' : (s === 'fallow' ? 'En jachère' : 'Inactif'));
    const statusData = Object.values(farmStatuses);
    const statusColors = ['#10b981', '#f59e0b', '#ef4444'];

    const harvestLabels = Object.keys(harvestEstimates);
    const harvestData = Object.values(harvestEstimates);

    const livestockLabels = Object.keys(livestockTotals);
    const livestockBirthData = Object.values(livestockBirths);
    const livestockTotalData = Object.values(livestockTotals);

    if (document.getElementById('chart-culture')) {
        new Chart(document.getElementById('chart-culture'), {
            type: 'doughnut',
            data: {
                labels: cultureLabels,
                datasets: [{
                    data: cultureData,
                    backgroundColor: cultureColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                ...chartDefaults,
                cutout: '60%'
            }
        });
    }

    if (document.getElementById('chart-status')) {
        new Chart(document.getElementById('chart-status'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: statusColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                ...chartDefaults,
                cutout: '60%'
            }
        });
    }

    if (document.getElementById('chart-harvest')) {
        new Chart(document.getElementById('chart-harvest'), {
            type: 'bar',
            data: {
                labels: harvestLabels.length ? harvestLabels : ['Aucune donnée'],
                datasets: [{
                    label: 'Estimation récolte (kg)',
                    data: harvestData.length ? harvestData : [0],
                    backgroundColor: '#f59e0b',
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                ...chartDefaults,
                plugins: { ...chartDefaults.plugins, legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8, font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.15)' }, ticks: { precision: 0 } }
                }
            }
        });
    }

    if (document.getElementById('chart-livestock')) {
        new Chart(document.getElementById('chart-livestock'), {
            type: 'bar',
            data: {
                labels: livestockLabels.length ? livestockLabels : ['Aucune donnée'],
                datasets: [
                    {
                        label: 'Naissances',
                        data: livestockBirthData.length ? livestockBirthData : [0],
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                        borderSkipped: false
                    },
                    {
                        label: 'Effectif total',
                        data: livestockTotalData.length ? livestockTotalData : [0],
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                ...chartDefaults,
                plugins: { ...chartDefaults.plugins, legend: { display: true, position: 'top' } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 8, font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.15)' }, ticks: { precision: 0 } }
                }
            }
        });
    }
});
</script>
@endif
@endpush