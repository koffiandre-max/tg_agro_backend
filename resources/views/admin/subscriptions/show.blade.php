@extends('layouts.app')

@section('page-title', 'Détails de l\'Abonnement')

@section('content')
@php
    $typeColors = [
        'basic' => 'bg-gray-100 text-gray-700',
        'standard' => 'bg-blue-100 text-blue-700',
        'premium' => 'bg-amber-100 text-amber-700',
    ];
    $typeColor = $typeColors[$subscription->type] ?? 'bg-gray-100 text-gray-700';

    $statusColors = [
        'active' => 'bg-green-100 text-green-700',
        'expired' => 'bg-red-100 text-red-700',
        'cancelled' => 'bg-gray-100 text-gray-700',
    ];
    $statusColor = $statusColors[$subscription->status] ?? 'bg-gray-100 text-gray-700';
@endphp

<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- En-tête --}}
        <div class="mb-6">
            <a href="{{ route('admin.subscriptions.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-3 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour aux abonnements
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">Abonnement #{{ $subscription->id }}</h1>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $typeColor }}">
                    {{ ucfirst($subscription->type) }}
                </span>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusColor }}">
                    {{ ucfirst($subscription->status) }}
                </span>
            </div>
            <p class="mt-1 text-gray-500">
                Client : <span class="font-medium text-gray-700">{{ $subscription->user->name ?? 'Client #' . $subscription->user_id }}</span>
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Détails principaux --}}
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="px-6 py-5 border-b border-gray-100">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Détails de l'abonnement</h2>
                    </div>
                    <div class="px-6 py-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Type</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900 capitalize">{{ $subscription->type ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Statut</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900 capitalize">{{ $subscription->status ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Montant</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $subscription->amount ? number_format($subscription->amount, 2) . ' ' . ($subscription->currency ?? 'FCFA') : '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Devise</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900 uppercase">{{ $subscription->currency ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Date de début</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $subscription->start_date?->format('d/m/Y') ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Date de fin</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $subscription->end_date?->format('d/m/Y') ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Méthode de paiement</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $subscription->payment_method ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Référence de paiement</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $subscription->payment_reference ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Code de paiement</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $subscription->payment_code ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Opérateur de paiement</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $subscription->payment_provider ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Téléphone de paiement</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $subscription->payment_phone ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">Prochain paiement</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $subscription->next_payment_date?->format('d/m/Y') ?? '—' }}</dd>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-1">
                            @if($subscription->auto_renew)
                                <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-0.5 text-sm text-blue-700">
                                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    Renouvellement automatique
                                </span>
                            @endif
                            @if($subscription->auto_payment)
                                <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-sm text-emerald-700">
                                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    Paiement automatique activé
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="space-y-5">
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Actions</h3>
                    <div class="space-y-2">
                        <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Modifier
                        </a>
                        @if($subscription->payment_status === 'pending_payment')
                            <form method="POST" action="{{ route('admin.subscriptions.confirm-payment', $subscription->id) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-md hover:bg-emerald-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    Confirmer le paiement
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.subscriptions.reject-payment', $subscription->id) }}" class="w-full" onsubmit="return confirm('Marquer ce paiement comme échoué ?');">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Rejeter le paiement
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.subscriptions.index') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                            Retour à la liste
                        </a>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-5">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Informations client</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Nom</span>
                            <span class="font-medium text-gray-900">{{ $subscription->user->name ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Email</span>
                            <span class="font-medium text-gray-900">{{ $subscription->user->email ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">ID client</span>
                            <span class="font-medium text-gray-900">{{ $subscription->user_id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
