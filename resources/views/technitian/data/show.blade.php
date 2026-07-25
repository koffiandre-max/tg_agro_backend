@extends('layouts.app')

@section('page-title', 'Détail de la Saisie')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Détail de la Saisie</h1>
                <p class="mt-2 text-sm text-gray-600">Saisie du {{ $entry->created_at?->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.technitian.data.edit', $entry->id) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-purple-700">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                </a>
                <a href="{{ route('admin.technitian.data.index') }}" class="rounded-full border border-gray-200 bg-white px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Retour
                </a>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Statut --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Statut</h3>
                @if($entry->status === 'validated')
                    <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">Validé</span>
                @elseif($entry->status === 'rejected')
                    <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700">Rejeté</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700">En attente</span>
                @endif
            </div>

            {{-- Exploitation et Client --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Localisation</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Exploitation</p>
                        <p class="text-sm text-gray-900">{{ $entry->farm?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Client</p>
                        <p class="text-sm text-gray-900">{{ $entry->client?->user?->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Stade Cultural --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Stade Cultural</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Stade</p>
                        <p class="text-sm text-gray-900">{{ $entry->crop_stage ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Progression</p>
                        <p class="text-sm text-gray-900">{{ $entry->crop_stage_progress ?? 0 }}%</p>
                    </div>
                </div>
            </div>

            {{-- Conditions & Récolte --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Conditions & Récolte</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Météo</p>
                        <p class="text-sm text-gray-900">{{ $entry->weather_conditions ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Date de récolte prévue</p>
                        <p class="text-sm text-gray-900">{{ $entry->estimated_harvest_date?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Intrants --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Intrants Utilisés</h3>
                <p class="text-sm text-gray-900 whitespace-pre-line">{{ $entry->inputs_used ?? '-' }}</p>
            </div>

            {{-- Observations --}}
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Observations</h3>
                <p class="text-sm text-gray-900 whitespace-pre-line">{{ $entry->observations ?? '-' }}</p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <form method="POST" action="{{ route('admin.technitian.data.destroy', $entry->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette saisie ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-full border border-red-200 bg-white px-6 py-3 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
