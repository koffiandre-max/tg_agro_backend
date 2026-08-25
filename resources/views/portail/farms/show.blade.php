@extends('layouts.app')

@section('page-title', 'Détails de l\'exploitation')

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
    $profileColor = $gradients[abs(crc32($farm->name ?? 'F')) % count($gradients)];
    $photo = $farm->photos?->first();
    $imageUrl = $photo && $photo->photo_path ? asset('storage/' . $photo->photo_path) : null;
@endphp

<div class="space-y-6 p-6">
    {{-- En-tête --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.portail.farms') }}"
               class="inline-flex items-center gap-1.5 rounded-md border border-[var(--color-border)] bg-[var(--color-bg-card)] px-3 py-2 text-sm font-medium text-[var(--color-text-primary)] shadow-sm transition-colors hover:bg-[var(--color-bg-hover)]">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour
            </a>
            <h1 class="text-2xl font-semibold tracking-tight text-[var(--color-text-primary)] mt-3">
                {{ $farm->name }}
            </h1>
            <p class="mt-1 text-sm text-[var(--color-text-muted)]">
                {{ $farm->location ?? 'Localisation inconnue' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        {{-- Colonne gauche : Photo et statut --}}
        <div class="space-y-5">
            <div class="overflow-hidden rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-card)] shadow-sm">
                <div class="h-48 w-full bg-gradient-to-br {{ $profileColor }}">
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="{{ $farm->name }}" class="size-full object-cover">
                    @endif
                </div>
                <div class="p-4 space-y-3">
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $farm->statusBadgeClasses() }}">
                        {{ $farm->statusLabel() }}
                    </span>
                    @if($farm->culture_type)
                        <span class="inline-flex items-center rounded-md bg-[var(--color-border-light)] px-2.5 py-1 text-xs font-medium capitalize text-[var(--color-text-secondary)]">
                            {{ $farm->culture_type }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-5 shadow-sm space-y-3 text-sm text-[var(--color-text-primary)]">
                <p class="flex items-center justify-between">
                    <span class="text-[var(--color-text-muted)]">Superficie</span>
                    <span class="font-semibold">{{ number_format($farm->total_area_hectares, 2) }} ha</span>
                </p>
                <p class="flex items-center justify-between">
                    <span class="text-[var(--color-text-muted)]">Stade</span>
                    <span class="font-semibold capitalize">{{ $farm->crop_stage ?? '—' }}</span>
                </p>
                <p class="flex items-center justify-between">
                    <span class="text-[var(--color-text-muted)]">Progression</span>
                    <span class="font-semibold">{{ $farm->crop_stage_progress ?? 0 }}%</span>
                </p>
                <div class="h-2 w-full overflow-hidden rounded-full bg-[var(--color-border-light)]">
                    <div class="h-full rounded-full bg-[var(--color-brand-600)] transition-all" style="width: {{ $farm->crop_stage_progress ?? 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- Colonne droite : Détails --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-5 shadow-sm space-y-3 text-sm">
                <p class="flex items-center justify-between">
                    <span class="text-[var(--color-text-muted)]">Propriétaire</span>
                    <span class="font-semibold">{{ $farm->user->name ?? '—' }}</span>
                </p>
                <p class="flex items-center justify-between">
                    <span class="text-[var(--color-text-muted)]">Technicien</span>
                    <span class="font-semibold">{{ $farm->assignedTechnician->user->name ?? '—' }}</span>
                </p>
                @if($farm->expected_harvest_date)
                    <p class="flex items-center justify-between">
                        <span class="text-[var(--color-text-muted)]">Récolte prévue</span>
                        <span class="font-semibold">{{ $farm->expected_harvest_date?->format('d/m/Y') }}</span>
                    </p>
                @endif
                @if($farm->last_visit_date)
                    <p class="flex items-center justify-between">
                        <span class="text-[var(--color-text-muted)]">Dernière visite</span>
                        <span class="font-semibold">{{ $farm->last_visit_date?->format('d/m/Y') }}</span>
                    </p>
                @endif
            </div>

            @if($farm->notes)
                <div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-5 shadow-sm">
                    <h3 class="text-sm font-semibold text-[var(--color-text-primary)] mb-2">Notes</h3>
                    <p class="text-sm text-[var(--color-text-muted)] whitespace-pre-line">{{ $farm->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
