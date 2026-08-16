@extends('layouts.app')

@section('page-title', 'Utilisateurs')

@section('content')

<div class="min-h-screen bg-gray-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 ">
        {{-- En-tête --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Utilisateurs</h1>
                <p class="mt-2 text-sm text-gray-600">Gérez les comptes utilisateurs et leurs rôles.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{  route('admin.users.create')  }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-emerald-700">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nouvel utilisateur
                </a>
            </div>
        </div>

        {{-- Composant Livewire --}}
            @livewire('users-table')
    </div>
</div>
@endsection
