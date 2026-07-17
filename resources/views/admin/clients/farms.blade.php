@extends('layouts.app')

@section('page-title', 'Exploitations du Client')

@section('content')
@php
    // Dégradés modernes pour les illustrations d'absence de photo
    $gradients = [
        ['from' => '#6366f1', 'to' => '#4f46e5'], // Indigo
        ['from' => '#8b5cf6', 'to' => '#7c3aed'], // Violet
        ['from' => '#ec4899', 'to' => '#db2777'], // Rose
        ['from' => '#14b8a6', 'to' => '#0d9488'], // Turquoise
        ['from' => '#f59e0b', 'to' => '#d97706'], // Ambre
        ['from' => '#10b981', 'to' => '#059669'], // Émeraude
        ['from' => '#3b82f6', 'to' => '#2563eb'], // Bleu
        ['from' => '#f97316', 'to' => '#ea580c'], // Orange
    ];
@endphp

    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.clients.show', $client->id) }}" class="group inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 mb-2 transition-colors duration-150">
                <svg class="w-4 h-4 mr-1.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour au client
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">
                Exploitations de <span class="text-indigo-600">{{ $client->user->name ?? 'Client' }}</span>
            </h1>
            <p class="mt-1.5 text-sm text-slate-500 flex items-center gap-1.5">
                <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                {{ $client->farms->count() }} exploitation(s) associée(s)
            </p>
        </div>
    </div>

    @if($client->farms->isEmpty())
        {{-- Empty State --}}
        <div class="mx-auto my-12 max-w-lg rounded-2xl border border-dashed border-slate-300 p-12 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 text-slate-400 mb-4">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12.75a1.5 1.5 0 011.5 1.5V21M3 3a1.5 1.5 0 00-1.5 1.5V21m16.5-16.5h2.25A1.5 1.5 0 0122.5 6v15" />
                </svg>
            </div>
            <h3 class="text-sm font-semibold text-slate-900">Aucune exploitation</h3>
            <p class="mt-1 text-sm text-slate-500">Ce client n'a pas encore d'exploitation associée.</p>
        </div>
    @else
        {{-- Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($client->farms as $farm)
                @php
                    $gradientIndex = abs(crc32($farm->name ?? 'F')) % count($gradients);
                    $selectedGradient = $gradients[$gradientIndex];
                    
                    $photo = $farm->photos->first();
                    $imageUrl = $photo && $photo->photo_path
                        ? asset('storage/' . $photo->photo_path)
                        : null;
                    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
                @endphp
                
                <div class="group bg-white rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col overflow-hidden">
                    
                    {{-- Image de couverture --}}
                    <div class="relative h-48 w-full overflow-hidden bg-slate-100">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $farm->name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300 ease-out">
                        @else
                            <div class="w-full h-full flex items-center justify-center transform group-hover:scale-105 transition-transform duration-300 ease-out" 
                                 style="background: linear-gradient(135deg, {{ $selectedGradient['from'] }} 0%, {{ $selectedGradient['to'] }} 100%)">
                                <svg class="w-12 h-12 text-white/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12.75a1.5 1.5 0 011.5 1.5V21M3 3a1.5 1.5 0 00-1.5 1.5V21m16.5-16.5h2.25A1.5 1.5 0 0122.5 6v15" />
                                </svg>
                            </div>
                        @endif

                        {{-- Badges flottants --}}
                        <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2 pointer-events-none">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold backdrop-blur-md shadow-sm bg-white/95 text-slate-800 border border-slate-200/50 capitalize {{ $farm->statusBadgeClasses() }}">
                                {{ $farm->status }}
                            </span>
                            @if($farm->culture_type)
                                <span class="inline-flex items-center rounded-full bg-slate-900/85 backdrop-blur-md px-2.5 py-1 text-xs font-semibold text-white tracking-wide capitalize">
                                    {{ $farm->culture_type }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Corps du contenu --}}
                    <div class="p-5 flex-1 flex flex-col">
                        
                        {{-- Titre et localisation --}}
                        <div>
                            <h3 class="text-lg font-bold text-slate-950 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                {{ $farm->name }}
                            </h3>
                            <p class="mt-1.5 text-xs text-slate-500 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">{{ $farm->location }}</span>
                            </p>
                        </div>

                        {{-- Section Métriques --}}
                        <div class="grid grid-cols-2 gap-4 py-3.5 border-y border-slate-100 my-4">
                            <div>
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Superficie</span>
                                <span class="text-base font-extrabold text-slate-900 mt-0.5 inline-block">
                                    {{ number_format($farm->total_area_hectares, 2) }} <span class="text-xs font-medium text-slate-500">ha</span>
                                </span>
                            </div>
                            @if($farm->crop_stage)
                                <div>
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stade actuel</span>
                                    <span class="text-xs font-semibold text-indigo-600 mt-1 inline-flex items-center px-2 py-0.5 rounded bg-indigo-50 border border-indigo-100/50 capitalize truncate max-w-full">
                                        {{ $farm->crop_stage }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Progression --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-500">Progression</span>
                                <span class="font-bold text-slate-950">{{ $farm->crop_stage_progress }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-600 h-full rounded-full transition-all duration-500" style="width: {{ $farm->crop_stage_progress }}%"></div>
                            </div>
                        </div>

                        {{-- Métadonnées additionnelles --}}
                        @if($farm->expected_harvest_date)
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200/60 px-2.5 py-1 text-xs font-medium text-slate-600">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Récolte : {{ $farm->expected_harvest_date->format('d/m/Y') }}
                                </span>
                            </div>
                        @endif

                        {{-- Actions Administrateur --}}
                        @if($isAdmin)
                            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-2">
                                <a href="{{ route('admin.farms.edit', $farm->id) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Modifier
                                </a>
                                <a href="{{ route('admin.farms.show', $farm->id) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 hover:shadow transition-all shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Détails
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection