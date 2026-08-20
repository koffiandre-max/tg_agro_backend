@extends('layouts.app')

@section('page-title', 'Gestion des Clients')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- En-tête --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Clients</h1>
                <p class="mt-2 text-sm text-gray-600">Gérez tous les clients de la diaspora</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.clients.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-700">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouveau Client
                </a>
            </div>
        </div>

        {{-- Composant Livewire --}}
        @livewire('clients-table')
    </div>
</div>
@endsection
