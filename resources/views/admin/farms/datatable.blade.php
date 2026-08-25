@extends('layouts.app')

@section('page-title', 'Gestion des Exploitations')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- En-tête --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Exploitations</h1>
                <p class="mt-2 text-sm text-gray-600">Gérez toutes les exploitations agricoles</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Switch Culture / Élevage --}}
                <div class="inline-flex rounded-lg p-1 bg-white border border-gray-200 shadow-sm">
                    <a href="{{ route('admin.farms.culture') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md transition-all {{ ($type ?? '') === 'culture' ? 'text-emerald-700 bg-emerald-50 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                        </svg>
                        Culture
                    </a>
                    <a href="{{ route('admin.farms.elevage') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md transition-all {{ ($type ?? '') === 'elevage' ? 'text-blue-700 bg-blue-50 font-semibold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/>
                        </svg>
                        Élevage
                    </a>
                </div>

                <a href="{{ route('admin.farms.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-700">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvelle Exploitation
                </a>
            </div>
        </div>

        {{-- Composant Livewire Datatable --}}
        @livewire('farms-table', ['type' => $type ?? ''])
    </div>
</div>
@endsection
