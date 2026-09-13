@extends('layouts.app')

@section('title', 'Missions')
@section('page-title', 'Missions')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- En-tête --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Missions</h1>
                <p class="mt-2 text-sm text-gray-600">Gérez toutes les missions : liste, fiche, création et modification</p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Bascule de vue + actions --}}
                <div class="inline-flex rounded-lg p-1 bg-white border border-gray-200 shadow-sm">
                {{-- Partie Kanban des missions (désactivée) --}}
                {{-- <a href="{{ route('admin.technitian.missions.kanban') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2m0 10V7"/>
                    </svg>
                    Kanban
                </a> --}}
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-800 bg-gray-100 rounded-md">
                        Liste
                    </span>
                </div>

                @if(auth()->user()?->role === 'admin')
                    <a href="{{ route('admin.technitian.missions.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nouvelle Mission
                    </a>
                @endif
            </div>
        </div>

        {{-- Composant Livewire Datatable --}}
        @livewire('missions-table')
    </div>
</div>
@endsection

