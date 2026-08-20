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
        </div>
        @livewire('subscriptions-table')
    </div>
</div>
@endsection
