@extends('layouts.app')

@section('title', 'Mes Saisies de Données')
@section('page-title', 'Mes Saisies de Données Agronomiques')

@section('content')
@php
    $isReadOnly = isset($subscriptionExpired) && $subscriptionExpired;
@endphp

<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($isReadOnly)
            <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="h-5 w-5 text-amber-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-amber-800">Abonnement expiré ou inactif</p>
                    <p class="text-xs text-amber-700 mt-1">Vous êtes en mode consultation uniquement. Veuillez renouveler votre abonnement pour créer ou modifier des données.</p>
                </div>
            </div>
        @endif

        {{-- En-tête --}}
        <div class="mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mes Saisies de Données</h1>
                <p class="mt-2 text-sm text-gray-600">Consultez les saisies validées par les techniciens</p>
            </div>
        </div>

        @if($entries->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-semibold text-gray-900">Aucune saisie disponible</h3>
                <p class="mt-1 text-sm text-gray-500">Les saisies validées apparaîtront ici.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 {{ $isReadOnly ? 'opacity-60 grayscale' : '' }}">
                @foreach($entries as $entry)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col">
                        <div class="flex items-start justify-between">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Validé
                            </span>
                        </div>

                        <h3 class="mt-4 text-base font-semibold text-gray-900 leading-snug">{{ $entry->farm?->name ?? 'Exploitation' }}</h3>

                        <dl class="mt-4 space-y-2 text-sm">
                            @if($entry->crop_stage)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Stade cultural</dt>
                                <dd class="font-medium text-gray-900">{{ $entry->crop_stage }}</dd>
                            </div>
                            @endif

                            @if($entry->crop_stage_progress !== null)
                            <div class="flex justify-between items-center">
                                <dt class="text-gray-500">Progression</dt>
                                <dd class="font-medium text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $entry->crop_stage_progress }}%"></div>
                                        </div>
                                        <span>{{ $entry->crop_stage_progress }}%</span>
                                    </div>
                                </dd>
                            </div>
                            @endif

                            @if($entry->weather_conditions)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Météo</dt>
                                <dd class="font-medium text-gray-900">{{ $entry->weather_conditions }}</dd>
                            </div>
                            @endif

                            @if($entry->estimated_harvest_date)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Date récolte prévue</dt>
                                <dd class="font-medium text-gray-900">{{ $entry->estimated_harvest_date->format('d/m/Y') }}</dd>
                            </div>
                            @endif

                            <div class="flex justify-between">
                                <dt class="text-gray-500">Validé le</dt>
                                <dd class="font-medium text-gray-900">{{ $entry->validated_at?->format('d/m/Y') ?? '—' }}</dd>
                            </div>
                        </dl>

                        @if($entry->observations)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs font-medium text-gray-500">Observations</p>
                            <p class="mt-1 text-sm text-gray-700 line-clamp-3">{{ $entry->observations }}</p>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection