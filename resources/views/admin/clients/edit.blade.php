@extends('layouts.app')

@section('page-title', 'Modifier le Client')

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32($client->user->name ?? 'C')) % count($colors)];
@endphp

{{-- Header --}}
<div class="mb-8">
    <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4 transition-colors">
        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Retour à la liste
    </a>
    <h1 class="text-2xl font-bold text-gray-900">Modifier le Client</h1>
    <p class="mt-1 text-gray-500">Modifiez les informations de {{ $client->user->name ?? 'Client' }}</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2  gap-6">
    <div class="xl:col-span-3">
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Informations du client</h3>
            </x-slot:header>
            <form action="{{ route('admin.clients.update', $client->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nom complet <span class="text-red-600">*</span></label>
                        <input type="text" id="name" name="name" required value="{{ old('name', $client->user->name ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Adresse email <span class="text-red-600">*</span></label>
                        <input type="email" id="email" name="email" required value="{{ old('email', $client->user->email ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Téléphone</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $client->phone ?? $client->user->phone ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="country_of_residence" class="block text-sm font-medium text-gray-700 mb-1.5">Pays de résidence</label>
                        <input type="text" id="country_of_residence" name="country_of_residence" value="{{ old('country_of_residence', $client->country_of_residence ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('country_of_residence')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="country_of_origin" class="block text-sm font-medium text-gray-700 mb-1.5">Pays d'origine</label>
                        <input type="text" id="country_of_origin" name="country_of_origin" value="{{ old('country_of_origin', $client->country_of_origin ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('country_of_origin')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="city_of_residence" class="block text-sm font-medium text-gray-700 mb-1.5">Ville de résidence</label>
                        <input type="text" id="city_of_residence" name="city_of_residence" value="{{ old('city_of_residence', $client->city_of_residence ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('city_of_residence')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="subscription_type" class="block text-sm font-medium text-gray-700 mb-1.5">Type d'abonnement</label>
                        <select id="subscription_type" name="subscription_type"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="basic" {{ old('subscription_type', $client->subscription_type ?? '') === 'basic' ? 'selected' : '' }}>Basique</option>
                            <option value="standard" {{ old('subscription_type', $client->subscription_type ?? '') === 'standard' ? 'selected' : '' }}>Standard</option>
                            <option value="premium" {{ old('subscription_type', $client->subscription_type ?? '') === 'premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                        @error('subscription_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="subscription_expires_at" class="block text-sm font-medium text-gray-700 mb-1.5">Expiration abonnement</label>
                        <input type="date" id="subscription_expires_at" name="subscription_expires_at" value="{{ old('subscription_expires_at', $client->subscription_expires_at ? $client->subscription_expires_at->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('subscription_expires_at')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="total_investment" class="block text-sm font-medium text-gray-700 mb-1.5">Investissement total (€)</label>
                        <input type="number" step="0.01" min="0" id="total_investment" name="total_investment" value="{{ old('total_investment', $client->total_investment ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @error('total_investment')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Mot de passe</label>
                        <input type="password" id="password" name="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Laisser vide pour ne pas changer">
                        @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1.5">Notes</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-y"
                                  placeholder="Informations complémentaires...">{{ old('notes', $client->notes ?? '') }}</textarea>
                        @error('notes')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <x-ui.btn variant="secondary" href="{{ route('admin.clients.index') }}">Annuler</x-ui.btn>
                    <x-ui.btn type="submit" variant="primary" icon="edit">Mettre à jour</x-ui.btn>
                </div>
            </form>
        </x-ui.card>
    </div>

    {{-- <div class="space-y-6">
        <x-ui.card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Aperçu</h3>
            </x-slot:header>
            <div class="text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl text-2xl font-bold text-white shadow-sm" style="background:{{ $profileColor }}">
                    {{ strtoupper(substr($client->user->name ?? 'C', 0, 1)) }}
                </div>
                <p class="mt-3 text-sm font-medium text-gray-500">Avatar du client</p>
            </div>
        </x-ui.card>

        <x-ui.card>
            <x-slot:header>
                <h3 class="text-base font-semibold text-gray-900">Informations</h3>
            </x-slot:header>
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-gray-500">Abonnement</dt>
                    <dd class="font-medium text-gray-900 capitalize">{{ $client->subscription_type ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-gray-500">Investissement</dt>
                    <dd class="font-medium text-gray-900">{{ $client->total_investment ? number_format($client->total_investment, 2) . ' €' : '—' }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-gray-500">Inscrit le</dt>
                    <dd class="font-medium text-gray-900">{{ $client->user->created_at?->format('d/m/Y') ?? '—' }}</dd>
                </div>
            </dl>
        </x-ui.card>
    </div> --}}
</div>
@endsection
