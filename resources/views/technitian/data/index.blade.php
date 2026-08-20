@extends('layouts.app')

@section('page-title', 'Mes Saisies de Données')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- En-tête --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mes Saisies de Données</h1>
                <p class="mt-2 text-sm text-gray-600">Visualisez et gérez vos saisies agronomiques</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.technitian.data.create') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-purple-700">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvelle Saisie
                </a>
            </div>
        </div>

        {{-- Composant Livewire Datatable --}}
        @livewire('data-entries-table')
    </div>
</div>
@endsection

