@extends('layouts.app')

@section('page-title', 'Gestion des Abonnements')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Abonnements</h1>
                <p class="mt-2 text-sm text-gray-600">Gérez les abonnements des clients</p>
            </div>
            <a href="{{ route('admin.subscriptions.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Ajouter un abonnement
            </a>
        </div>
        @livewire('subscriptions-table')
    </div>
</div>
@endsection
