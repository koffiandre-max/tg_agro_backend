@extends('layouts.app')

@section('title', 'Modifier une Saisie')
@section('page-title', 'Modifier une Saisie de Données')

@section('content')
<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('admin.technitian.data.update', $entry->id) }}" id="dataEntryForm">
        @csrf
        @method('PUT')

        {{-- Ferme --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z"/>
                </svg>
                Exploitation
            </h3>
            <select name="farm_id" id="farm_id" required
                class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-purple-500 focus:ring-purple-500">
                <option value="">Sélectionner une exploitation</option>
                @foreach($farms as $farm)
                    <option value="{{ $farm->id }}" {{ old('farm_id', $entry->farm_id) == $farm->id ? 'selected' : '' }}>
                        {{ $farm->name }}
                    </option>
                @endforeach
            </select>
            @error('farm_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Client --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Client
            </h3>
            <select name="client_id" id="client_id" required
                class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-purple-500 focus:ring-purple-500">
                <option value="">Sélectionner un client</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id', $entry->client_id) == $client->id ? 'selected' : '' }}>
                        {{ $client->code ?? ('Client #'.$client->id) }}
                    </option>
                @endforeach
            </select>
            @error('client_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Stade Cultural --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A9 9 0 006 18c1.052 0 2.062-.18 3-.512m0-13.042A8.967 8.967 0 0118 3.75c1.052 0 2.062.18 3 .512v14.25A9 9 0 0118 18c-1.052 0-2.062-.18-3-.512"/>
                </svg>
                Stade Cultural
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Stade</label>
                    <input type="text" name="crop_stage" value="{{ old('crop_stage', $entry->crop_stage) }}"
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-purple-500 focus:ring-purple-500"
                        placeholder="Ex: Floraison, Croissance...">
                    @error('crop_stage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Progression (%)</label>
                    <input type="number" name="crop_stage_progress" value="{{ old('crop_stage_progress', $entry->crop_stage_progress) }}" min="0" max="100"
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-purple-500 focus:ring-purple-500"
                        placeholder="0 - 100">
                    @error('crop_stage_progress')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Conditions --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 006.75 15z"/>
                </svg>
                Conditions & Récolte
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Météo</label>
                    <input type="text" name="weather_conditions" value="{{ old('weather_conditions', $entry->weather_conditions) }}"
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-purple-500 focus:ring-purple-500"
                        placeholder="Ex: Ensoleillé, Pluvieux...">
                    @error('weather_conditions')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1.5">Date de récolte prévue</label>
                    <input type="date" name="estimated_harvest_date" value="{{ old('estimated_harvest_date', $entry->estimated_harvest_date?->format('Y-m-d')) }}"
                        class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-purple-500 focus:ring-purple-500">
                    @error('estimated_harvest_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Intrants --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19 14.5M14.25 3.104c.251.023.501.05.75.082M19 14.5l-4.5 4.5m0 0l-4.5-4.5m4.5 4.5V19.5"/>
                </svg>
                Intrants Utilisés
            </h3>
            <textarea name="inputs_used" rows="2" class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-purple-500 focus:ring-purple-500" placeholder="Engrais, pesticides, semences...">{{ old('inputs_used', $entry->inputs_used) }}</textarea>
            @error('inputs_used')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Observations --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 mb-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/>
                </svg>
                Observations
            </h3>
            <textarea name="observations" rows="4" class="block w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-purple-500 focus:ring-purple-500" placeholder="Notes et observations sur la parcelle...">{{ old('observations', $entry->observations) }}</textarea>
            @error('observations')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Bouton de soumission --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.technitian.data.index') }}" class="rounded-full border border-gray-200 bg-white px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Annuler
            </a>
            <button type="submit" class="rounded-full bg-purple-600 px-6 py-3 text-sm font-medium text-white hover:bg-purple-700 transition-colors shadow-sm">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
