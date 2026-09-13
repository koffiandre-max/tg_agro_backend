@extends('layouts.app')

@section('title', 'Saisir des Données')
@section('page-title', 'Saisir des Données Agronomiques')

@php
    // Options pour les composants x-select
    $farmOptions = $farms->mapWithKeys(fn ($farm) => [$farm->id => $farm->name])->all();
    $clientOptions = $clients->mapWithKeys(fn ($client) => [$client->id => $client->code ?? ('Client #'.$client->id)])->all();

    // Stades culturaux prédéfinis (+ valeur saisie précédemment si libre)
    $cropStageOptions = [
        'Semis',
        'Germination / Levée',
        'Croissance végétative',
        'Repiquage',
        'Floraison',
        'Fructification',
        'Maturation',
        'Récolte',
    ];
    if ($stage = old('crop_stage')) {
        if (! in_array($stage, $cropStageOptions)) {
            $cropStageOptions[] = $stage;
        }
    }

    // Conditions météo prédéfinies (+ valeur libre précédente si présente)
    $weatherOptions = [
        'Ensoleillé',
        'Partiellement nuageux',
        'Nuageux',
        'Pluvieux',
        'Orageux',
        'Venteux',
        'Sec',
    ];
    if ($weather = old('weather_conditions')) {
        if (! in_array($weather, $weatherOptions)) {
            $weatherOptions[] = $weather;
        }
    }
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 pb-12">
    <form method="POST" action="{{ route('admin.technitian.data.store') }}" id="dataEntryForm">
        @csrf

        {{-- Section 1 : Informations Générales --}}
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm p-6 mb-6 space-y-5">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2 border-b border-slate-100 pb-3 shrink-0">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Informations Générales
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Sélection de l'Exploitation --}}
                <div class="space-y-1.5">
                    <label for="farm_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Exploitation agricole <span class="text-red-500">*</span>
                    </label>
                    <x-select name="farm_id" id="farm_id" :options="$farmOptions"
                              placeholder="Sélectionner une exploitation" error="farm_id" />
                    @error('farm_id')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sélection du Client --}}
                <div class="space-y-1.5">
                    <label for="client_id" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Client associé <span class="text-red-500">*</span>
                    </label>
                    <x-select name="client_id" id="client_id" :options="$clientOptions"
                              placeholder="Sélectionner un client" error="client_id" />
                    @error('client_id')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section 2 : Indicateurs de Suivi & Conditions --}}
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm p-6 mb-6 space-y-5">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2 border-b border-slate-100 pb-3 shrink-0">
                <svg class="w-4.5 h-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                Indicateurs & Conditions de suivi
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Stade Cultural --}}
                <div class="space-y-1.5">
                    <label for="crop_stage" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stade Cultural</label>
                    <x-select name="crop_stage" id="crop_stage" :options="$cropStageOptions"
                              placeholder="Sélectionner un stade..." error="crop_stage" />
                    @error('crop_stage')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Progression --}}
                <div class="space-y-1.5">
                    <label for="crop_stage_progress" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Progression (%)</label>
                    <input type="number" name="crop_stage_progress" id="crop_stage_progress" value="{{ old('crop_stage_progress') }}" min="0" max="100"
                           class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold"
                           placeholder="0 - 100">
                    @error('crop_stage_progress')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Météo --}}
                <div class="space-y-1.5">
                    <label for="weather_conditions" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Conditions Météo</label>
                    <x-select name="weather_conditions" id="weather_conditions" :options="$weatherOptions"
                              placeholder="Sélectionner la météo..." error="weather_conditions" />
                    @error('weather_conditions')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date de récolte --}}
                <div class="space-y-1.5">
                    <label for="estimated_harvest_date" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date de récolte prévue</label>
                    <input type="date" name="estimated_harvest_date" id="estimated_harvest_date" value="{{ old('estimated_harvest_date') }}"
                           class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-semibold">
                    @error('estimated_harvest_date')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section 3 : Intrants & Observations --}}
        <div class="rounded-2xl bg-white border border-slate-200/80 shadow-sm p-6 mb-6 space-y-5">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2 border-b border-slate-100 pb-3 shrink-0">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Intrants & Observations de Parcelle
            </h3>

            <div class="space-y-4">
                {{-- Intrants --}}
                <div class="space-y-1.5">
                    <label for="inputs_used" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Intrants utilisés</label>
                    <textarea name="inputs_used" id="inputs_used" rows="2" 
                              class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium" 
                              placeholder="Engrais, pesticides, semences spécifiques utilisés...">{{ old('inputs_used') }}</textarea>
                    @error('inputs_used')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Observations --}}
                <div class="space-y-1.5">
                    <label for="observations" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Observations complémentaires</label>
                    <textarea name="observations" id="observations" rows="4" 
                              class="block w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium" 
                              placeholder="Notes et remarques observées sur la parcelle lors de la visite...">{{ old('observations') }}</textarea>
                    @error('observations')
                        <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Actions de Validation --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.technitian.data.index') }}" class="px-5 py-2.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                Annuler
            </a>
            <button type="submit" class="px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-all shadow-sm shadow-indigo-600/10">
                Enregistrer les données
            </button>
        </div>
    </form>
</div>
@endsection