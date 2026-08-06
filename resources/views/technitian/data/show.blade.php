@extends('layouts.app')

@section('page-title', 'Détail de la Saisie')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.technitian.data.index') }}" class="group inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 mb-2 transition-colors duration-150">
                <svg class="w-4 h-4 mr-1.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour aux saisies
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">
                Saisie du {{ $entry->created_at?->format('d/m/Y') ?? '—' }}
            </h1>
            <p class="mt-1.5 text-sm text-slate-500">
                {{ $entry->farm?->name ?? 'Exploitation inconnue' }} — {{ $entry->client?->user?->name ?? 'Client inconnu' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <x-ui.card>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full mx-auto flex items-center justify-center text-white text-2xl font-bold bg-indigo-600">
                        {{ strtoupper(substr($entry->client?->user?->name ?? 'C', 0, 1)) }}
                    </div>
                    <h2 class="mt-4 text-lg font-bold text-gray-900">Saisie de données</h2>
                    <div class="flex items-center justify-center gap-2 mt-2">
                        @if($entry->status === 'validated')
                            <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700 ring-1 ring-green-600/20">Validé</span>
                        @elseif($entry->status === 'rejected')
                            <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-medium text-red-700 ring-1 ring-red-600/20">Rejeté</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 ring-1 ring-amber-600/20">En attente</span>
                        @endif
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-user w-4 text-gray-400"></i>
                        {{ $entry->technician->name ?? '—' }}
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-tractor w-4 text-gray-400"></i>
                        {{ $entry->farm?->name ?? '—' }}
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-user-tag w-4 text-gray-400"></i>
                        {{ $entry->client?->user?->name ?? '—' }}
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-calendar w-4 text-gray-400"></i>
                        {{ $entry->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </div>
                </div>
            </x-ui.card>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-ui.card class="flex items-center gap-4">
                    <div class="p-3.5 bg-indigo-50 text-indigo-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Stade cultural</span>
                        <span class="text-lg font-black text-gray-900 mt-0.5 inline-block">
                            {{ $entry->crop_stage ?? '—' }}
                        </span>
                        @if($entry->crop_stage_progress)
                            <span class="text-sm font-normal text-gray-500">({{ $entry->crop_stage_progress }}%)</span>
                        @endif
                    </div>
                </x-ui.card>

                <x-ui.card class="flex items-center gap-4">
                    <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 013 15.375v-2.25zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-8.25zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Progression</span>
                        <span class="text-2xl font-black text-gray-900 mt-0.5 inline-block">
                            {{ $entry->crop_stage_progress ?? 0 }}<span class="text-sm font-normal text-gray-500">%</span>
                        </span>
                    </div>
                </x-ui.card>

                <x-ui.card class="flex items-center gap-4">
                    <div class="p-3.5 bg-amber-50 text-amber-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Conditions météo</span>
                        <span class="text-lg font-black text-gray-900 mt-0.5 inline-block">
                            {{ $entry->weather_conditions ?? '—' }}
                        </span>
                    </div>
                </x-ui.card>

                <x-ui.card class="flex items-center gap-4">
                    <div class="p-3.5 bg-purple-50 text-purple-600 rounded-2xl shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <div>
                        <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Récolte prévue</span>
                        <span class="text-lg font-black text-gray-900 mt-0.5 inline-block">
                            {{ $entry->estimated_harvest_date?->format('d/m/Y') ?? '—' }}
                        </span>
                    </div>
                </x-ui.card>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <x-ui.card>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Intrants Utilisés</h3>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $entry->inputs_used ?? '—' }}</p>
                </x-ui.card>

                <x-ui.card>
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Observations</h3>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $entry->observations ?? '—' }}</p>
                </x-ui.card>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.technitian.data.edit', $entry->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
                <form method="POST" action="{{ route('admin.technitian.data.destroy', $entry->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette saisie ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-red-200 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
