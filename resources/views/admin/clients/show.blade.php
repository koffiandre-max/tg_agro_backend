@extends('layouts.app')

@section('title', $client->code ?? $client->user->name ?? 'Client')

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32($client->user->name ?? 'C')) % count($colors)];

    $subscriptionColors = [
        'basic' => ['label' => 'Basique', 'text' => 'text-slate-700', 'bg' => 'bg-slate-100', 'ring' => 'ring-slate-600/20'],
        'standard' => ['label' => 'Standard', 'text' => 'text-indigo-700', 'bg' => 'bg-indigo-50/80', 'ring' => 'ring-indigo-600/20'],
        'premium' => ['label' => 'Premium', 'text' => 'text-amber-700', 'bg' => 'bg-amber-50/80', 'ring' => 'ring-amber-600/20'],
        'vip' => ['label' => 'VIP', 'text' => 'text-amber-700', 'bg' => 'bg-amber-50/80', 'ring' => 'ring-amber-600/20'],
    ];
    $subscription = $subscriptionColors[$client->subscription_type] ?? ['label' => ucfirst($client->subscription_type ?? '—'), 'text' => 'text-slate-700', 'bg' => 'bg-slate-100', 'ring' => 'ring-slate-600/20'];
@endphp



<div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="#" onClick= "history.back()" class="group inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 mb-2 transition-colors duration-150">
                <svg class="w-4 h-4 mr-1.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour au client
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">
                Information sur <span class="text-indigo-600">@if(auth()->user()->type === 'admin') {{ $client->user->name ?? 'Client' }} @else {{ $client->code ?? '-' }} @endif</span>
            </h1>
            {{-- <p class="mt-1.5 text-sm text-slate-500 flex items-center gap-1.5">
                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ $client->farms->count() }} exploitation(s) associée(s)
            </p> --}}
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <x-ui.card>
            <div class="text-center">
                @if($client->avatar)
                    <img src="{{ asset('storage/' . $client->avatar) }}" alt="{{ $client->user->name ?? 'Client' }}" class="w-32 h-32 rounded-full mx-auto object-cover">
                @else
                    <div class="w-32 h-32 rounded-full mx-auto flex items-center justify-center text-white text-3xl font-bold" style="background: {{ $profileColor }}">
                        {{ strtoupper(substr($client->user->name ?? 'C', 0, 1)) }}
                    </div>
                @endif
                <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $client->user->name ?? 'Client' }}</h2>
                <div class="flex items-center justify-center gap-2 mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $subscription['bg'] }} {{ $subscription['text'] }} {{ $subscription['ring'] }} ring-1">
                        {{ $subscription['label'] }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $client->user->is_active ?? true ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                        {{ ($client->user->is_active ?? true) ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                @if($client->code)
                    <p class="mt-2 text-xs text-gray-500">Code: <span class="font-mono font-semibold text-gray-700">{{ $client->code }}</span></p>
                @endif
            </div>

            <div class="mt-6 space-y-3">
                @if($client->user->email)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-envelope w-4 text-gray-400"></i>
                        {{ $client->user->email }}
                    </div>
                @endif
                @if($client->phone)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-phone w-4 text-gray-400"></i>
                        {{ $client->phone }}
                    </div>
                @endif
                @if($client->city_of_residence)
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-map-marker-alt w-4 text-gray-400"></i>
                        {{ $client->city_of_residence }}, {{ $client->country_of_residence }}
                    </div>
                @endif
            </div>
        </x-ui.card>

         @if($client->notes)
            <div class="mt-5">
                <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Notes & Observations</h3>
                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $client->notes }}</p>
            </x-ui.card>
            </div>
        @endif
    </div>

    <div class="lg:col-span-2 space-y-6">
       

        {{-- Métriques --}}
        @if(is_admin())

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-ui.card class="flex items-center gap-4">
                <div class="p-3.5 bg-emerald-50 text-emerald-600 rounded-2xl shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Investissement engagé</span>
                    <span class="text-2xl font-black text-gray-900 mt-0.5 inline-block">
                        {{ $client->total_investment ? number_format($client->total_investment, 2) . ' €' : '0.00 €' }}
                    </span>
                </div>
            </x-ui.card>

            <x-ui.card class="flex items-center gap-4">
                <div class="p-3.5 bg-indigo-50 text-indigo-600 rounded-2xl shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider text-gray-400">Fin d'Abonnement</span>
                    <span class="text-base font-extrabold text-gray-900 mt-1 inline-block">
                        {{ $client->subscription_expires_at?->format('d/m/Y') ?? 'Non planifiée' }}
                    </span>
                </div>
            </x-ui.card>
        </div>
        @endif


        {{-- Fermes --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Fermes assignées</h3>
            </div>
            @if($client->farms->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($client->farms as $farm)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">{{ $farm->name }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $farm->location ?? '—' }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $farm->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($farm->status === 'fallow' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($farm->status ?? '—') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">Aucune ferme assignée.</p>
            @endif
        </x-ui.card>

        {{-- Rapports --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Rapports d'évaluation</h3>
            </div>
            @if($client->reports->count() > 0)
                <div class="space-y-3">
                    @foreach($client->reports as $report)
                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Rapport du {{ $report->created_at?->format('d/m/Y') ?? '—' }}</p>
                                    <p class="text-xs text-gray-500">{{ $report->report_type ?? '—' }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">{{ $report->is_validated ? 'Validé' : 'En attente' }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-8">Aucun rapport enregistré.</p>
            @endif
        </x-ui.card>
    </div>
</div>
</div>
@endsection
