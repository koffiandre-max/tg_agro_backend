@extends('layouts.app')

@section('page-title', 'Détails du Rapport')

@section('content')
@php
    $colors = ['#6366f1','#8b5cf6','#ec4899','#14b8a6','#f59e0b','#10b981','#3b82f6','#f97316'];
    $profileColor = $colors[abs(crc32($report->title ?? 'R')) % count($colors)];

    $statusMap = [
        'validated' => ['label' => 'Validé',     'text' => 'text-emerald-700', 'bg' => 'bg-emerald-50', 'ring' => 'ring-emerald-600/20', 'dot' => 'bg-emerald-500'],
        'rejected'  => ['label' => 'Rejeté',      'text' => 'text-rose-700',    'bg' => 'bg-rose-50',    'ring' => 'ring-rose-600/20',    'dot' => 'bg-rose-500'],
        'pending'   => ['label' => 'En attente',  'text' => 'text-amber-700',   'bg' => 'bg-amber-50',   'ring' => 'ring-amber-600/20',   'dot' => 'bg-amber-500'],
    ];
    $status = $statusMap[$report->status] ?? $statusMap['pending'];
@endphp

{{-- Formulaire de rejet (motif) --}}
<form id="reject-form" method="POST" action="{{ route('admin.reports.reject', $report) }}" class="hidden">
    @csrf
    <input type="hidden" name="rejection_reason" id="rejection_reason_input">
</form>



