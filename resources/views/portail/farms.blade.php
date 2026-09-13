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
    $isReadOnly = isset($subscriptionExpired) && $subscriptionExpired;
@endphp

<div class="space-y-6 p-6">
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

    @if($isReadOnly)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
            <svg class="h-5 w-5 text-amber-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-800">Abonnement expiré ou inactif</p>
                <p class="text-xs text-amber-700 mt-1">Vous êtes en mode consultation uniquement. Veuillez renouveler votre abonnement pour créer ou modifier des données.</p>
            </div>
        </div>
    @endif

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

        
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

           
            @foreach($farms as $farm)
                @php
                    $gradient = $gradients[abs(crc32($farm->name ?? 'F')) % count($gradients)];
                    $photo = $farm->photos->first();
                    $imageUrl = $photo && $photo->photo_path ? asset('storage/' . $photo->photo_path) : null;
                @endphp

                <article class="group flex flex-col overflow-hidden rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-card)] shadow-sm transition-all hover:shadow-md md:flex-row {{ $isReadOnly ? 'opacity-60 grayscale' : '' }}">
                    {{-- Image à gauche --}}
                    <div class="relative h-48 w-full shrink-0 overflow-hidden bg-[var(--color-border-light)] md:h-auto md:w-48">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $farm->name }}"
                                 class="size-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                        @else
                            <div class="flex size-full items-center justify-center bg-gradient-to-br {{ $gradient }}">
                                <img src="{{ asset('img/farms_2.jpg') }}" alt="{{ $farm->name }}"
                                 class="size-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                            </div>
                        @endif

                        {{-- Badge statut --}}
                        <div class="absolute left-3 top-3">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium shadow-sm {{ $farm->statusBadgeClasses() }}">
                                {{ $farm->statusLabel() }}
                            </span>
                        </div>
                    </div>

                    {{-- Contenu à droite --}}
                    <div class="flex flex-1 flex-col justify-between p-4 leading-normal">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <h5 class="text-lg font-bold tracking-tight text-[var(--color-text-primary)]">{{ $farm->name }}</h5>
                                @if($farm->culture_type)
                                    <span class="shrink-0 rounded-md bg-[var(--color-border-light)] px-2 py-0.5 text-xs font-medium capitalize text-[var(--color-text-secondary)]">
                                        {{ $farm->culture_type }}
                                    </span>
                                @endif
                            </div>

                            @if($farm->location)
                                <p class="mt-1 flex items-center gap-1 text-sm text-[var(--color-text-muted)]">
                                    <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ $farm->location }}
                                </p>
                            @endif

                            {{-- Métriques --}}
                            <div class="mt-3 grid grid-cols-2 gap-2">
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
                            <div class="mt-3 space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-[var(--color-text-muted)]">Progression</span>
                                    <span class="font-semibold text-[var(--color-text-primary)]">{{ $farm->crop_stage_progress }}%</span>
                                </div>
                                <div class="h-2 w-full overflow-hidden rounded-full bg-[var(--color-border-light)]">
                                    <div class="h-full rounded-full bg-[var(--color-brand-600)] transition-all" style="width: {{ $farm->crop_stage_progress }}%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="mt-4 flex items-center justify-end gap-2">
                            {{-- Voir --}}
                            <a href="{{ route('admin.portail.farms.show', $farm->id) }}" title="Voir"
                               class="inline-flex size-9 items-center justify-center rounded-lg border border-[var(--color-border)] bg-[var(--color-bg-page)] text-[var(--color-text-secondary)] shadow-sm transition-colors hover:bg-[var(--color-brand-600)] hover:text-white focus:outline-none focus:ring-4 focus:ring-[var(--color-brand-50)]">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>

                            @if(!$isReadOnly)
                                {{-- Éditer --}}
                                <a href="{{ route('admin.portail.farms.edit', $farm->id) }}" title="Éditer"
                                   class="inline-flex size-9 items-center justify-center rounded-lg border border-[var(--color-border)] bg-[var(--color-bg-page)] text-[var(--color-text-secondary)] shadow-sm transition-colors hover:bg-[var(--color-brand-600)] hover:text-white focus:outline-none focus:ring-4 focus:ring-[var(--color-brand-50)]">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                    </svg>
                                </a>

                                {{-- Supprimer --}}
                                <button type="button" title="Supprimer" data-delete-url="{{ route('admin.portail.farms.destroy', $farm->id) }}"
                                        class="inline-flex size-9 items-center justify-center rounded-lg border border-[var(--color-border)] bg-[var(--color-bg-page)] text-[var(--color-text-secondary)] shadow-sm transition-colors hover:bg-red-600 hover:text-white focus:outline-none focus:ring-4 focus:ring-red-100">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        {{-- Modale de confirmation de suppression --}}
        <div id="deleteFarmModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
            <div class="w-full max-w-sm rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-6 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-[var(--color-text-primary)]">Supprimer l'exploitation</h3>
                </div>
                <p class="mt-3 text-sm text-[var(--color-text-muted)]">
                    Êtes-vous sûr de vouloir supprimer définitivement cette exploitation ? Cette action est irréversible.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" id="cancelDeleteBtn"
                            class="rounded-lg border border-[var(--color-border)] bg-[var(--color-bg-card)] px-4 py-2 text-sm font-medium text-[var(--color-text-primary)] transition-colors hover:bg-[var(--color-bg-hover)]">
                        Annuler
                    </button>
                    <form id="deleteFarmForm" method="POST" class="inline" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-red-700">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const modal = document.getElementById('deleteFarmModal');
                const form = document.getElementById('deleteFarmForm');
                const cancelBtn = document.getElementById('cancelDeleteBtn');

                function openModal(url) {
                    form.action = url;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
                function closeModal() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                document.querySelectorAll('[data-delete-url]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        openModal(btn.dataset.deleteUrl);
                    });
                });

                cancelBtn.addEventListener('click', closeModal);
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) closeModal();
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
                });
            })();
        </script>
    @endif
</div>
@endsection