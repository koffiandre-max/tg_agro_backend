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
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200/50">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Système opérationnel
            </span>
        </div>
    </div>

    {{-- Cartes de Statistiques (KPIs) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        {{-- Clients --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Clients</p>
                    <p class="text-3xl font-black text-slate-900 mt-2">12</p>
                </div>
                <div class="h-11 w-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100/50">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-1.5 text-xs">
                <span class="inline-flex items-center gap-0.5 font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    +8%
                </span>
                <span class="text-slate-400">vs mois dernier</span>
            </div>
        </div>

        {{-- Techniciens --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Techniciens</p>
                    <p class="text-3xl font-black text-slate-900 mt-2">8</p>
                </div>
                <div class="h-11 w-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center border border-emerald-100/50">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-1.5 text-xs">
                <span class="inline-flex items-center gap-0.5 font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    +12%
                </span>
                <span class="text-slate-400">vs mois dernier</span>
            </div>
        </div>

        {{-- Exploitations --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Exploitations</p>
                    <p class="text-3xl font-black text-slate-900 mt-2">24</p>
                </div>
                <div class="h-11 w-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center border border-purple-100/50">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-1.5 text-xs">
                <span class="inline-flex items-center gap-0.5 font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    +15%
                </span>
                <span class="text-slate-400">vs mois dernier</span>
            </div>
        </div>

        {{-- Rapports en attente --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Rapports en attente</p>
                    <p class="text-3xl font-black text-slate-900 mt-2">5</p>
                </div>
                <div class="h-11 w-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center border border-amber-100/50">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-1.5 text-xs">
                <span class="inline-flex items-center gap-0.5 font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded-md">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 13l-7 7-7-7m7 7V3"/>
                    </svg>
                    -20%
                </span>
                <span class="text-slate-400">par rapport à hier</span>
            </div>
        </div>
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