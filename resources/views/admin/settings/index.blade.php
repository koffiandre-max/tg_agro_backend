@extends('layouts.app')

@section('title', 'Paramètres - Tableau de Bord')
@section('page-title', 'Paramètres du Tableau de Bord')

@section('content')
<div class="space-y-6 px-6 pb-12" x-data="settingsPanel()" x-init="init()">

    {{-- En-tête --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Paramètres KPIs</h1>
            <p class="mt-1.5 text-sm text-slate-500">Statistiques globales des fonctionnalités et sélection de celles à afficher.</p>
        </div>
        <button @click="save()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-yellow-600 text-white text-sm font-semibold shadow-sm hover:bg-yellow-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
            </svg>
            Enregistrer la sélection
        </button>
    </div>

    {{-- Cartes de Statistiques (KPIs) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <template x-for="feature in features" :key="feature.key">
            <div :class="feature.enabled ? '' : 'opacity-50 grayscale'">
                @include('admin.settings.partials.kpi-card-alpine')
            </div>
        </template>
    </div>

    {{-- Cartes de bascule des fonctionnalités --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
            <svg class="w-4.5 h-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/>
            </svg>
            Fonctionnalités à afficher
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <template x-for="feature in features" :key="feature.key">
                <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all duration-150"
                       :class="feature.enabled ? 'border-yellow-300 bg-yellow-50/40' : 'border-slate-200 bg-slate-50 hover:border-slate-300 opacity-60 grayscale'">
                    <input type="checkbox" class="peer sr-only" x-model="feature.enabled" :value="feature.key">
                    <span class="relative h-5 w-9 shrink-0 rounded-full transition-colors duration-200"
                          :class="feature.enabled ? 'bg-yellow-500' : 'bg-slate-300'">
                        <span class="absolute top-0.5 left-0.5 h-4 w-4 rounded-full bg-white shadow transition-transform duration-200"
                              :class="feature.enabled ? 'translate-x-4' : 'translate-x-0'"></span>
                    </span>
                    <span class="flex items-center gap-2 min-w-0" :class="feature.enabled ? '' : 'text-slate-400'">
                        <svg class="h-4 w-4 shrink-0" :class="feature.enabled ? 'text-slate-500' : 'text-slate-400'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="feature.icon"/>
                        </svg>
                        <span class="text-sm font-medium truncate" :class="feature.enabled ? 'text-slate-800' : 'text-slate-400'" x-text="feature.label"></span>
                    </span>
                </label>
            </template>
        </div>
    </div>

    {{-- Graphiques (toutes les fonctionnalités ; grisées si désactivées) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <x-ui.chart title="Répartition (Barres)" id="chartBar" type="bar" />
        <x-ui.chart title="Évolution (Linéaire)" id="chartLine" type="line" />
        <x-ui.chart title="Vue Polaire" id="chartPolar" type="polarArea" center />
        <x-ui.chart title="Proportion (Donut)" id="chartDoughnut" type="doughnut" center />
    </div>
</div>
@endsection

@push('scripts')
<script>
    function settingsPanel() {
        return {
            features: @json($features),
            stats: @json($stats),
            charts: {},
            init() {
                this.renderCharts();
                this.$watch('features', () => this.renderCharts(), { deep: true });
            },
            ready() {
                return typeof Chart !== 'undefined'
                    && document.getElementById('chartBar')
                    && document.getElementById('chartLine')
                    && document.getElementById('chartPolar')
                    && document.getElementById('chartDoughnut');
            },
            activeFeatures() {
                return this.features.filter(f => f.enabled);
            },
            chartData() {
                const labels = this.features.map(f => f.label);
                const counts = this.features.map(f => f.count);
                const colors = this.stats.colors;
                const disabled = this.features.map(f => !f.enabled);
                return { labels, counts, colors, disabled };
            },
            destroyCharts() {
                Object.values(this.charts).forEach(c => c && c.destroy());
                this.charts = {};
            },
            renderCharts() {
                if (! this.ready()) {
                    requestAnimationFrame(() => this.renderCharts());
                    return;
                }

                const data = this.chartData();
                this.destroyCharts();

                if (data.labels.length === 0) {
                    return;
                }

                const ctxBar = document.getElementById('chartBar');
                const ctxLine = document.getElementById('chartLine');
                const ctxPolar = document.getElementById('chartPolar');
                const ctxDoughnut = document.getElementById('chartDoughnut');

                if (ctxBar) {
                    this.charts.bar = new Chart(ctxBar, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Total',
                                data: data.counts,
                                backgroundColor: data.colors,
                                borderRadius: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                        }
                    });
                }

                if (ctxLine) {
                    this.charts.line = new Chart(ctxLine, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Total',
                                data: data.counts,
                                borderColor: '#6366f1',
                                backgroundColor: 'rgba(99,102,241,0.15)',
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#6366f1',
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                        }
                    });
                }

                if (ctxPolar) {
                    this.charts.polar = new Chart(ctxPolar, {
                        type: 'polarArea',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.counts,
                                backgroundColor: data.colors.map(c => c + 'cc'),
                                borderColor: '#fff',
                                borderWidth: 1,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { r: { beginAtZero: true, ticks: { precision: 0 } } }
                        }
                    });
                }

                if (ctxDoughnut) {
                    this.charts.doughnut = new Chart(ctxDoughnut, {
                        type: 'doughnut',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                data: data.counts,
                                backgroundColor: data.colors,
                                borderColor: '#fff',
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%',
                        }
                    });
                }
            },
            save() {
                const selected = this.activeFeatures().map(f => f.key);
                fetch('{{ route('admin.settings.features.update') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ features: selected })
                })
                .then(() => {
                    if (window.NotificationManager) {
                        NotificationManager.show('Préférences de tableau de bord enregistrées.', 'success');
                    }
                })
                .catch(() => {
                    if (window.NotificationManager) {
                        NotificationManager.show('Erreur lors de l\'enregistrement.', 'error');
                    }
                });
            }
        };
    }
</script>
@endpush
