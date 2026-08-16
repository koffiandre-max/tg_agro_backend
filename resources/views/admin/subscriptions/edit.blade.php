@extends('layouts.app')

@section('page-title', 'Modifier l\'Abonnement')

@section('content')
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
            <h1 class="text-2xl font-bold text-gray-900">Modifier l'abonnement</h1>
            <p class="mt-1 text-gray-500">
                Client : <span class="font-medium text-gray-700">{{ $subscription->user?->name ?? 'Client #' . $subscription->user_id }}</span>
            </p>
        </div>

        {{-- Erreurs globales --}}
        @if (isset($errors) && $errors->any())
            <div class="mb-6 px-6 py-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Il y a des erreurs dans le formulaire</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.subscriptions.update', $subscription->id) }}"
              class="bg-white border border-gray-200 rounded-lg shadow-sm">
            @csrf
            @method('PUT')

            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Détails de l'abonnement</h2>
            </div>

            <div class="px-6 py-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1.5">Type</label>
                        <select id="type" name="type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Sélectionner</option>
                            <option value="basic" {{ old('type', $subscription->type) === 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="standard" {{ old('type', $subscription->type) === 'standard' ? 'selected' : '' }}>Standard</option>
                            <option value="premium" {{ old('type', $subscription->type) === 'premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                        @error('type') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Statut</label>
                        <select id="status" name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                            <option value="">Sélectionner</option>
                            <option value="active" {{ old('status', $subscription->status) === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="expired" {{ old('status', $subscription->status) === 'expired' ? 'selected' : '' }}>Expiré</option>
                            <option value="cancelled" {{ old('status', $subscription->status) === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        </select>
                        @error('status') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">Montant</label>
                        <input type="number" step="0.01" min="0" id="amount" name="amount"
                               value="{{ old('amount', $subscription->amount) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="0.00">
                        @error('amount') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-1.5">Devise</label>
                        <input type="text" id="currency" name="currency" maxlength="3"
                               value="{{ old('currency', $subscription->currency) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 uppercase focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="EUR">
                        @error('currency') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1.5">Date de début</label>
                        <input type="date" id="start_date" name="start_date"
                               value="{{ old('start_date', $subscription->start_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        @error('start_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1.5">Date de fin</label>
                        <input type="date" id="end_date" name="end_date"
                               value="{{ old('end_date', $subscription->end_date?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600">
                        @error('end_date') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1.5">Méthode de paiement</label>
                        <input type="text" id="payment_method" name="payment_method"
                               value="{{ old('payment_method', $subscription->payment_method) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: Carte bancaire, Mobile Money...">
                        @error('payment_method') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="payment_reference" class="block text-sm font-medium text-gray-700 mb-1.5">Référence de paiement</label>
                        <input type="text" id="payment_reference" name="payment_reference"
                               value="{{ old('payment_reference', $subscription->payment_reference) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
                               placeholder="Ex: TX-123456">
                        @error('payment_reference') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-1">
                    <input type="checkbox" id="auto_renew" name="auto_renew" value="1"
                           {{ old('auto_renew', $subscription->auto_renew) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                    <label for="auto_renew" class="text-sm text-gray-700">Renouvellement automatique</label>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex items-center justify-end gap-3">
                <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">Annuler</a>
                <button type="submit"
                        class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 active:bg-blue-800 transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
