@extends('layouts.app')

@section('page-title', 'Gestion des Exploitations')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- En-tête --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Exploitations Agricoles</h1>
                <p class="mt-2 text-sm text-gray-600">Gérez toutes les exploitations agricoles</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.farms.datatable') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Vue Datatable
                </a>
                <a href="{{ route('admin.farms.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-700">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvelle Exploitation
                </a>
            </div>
        </div>

        {{-- Message d'information --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-blue-700">
                    Utilisez la <a href="{{ route('admin.farms.datatable') }}" class="font-medium underline">vue datatable</a> pour une expérience de navigation améliorée avec recherche, filtres et tri.
                </p>
            </div>
        </div>

        {{-- Contenu de la vue classique --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Liste des exploitations</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-500 text-center py-8">
                    Cette vue sera complétée avec la liste des exploitations.
                    <br>
                    <a href="{{ route('admin.farms.datatable') }}" class="text-indigo-600 hover:underline mt-2 inline-block">
                        Accéder à la vue datatable complète →
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection