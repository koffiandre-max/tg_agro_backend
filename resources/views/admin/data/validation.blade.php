@extends('layouts.app')

@section('page-title', 'Validation des saisies de données')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- En-tête --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Validation des saisies de données</h1>
                <p class="mt-2 text-sm text-gray-600">Saisies agronomiques en attente de validation</p>
            </div>
            <div class="flex gap-3">
                <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-semibold text-yellow-800" id="pending-count">
                    {{ $entries->flatten()->count() }} saisie(s) en attente
                </span>
            </div>
        </div>

        {{-- Grille de saisies par exploitation --}}
        @if($entries->count() > 0)
            <div class="space-y-6">
                @foreach($entries as $farmName => $farmEntries)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h2 class="text-lg font-semibold text-gray-900">{{ $farmName }}</h2>
                        </div>
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($farmEntries as $entry)
                                <div class="data-card relative group rounded-2xl border border-amber-200 bg-white shadow-sm hover:shadow-md transition-all" data-entry-id="{{ $entry->id }}">
                                    <div class="p-5">
                                        {{-- Badge En attente --}}
                                        <div class="absolute top-3 right-3">
                                            <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-800 px-2 py-0.5 text-xs font-semibold shadow-sm border border-amber-200">
                                                En attente
                                            </span>
                                        </div>

                                        {{-- Client --}}
                                        <div class="mb-3">
                                            <p class="text-xs font-medium text-gray-500">Client</p>
                                            <p class="text-sm font-semibold text-gray-900">{{ $entry->client?->user?->name ?? 'Client #'.$entry->client_id }}</p>
                                        </div>

                                        {{-- Stade cultural --}}
                                        @if($entry->crop_stage)
                                            <div class="mb-3">
                                                <p class="text-xs font-medium text-gray-500">Stade cultural</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ $entry->crop_stage }}</p>
                                            </div>
                                        @endif

                                        {{-- Progression --}}
                                        @if($entry->crop_stage_progress !== null)
                                            <div class="mb-3">
                                                <p class="text-xs font-medium text-gray-500">Progression</p>
                                                <div class="mt-1 flex items-center gap-2">
                                                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $entry->crop_stage_progress }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-semibold text-gray-700">{{ $entry->crop_stage_progress }}%</span>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Date de récolte --}}
                                        @if($entry->estimated_harvest_date)
                                            <div class="mb-3">
                                                <p class="text-xs font-medium text-gray-500">Date de récolte prévue</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ $entry->estimated_harvest_date->format('d/m/Y') }}</p>
                                            </div>
                                        @endif

                                        {{-- Technicien --}}
                                        <div class="mb-3">
                                            <p class="text-xs font-medium text-gray-500">Technicien</p>
                                            <p class="text-sm font-semibold text-gray-900">{{ $entry->technician?->name ?? '—' }}</p>
                                        </div>

                                        {{-- Date --}}
                                        <div class="mb-4">
                                            <p class="text-xs font-medium text-gray-500">Créé le</p>
                                            <p class="text-sm font-semibold text-gray-900">{{ $entry->created_at?->format('d/m/Y H:i') ?? '' }}</p>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                                            <button type="button" onclick="validateEntry({{ $entry->id }}, this)" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-full bg-emerald-600 py-2 text-xs font-medium text-white hover:bg-emerald-700 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/>
                                                </svg>
                                                Valider
                                            </button>
                                            <button type="button" onclick="rejectEntry({{ $entry->id }}, this)" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-full bg-rose-600 py-2 text-xs font-medium text-white hover:bg-rose-700 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Rejeter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h12m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h8l6 6v10a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Aucune saisie en attente</h3>
                <p class="mt-1 text-sm text-gray-500">Toutes les saisies ont été validées.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function validateEntry(id, button) {
        try {
            const response = await fetch('/data/' + id + '/validate', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                window.location.reload();
                return;
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                window.location.reload();
                return;
            }

            const data = await response.json();

            if (data.success) {
                const card = button.closest('.data-card');
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => card.remove(), 300);

                const countEl = document.getElementById('pending-count');
                if (countEl) {
                    let count = parseInt(countEl.textContent) || 0;
                    countEl.textContent = Math.max(0, count - 1) + ' saisie(s) en attente';
                }
            } else {
                alert('Erreur lors de la validation.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erreur lors de la validation.');
        }
    }

    async function rejectEntry(id, button) {
        const reason = prompt('Motif du rejet (optionnel):');

        try {
            const response = await fetch('/data/' + id + '/reject', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ rejection_reason: reason || '' }),
            });

            if (!response.ok) {
                window.location.reload();
                return;
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                window.location.reload();
                return;
            }

            const data = await response.json();

            if (data.success) {
                const card = button.closest('.data-card');
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => card.remove(), 300);

                const countEl = document.getElementById('pending-count');
                if (countEl) {
                    let count = parseInt(countEl.textContent) || 0;
                    countEl.textContent = Math.max(0, count - 1) + ' saisie(s) en attente';
                }
            } else {
                alert('Erreur lors du rejet.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erreur lors du rejet.');
        }
    }
</script>
@endpush