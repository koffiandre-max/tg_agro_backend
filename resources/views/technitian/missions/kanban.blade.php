@extends('layouts.app')

@section('page-title', 'Tableau Kanban - Mes Missions')

@section('content')
<div class="space-y-6 p-4 lg:p-6 bg-gray-50/50 min-h-screen">
    
    {{-- Header & Control Bar --}}
    <div class="bg-white p-4 rounded-xl border border-gray-200/80 shadow-sm space-y-4">
        
        {{-- Top Bar: Titre + Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tableau Kanban</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        Projets & Missions
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">Glissez et déposez les cartes pour mettre à jour l'avancement en temps réel.</p>
            </div>

            {{-- Actions Principales & Bascule de Vue --}}
            <div class="flex items-center gap-2">
                {{-- Basculeur de vue (Style Odoo) --}}
                <div class="inline-flex rounded-lg p-1 bg-gray-100 border border-gray-200">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-800 bg-white rounded-md shadow-xs">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2m0 10V7"/>
                        </svg>
                        Kanban
                    </span>

                    @if(in_array(auth()->user()?->role, ['admin', 'technician']))
                        <a href="{{ route('admin.technitian.missions') }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-200/60 rounded-md transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                            </svg>
                            Vue Liste
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <hr class="border-gray-100" />

        {{-- Barre de recherche & Filtres rapides (Style Odoo / Task) --}}
        <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
            <div class="flex items-center gap-2 flex-1 max-w-md">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           placeholder="Filtrer les missions par titre, client..." 
                           class="w-full pl-9 pr-4 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-800 placeholder-gray-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
            </div>

            {{-- Filtres rapides (Chips) --}}
            <div class="flex items-center gap-1.5 overflow-x-auto py-1">
                <button class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                    Toutes
                </button>
                <button class="px-2.5 py-1 text-xs font-medium rounded-full bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-colors">
                    Urgent
                </button>
                <button class="px-2.5 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-colors">
                    En cours
                </button>
            </div>
        </div>
    </div>

    {{-- Conteneur du composant Livewire --}}
    <div class="min-h-[600px]">
        @livewire('missions-kanban')
    </div>

</div>
@endsection