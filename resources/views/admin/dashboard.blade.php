@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Tableau de Bord Admin')

@section('content')
<div class="space-y-6 px-6 pb-12">

    {{-- En-tête de bienvenue --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Tableau de bord</h1>
            <p class="mt-1.5 text-sm text-slate-500">Vue d'ensemble de l'activité opérationnelle de la plateforme.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.settings') }}"
               title="Paramètres du tableau de bord"
               class="inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 bg-white text-slate-600 hover:text-yellow-600 hover:border-yellow-300 hover:bg-yellow-50 transition-colors shadow-sm">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </a>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200/50">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Système opérationnel
            </span>
        </div>
    </div>

    {{-- Cartes de Statistiques (KPIs) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($kpis as $kpi)
            <x-ui.stat-card-2
                :label="$kpi['label']"
                :value="$kpi['count']"
                :icon="$kpi['icon']"
                :color="$kpi['color']"
                :trend="$kpi['trend']"
                :suffix="$kpi['suffix'] ?? 'vs mois dernier'" />
        @endforeach
    </div>

    {{-- Actions rapides --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
            <svg class="w-4.5 h-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Raccourcis & Actions rapides
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Nouveau Client --}}
            <a href="{{ route('admin.clients.create') }}" class="group flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/20 transition-all duration-200">
                <div class="h-10 w-10 bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100 group-hover:text-indigo-700 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">Nouveau Client</p>
                    <p class="text-xs text-slate-400 mt-0.5">Enregistrer un nouveau compte</p>
                </div>
            </a>

            {{-- Nouveau Technicien --}}
            <a href="{{ route('admin.technicians.create') }}" class="group flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/20 transition-all duration-200">
                <div class="h-10 w-10 bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100 group-hover:text-emerald-700 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">Nouveau Technicien</p>
                    <p class="text-xs text-slate-400 mt-0.5">Associer un agent de terrain</p>
                </div>
            </a>

            {{-- Nouvelle Exploitation --}}
            <a href="{{ route('admin.farms.create') }}" class="group flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-purple-500 hover:bg-purple-50/20 transition-all duration-200">
                <div class="h-10 w-10 bg-purple-50 text-purple-600 group-hover:bg-purple-100 group-hover:text-purple-700 rounded-lg flex items-center justify-center transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">Nouvelle Exploitation</p>
                    <p class="text-xs text-slate-400 mt-0.5">Déclarer un nouveau terrain</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Layout asymétrique : Rapports prioritaires & Activités --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Liste d'attente des Rapports (2/3 de l'espace) --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Rapports en attente d'approbation</h3>
                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 border border-amber-200/50">
                    5 en attente
                </span>
            </div>
            
            <div class="divide-y divide-slate-100 flex-1">
                {{-- Rapport 1 --}}
                <div class="p-4 flex items-center justify-between hover:bg-slate-50/40 transition-colors">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="h-9 w-9 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shrink-0 border border-amber-100/30">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900 truncate">Ferme #123 — Analyse d'Irrigation</p>
                            <p class="text-xs text-slate-400 mt-0.5">Par Jean Kouassi · Reçu le 17/07/2026</p>
                        </div>
                    </div>
                    <a href="#" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                        Examiner
                    </a>
                </div>

                {{-- Rapport 2 --}}
                <div class="p-4 flex items-center justify-between hover:bg-slate-50/40 transition-colors">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="h-9 w-9 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center shrink-0 border border-amber-100/30">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-900 truncate">Ferme #456 — Qualité des Sols</p>
                            <p class="text-xs text-slate-400 mt-0.5">Par Marie Diabaté · Reçu le 16/07/2026</p>
                        </div>
                    </div>
                    <a href="#" class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                        Examiner
                    </a>
                </div>
            </div>
        </div>

        {{-- Activités Récentes (1/3 de l'espace - Timeline) --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex flex-col">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-5">Dernières activités</h3>
            
            <div class="relative border-l-2 border-slate-100 ml-3 pl-5 space-y-5 flex-1">
                {{-- Activité 1 --}}
                <div class="relative">
                    <span class="absolute -left-[26px] top-1 flex h-3 w-3 items-center justify-center rounded-full bg-blue-600 ring-4 ring-white"></span>
                    <p class="text-[10px] font-semibold text-slate-400">Il y a 10 min</p>
                    <p class="text-sm font-bold text-slate-900 mt-0.5">Nouveau rapport déposé</p>
                    <p class="text-xs text-slate-500 mt-0.5">Rapport mensuel sur la <strong>Ferme #123</strong> par Jean Kouassi.</p>
                </div>

                {{-- Activité 2 --}}
                <div class="relative">
                    <span class="absolute -left-[26px] top-1 flex h-3 w-3 items-center justify-center rounded-full bg-emerald-600 ring-4 ring-white"></span>
                    <p class="text-[10px] font-semibold text-slate-400">Il y a 1h</p>
                    <p class="text-sm font-bold text-slate-900 mt-0.5">Rapport validé</p>
                    <p class="text-xs text-slate-500 mt-0.5">Rapport de sol validé avec succès pour la <strong>Ferme #456</strong>.</p>
                </div>

                {{-- Activité 3 --}}
                <div class="relative">
                    <span class="absolute -left-[26px] top-1 flex h-3 w-3 items-center justify-center rounded-full bg-purple-600 ring-4 ring-white"></span>
                    <p class="text-[10px] font-semibold text-slate-400">Il y a 2h</p>
                    <p class="text-sm font-bold text-slate-900 mt-0.5">Ajout de médias</p>
                    <p class="text-xs text-slate-500 mt-0.5">Marie Diabaté a chargé <strong>5 nouvelles photos</strong> de suivi sur la Ferme #789.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection