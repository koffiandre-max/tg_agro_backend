@extends('layouts.app')

@section('title', 'Détail de la Mission')
@section('page-title', 'Détail de la Mission')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.technitian.missions') }}" class="group inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 mb-2 transition-colors duration-150">
                <svg class="w-4 h-4 mr-1.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour aux missions
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">{{ $mission->title }}</h1>
            <div class="mt-3 flex flex-wrap items-center gap-3">
                @if($mission->status === 'completed')
                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-emerald-600/20">Terminée</span>
                @elseif($mission->status === 'in_progress')
                    <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 ring-1 ring-amber-600/20">En cours</span>
                @elseif($mission->status === 'pending')
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 ring-1 ring-blue-600/20">En attente</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700 ring-1 ring-red-600/20">Annulée</span>
                @endif
                <span class="text-sm text-slate-500">Créée le {{ $mission->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
            </div>
        </div>

        @if(auth()->user()?->role === 'admin')
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.technitian.missions.edit', $mission->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
                <form method="POST" action="{{ route('admin.technitian.missions.destroy', $mission->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette mission ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-red-200 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50">
                        Supprimer
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Carte latérale --}}
        <div class="lg:col-span-1 space-y-6">
            <x-ui.card>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center text-white text-2xl font-bold bg-indigo-600">
                        {{ strtoupper(substr($mission->title ?? 'M', 0, 1)) }}
                    </div>
                    <h2 class="mt-4 text-lg font-bold text-gray-900">Mission</h2>
                </div>

                <div class="mt-6 space-y-3 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user w-4 text-gray-400"></i>
                        {{ $mission->technician?->user?->name ?? '—' }}
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-tractor w-4 text-gray-400"></i>
                        {{ $mission->farm?->name ?? '—' }}
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar w-4 text-gray-400"></i>
                        {{ $mission->scheduled_date?->format('d/m/Y') ?? '—' }}
                    </div>
                    @if($mission->completed_at)
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle w-4 text-gray-400"></i>
                            Terminée le {{ $mission->completed_at->format('d/m/Y H:i') }}
                        </div>
                    @endif
                </div>
            </x-ui.card>
        </div>

        {{-- Contenu principal --}}
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Description</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $mission->description ?: '—' }}</p>
            </x-ui.card>

            <x-ui.card>
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Notes</h3>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $mission->notes ?: '—' }}</p>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection
