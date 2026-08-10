@extends('layouts.app')

@section('page-title', 'Mes Exploitations')

@section('content')
@php
    $gradients = [
        'from-slate-100 to-slate-200',
        'from-emerald-100 to-teal-200',
        'from-indigo-100 to-violet-200',
        'from-amber-100 to-orange-200',
        'from-rose-100 to-pink-200',
        'from-sky-100 to-blue-200',
    ];
@endphp

<div class="mx-auto max-w-6xl">
    {{-- En-tête --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-[var(--color-text-primary)]">Mes Exploitations</h1>
            <p class="mt-1 text-sm text-[var(--color-text-muted)]">
                {{ $farms->count() }} exploitation{{ $farms->count() > 1 ? 's' : '' }} au total
            </p>
        </div>
        <a href="{{ route('admin.portail.index') }}"
           class="inline-flex items-center gap-1.5 rounded-md border border-[var(--color-border)] bg-[var(--color-bg-card)] px-3 py-2 text-sm font-medium text-[var(--color-text-primary)] shadow-sm transition-colors hover:bg-[var(--color-bg-hover)]">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Retour
        </a>
    </div>

    @if($farms->isEmpty())
        {{-- État vide --}}
        <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-[var(--color-border)] bg-[var(--color-bg-card)] py-24 text-center">
            <div class="mb-4 flex size-12 items-center justify-center rounded-full bg-[var(--color-border-light)] text-[var(--color-text-subtle)]">
                <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12.75a1.5 1.5 0 011.5 1.5V21M3 3a1.5 1.5 0 00-1.5 1.5V21m16.5-16.5h2.25A1.5 1.5 0 0122.5 6v15" />
                </svg>
            </div>
            <p class="text-sm font-medium text-[var(--color-text-primary)]">Aucune exploitation</p>
            <p class="mt-1 text-sm text-[var(--color-text-muted)]">Vos plantations apparaîtront ici.</p>
        </div>
    @else
        {{-- Grille de product cards --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($farms as $farm)
                @php
                    $gradient = $gradients[abs(crc32($farm->name ?? 'F')) % count($gradients)];
                    $photo = $farm->photos->first();
                    $imageUrl = $photo && $photo->photo_path ? asset('storage/' . $photo->photo_path) : null;
                @endphp

                <article class="group flex flex-col overflow-hidden rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-card)] shadow-sm transition-all hover:shadow-md">
                    {{-- Image --}}
                    <div class="relative aspect-[16/10] w-full overflow-hidden bg-[var(--color-border-light)]">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $farm->name }}"
                                 class="size-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                        @else
                            <div class="flex size-full items-center justify-center bg-gradient-to-br {{ $gradient }}">
                                <svg class="size-10 text-[var(--color-text-subtle)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12.75a1.5 1.5 0 011.5 1.5V21M3 3a1.5 1.5 0 00-1.5 1.5V21m16.5-16.5h2.25A1.5 1.5 0 0122.5 6v15" />
                                </svg>
                            </div>
                        @endif

                        {{-- Badge statut --}}
                        <div class="absolute left-3 top-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium shadow-sm {{ $farm->statusBadgeClasses() }}">
                                {{ $farm->statusLabel() }}
                            </span>
                        </div>
                    </div>

                    {{-- Corps --}}
                    <div class="flex flex-1 flex-col gap-3 p-4">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-semibold leading-none tracking-tight text-[var(--color-text-primary)]">{{ $farm->name }}</h3>
                                @if($farm->culture_type)
                                    <span class="shrink-0 rounded-md bg-[var(--color-border-light)] px-2 py-0.5 text-xs font-medium capitalize text-[var(--color-text-secondary)]">
                                        {{ $farm->culture_type }}
                                    </span>
                                @endif
                            </div>
                            @if($farm->location)
                                <p class="mt-1.5 flex items-center gap-1 text-sm text-[var(--color-text-muted)]">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $farm->location }}
                                </p>
                            @endif
                        </div>

                        {{-- Métriques --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-bg-page)] p-2.5">
                                <p class="text-[11px] font-medium uppercase tracking-wide text-[var(--color-text-muted)]">Superficie</p>
                                <p class="mt-0.5 text-sm font-semibold text-[var(--color-text-primary)]">
                                    {{ number_format($farm->total_area_hectares, 2) }} <span class="text-xs font-normal text-[var(--color-text-muted)]">ha</span>
                                </p>
                            </div>
                            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-bg-page)] p-2.5">
                                <p class="text-[11px] font-medium uppercase tracking-wide text-[var(--color-text-muted)]">Stade</p>
                                <p class="mt-0.5 truncate text-sm font-semibold capitalize text-[var(--color-text-primary)]">{{ $farm->crop_stage ?? '—' }}</p>
                            </div>
                        </div>

                        {{-- Progression --}}
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-[var(--color-text-muted)]">Progression</span>
                                <span class="font-semibold text-[var(--color-text-primary)]">{{ $farm->crop_stage_progress }}%</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-[var(--color-border-light)]">
                                <div class="h-full rounded-full bg-[var(--color-brand-600)] transition-all" style="width: {{ $farm->crop_stage_progress }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="border-t border-[var(--color-border)] p-4">
                        <div class="flex flex-wrap items-center gap-2 text-xs text-[var(--color-text-muted)]">
                            @if($farm->expected_harvest_date)
                                <span class="inline-flex items-center gap-1.5 rounded-md bg-[var(--color-border-light)] px-2 py-1">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Récolte {{ $farm->expected_harvest_date->format('d/m/Y') }}
                                </span>
                            @endif
                            @if($farm->numero_cadastral)
                                <span class="inline-flex items-center gap-1.5 rounded-md bg-[var(--color-border-light)] px-2 py-1">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    {{ $farm->numero_cadastral }}
                                </span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</div>
@endsection