<div class="grid grid-cols-2 xl:grid-cols-3 gap-6">

    {{-- ============ Colonne centrale (2/3) ============ --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- Carte d'identité --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
            <div class="flex flex-col items-center text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl text-2xl font-bold text-white shadow-sm" style="background:{{ $profileColor }}">
                    {{ strtoupper(substr($report->type ?? 'R', 0, 1)) }}
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $report->title }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $report->file_original_name ?? $report->file_path }}</p>

                <div class="mt-3 flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 capitalize ring-1 ring-inset ring-indigo-600/10">
                        {{ $report->type }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full {{ $status['bg'] }} px-3 py-1 text-xs font-semibold {{ $status['text'] }} ring-1 ring-inset {{ $status['ring'] }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $status['dot'] }}"></span>
                        {{ $status['label'] }}
                    </span>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="rounded-xl bg-gray-50 p-3.5">
                    <p class="text-xs font-medium text-gray-500">Exploitation</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 truncate">{{ $report->farm?->name ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3.5">
                    <p class="text-xs font-medium text-gray-500">Client</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 truncate">{{ $report->client?->user?->name ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3.5">
                    <p class="text-xs font-medium text-gray-500">Technicien</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 truncate">{{ $report->technician?->name ?? '—' }}</p>
                </div>
                <div class="rounded-xl bg-gray-50 p-3.5">
                    <p class="text-xs font-medium text-gray-500">Taille du fichier</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ $report->file_size ? number_format($report->file_size / 1024, 2) . ' MB' : '—' }}
                    </p>
                </div>
            </div>

            <div class="mt-5 space-y-2">
                @if($report->file_path)
                    <a href="{{ route('admin.reports.download', $report) }}" class="flex w-full items-center justify-center gap-2 rounded-full bg-indigo-600 py-3 text-sm font-medium text-white hover:bg-indigo-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Télécharger le PDF
                    </a>
                @endif
                <a href="{{ route('admin.reports.index') }}" class="flex w-full items-center justify-center gap-2 rounded-full border border-gray-200 bg-white py-3 text-sm font-medium text-indigo-600 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour à la liste
                </a>
            </div>
        </div>

        {{-- Notes --}}
        @if($report->notes)
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-2">Notes</h3>
            <p class="text-sm text-gray-600 bg-gray-50 rounded-xl p-4 leading-relaxed">{{ $report->notes }}</p>
        </div>
        @endif

        {{-- Motif de rejet --}}
        @if($report->rejection_reason)
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-rose-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                Motif de rejet
            </h3>
            <p class="text-sm text-rose-600 bg-rose-50 rounded-xl p-4 leading-relaxed">{{ $report->rejection_reason }}</p>
        </div>
        @endif

        {{-- Visualiseur PDF --}}
        @if($report->file_path)
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Aperçu du document
                </h3>
                <a href="{{ route('admin.reports.download', $report) }}" class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Télécharger
                </a>
            </div>
            <div class="bg-gray-50">
                <iframe src="{{ route('admin.reports.pdf', $report) }}" class="w-full h-[600px] lg:h-[800px]" frameborder="0"></iframe>
            </div>
        </div>
        @endif
    </div>

    {{-- ============ Colonne latérale (1/3) ============ --}}
    <div class="xl:col-span-1 space-y-6">

        {{-- Tuiles rapides --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="mt-3 text-xs font-medium text-gray-500">Créé le</p>
                <p class="text-sm font-bold text-gray-900">{{ $report->created_at?->format('d/m/Y') ?? '—' }}</p>
            </div>

            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <p class="mt-3 text-xs font-medium text-gray-500">Mis à jour</p>
                <p class="text-sm font-bold text-gray-900">{{ $report->updated_at?->format('d/m/Y') ?? '—' }}</p>
            </div>

            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-50 text-teal-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <p class="mt-3 text-xs font-medium text-gray-500">Taille</p>
                <p class="text-sm font-bold text-gray-900">{{ $report->file_size ? number_format($report->file_size / 1024, 1) . ' MB' : '—' }}</p>
            </div>

            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m16 0h-5m-9 0h9m-9 0V13a2 2 0 012-2h5a2 2 0 012 2v8"/></svg>
                </div>
                <p class="mt-3 text-xs font-medium text-gray-500">Type</p>
                <p class="text-sm font-bold text-gray-900 capitalize truncate">{{ $report->type ?? '—' }}</p>
            </div>
        </div>

        {{-- Statut du rapport (style "plan actuel") --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-semibold tracking-wide {{ $status['text'] }} uppercase">Statut du rapport</p>
                    <p class="mt-1 text-xl font-bold text-gray-900">{{ $status['label'] }}</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full {{ $status['bg'] }} px-3 py-1 text-xs font-semibold {{ $status['text'] }} ring-1 ring-inset {{ $status['ring'] }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $status['dot'] }}"></span>
                    {{ $report->status === 'pending' ? 'À traiter' : $status['label'] }}
                </span>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-3">
                @if($report->status === 'validated' && $report->validated_by)
                    <div class="rounded-xl bg-gray-50 p-3.5">
                        <p class="text-xs font-medium text-gray-500">Validé par</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $report->validator?->name ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-3.5">
                        <p class="text-xs font-medium text-gray-500">Date de validation</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $report->validated_at?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                @elseif($report->status === 'rejected')
                    <div class="rounded-xl bg-gray-50 p-3.5">
                        <p class="text-xs font-medium text-gray-500">Statut mis à jour le</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $report->updated_at?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                @else
                    <div class="rounded-xl bg-gray-50 p-3.5">
                        <p class="text-xs font-medium text-gray-500">En attente depuis</p>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ $report->created_at?->diffForHumans() ?? '—' }}</p>
                    </div>

                    {{-- Actions de validation (étape 3 : Admin prévisualise et valide) --}}
                    <div class="mt-2 flex flex-col gap-2">
                        <form method="POST" action="{{ route('admin.reports.validate', $report) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-emerald-600 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Valider le rapport
                            </button>
                        </form>
                        <button type="button" onclick="openRejectModal()"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-full border border-rose-200 bg-white py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Rejeter le rapport
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal de rejet --}}
        <div id="reject-modal" x-data="{ open: false }" x-show="false" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
             @keydown.escape.window="open = false; $el.style.display='none'">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl" @click.stop>
                <h3 class="text-lg font-semibold text-gray-900">Motif du rejet</h3>
                <p class="mt-1 text-sm text-gray-500">Veuillez indiquer la raison du rejet de ce rapport.</p>
                <textarea id="reject-reason" rows="4"
                          class="mt-4 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-rose-500 focus:outline-none focus:ring-1 focus:ring-rose-500"
                          placeholder="Précisez le motif..."></textarea>
                <div class="mt-5 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="button" onclick="submitReject()"
                            class="px-4 py-2 text-sm font-medium text-white bg-rose-600 rounded-md hover:bg-rose-700">
                        Confirmer le rejet
                    </button>
                </div>
            </div>
        </div>

        <script>
            function openRejectModal() {
                const modal = document.getElementById('reject-modal');
                modal.style.display = 'flex';
                modal.setAttribute('x-show', 'true');
            }
            function closeRejectModal() {
                const modal = document.getElementById('reject-modal');
                modal.style.display = 'none';
                modal.setAttribute('x-show', 'false');
            }
            function submitReject() {
                const reason = document.getElementById('reject-reason').value.trim();
                if (!reason) {
                    alert('Veuillez saisir un motif de rejet.');
                    return;
                }
                document.getElementById('rejection_reason_input').value = reason;
                document.getElementById('reject-form').submit();
            }
        </script>

    </div>
</div>
@endsection