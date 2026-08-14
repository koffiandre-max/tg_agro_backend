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

    {{-- Cartes de Statistiques (KPIs) — triables par glisser-déposer --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
         x-on:dragover.prevent
         x-on:drop.prevent="dropAtEnd()">
        <template x-for="(feature, index) in features" :key="feature.key">
            <div draggable="true"
                 class="transition-all duration-150 cursor-grab active:cursor-grabbing"
                 :class="[
                     feature.enabled ? '' : 'opacity-50 grayscale',
                     dragIndex === index ? 'opacity-40 scale-[0.98]' : '',
                     dragOverIndex === index && dragIndex !== index ? 'ring-2 ring-yellow-400 rounded-2xl' : '',
                 ]"
                 @dragstart="dragStart(index)"
                 @dragend="dragEnd()"
                 @dragover.prevent="dragOverIndex = index"
                 @drop.stop.prevent="dropAt(index)">
                <div class="flex items-center justify-center gap-1.5 mb-2 py-1 rounded-lg border border-dashed border-slate-200 bg-slate-50/60 text-slate-400 select-none">
                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M6 4a1 1 0 100 2h8a1 1 0 100-2H6zM6 9a1 1 0 100 2h8a1 1 0 100-2H6zM6 14a1 1 0 100 2h8a1 1 0 100-2H6z"/>
                    </svg>
                    <span class="text-[10px] uppercase tracking-wider font-semibold">Glisser pour réordonner</span>
                </div>
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

    {{-- Graphiques : un chart par statistique (3 par ligne) --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                <svg class="w-4.5 h-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
                Graphiques par statistique
            </h2>
            <span class="text-xs text-slate-400">Évolution des créations sur les 6 derniers mois</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
             x-on:dragover.prevent
             x-on:drop.prevent="dropAtEnd()">
            <template x-for="(feature, index) in features" :key="feature.key">
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col cursor-grab active:cursor-grabbing transition-all duration-150"
                     draggable="true"
                     :class="[
                         feature.enabled ? '' : 'opacity-50 grayscale',
                         dragIndex === index ? 'opacity-40 scale-[0.98]' : '',
                         dragOverIndex === index && dragIndex !== index ? 'ring-2 ring-yellow-400' : '',
                     ]"
                     @dragstart="dragStart(index)"
                     @dragend="dragEnd()"
                     @dragover.prevent="dragOverIndex = index"
                     @drop.stop.prevent="dropAt(index)">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 truncate pr-2" x-text="feature.label"></h3>
                        <span class="text-lg font-black text-slate-900 shrink-0" x-text="feature.count"></span>
                    </div>
                    <div class="h-44">
                        <canvas :id="'chart-' + feature.key"></canvas>
                    </div>
                    <p class="mt-2 pt-2 border-t border-slate-100 text-[11px] text-slate-400 text-center">
                        Tendance
                        <span :class="feature.trend >= 0 ? 'text-emerald-600' : 'text-red-600'"
                              x-text="(feature.trend > 0 ? '+' : '') + feature.trend + '%'"></span>
                        vs mois dernier
                    </p>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function settingsPanel() {
        return {
            features: @json($features),
            stats: @json($stats),
            monthLabels: @json($monthLabels),
            charts: {},
            dragIndex: null,
            dragOverIndex: null,
            init() {
                this.renderCharts();
                this.$watch('features', () => this.renderCharts(), { deep: true });
            },
            ready() {
                return typeof Chart !== 'undefined'
                    && this.features.length > 0
                    && this.features.every(f => document.getElementById('chart-' + f.key));
            },
            activeFeatures() {
                return this.features.filter(f => f.enabled);
            },
            chartData() {
                return {
                    labels: this.monthLabels,
                    features: this.features,
                    colors: this.stats.colors,
                };
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

                if (data.features.length === 0) {
                    return;
                }

                const colors = data.colors;

                data.features.forEach((feature, index) => {
                    const ctx = document.getElementById('chart-' + feature.key);
                    if (! ctx) {
                        return;
                    }

                    const color = colors[index % colors.length];

                    this.charts[feature.key] = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: feature.label,
                                data: feature.monthly,
                                borderColor: color,
                                backgroundColor: color + '22',
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: color,
                                pointRadius: 2,
                                pointHoverRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (c) => c.parsed.y + ' création(s)',
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 6, font: { size: 9 } }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: { precision: 0 },
                                    grid: { color: 'rgba(148,163,184,0.15)' }
                                }
                            }
                        }
                    });
                });
            },
            dragStart(index) {
                this.dragIndex = index;
            },
            dragEnd() {
                this.dragIndex = null;
                this.dragOverIndex = null;
            },
            moveTo(from, to) {
                if (from === null || from === to) {
                    return;
                }
                const arr = [...this.features];
                const [moved] = arr.splice(from, 1);
                arr.splice(to === null ? arr.length : to, 0, moved);
                this.features = arr;
                this.saveOrder();
            },
            dropAt(index) {
                this.moveTo(this.dragIndex, index);
                this.dragEnd();
            },
            dropAtEnd() {
                this.moveTo(this.dragIndex, null);
                this.dragEnd();
            },
            save() {
                this.saveOrder();
            },
            saveOrder() {
                const payload = this.features.map(f => ({ key: f.key, enabled: f.enabled }));
                fetch('{{ route('admin.settings.features.update') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ features: payload })
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
