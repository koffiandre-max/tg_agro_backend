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

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 p-6" x-data="{ rejectModalOpen: false, reason: '' }">

    {{-- Formulaire de rejet caché (soumis via Alpine) --}}
    <form id="reject-form" method="POST" action="{{ route('admin.reports.reject', $report) }}" class="hidden">
        @csrf
        <input type="hidden" name="rejection_reason" x-model="reason">
    </form>

    {{-- ============ Colonne principale (2/3 de l'espace) ============ --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- Carte d'identité principale --}}
        <x-ui.card class="border border-slate-200/80 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div class="flex items-start gap-4">
                    {{-- Avatar type de rapport --}}
                    <div class="h-14 w-14 rounded-2xl flex items-center justify-center font-bold text-white text-lg shrink-0 shadow-sm" style="background: {{ $profileColor }}">
                        {{ strtoupper(substr($report->type ?? 'R', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight leading-tight">{{ $report->title }}</h2>
                        <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1.5 font-semibold">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="truncate">{{ $report->file_original_name ?? $report->file_path }}</span>
                        </p>
                    </div>
                </div>

                {{-- Badges d'état --}}
                <div class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex items-center rounded-full bg-slate-50 border border-slate-200/60 px-2.5 py-0.5 text-xs font-semibold text-slate-600 capitalize">
                        {{ $report->type }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full {{ $status['bg'] }} px-2.5 py-1 text-xs font-bold {{ $status['text'] }} border border-slate-200/50">
                        <span class="h-1.5 w-1.5 rounded-full {{ $status['dot'] }}"></span>
                        {{ $status['label'] }}
                    </span>
                </div>
            </div>

            {{-- Fiche Technique (Relations clés) --}}
            <dl class="divide-y divide-slate-100 text-sm mt-2">
                <div class="grid grid-cols-1 sm:grid-cols-3 py-4 gap-1">
                    <dt class="font-bold text-slate-500">Exploitation agricole</dt>
                    <dd class="sm:col-span-2 text-slate-900 font-extrabold">{{ $report->farm?->name ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 py-4 gap-1">
                    <dt class="font-semibold text-slate-500">Client associé</dt>
                    <dd class="sm:col-span-2 text-slate-900 font-medium">{{ $report->client?->user?->name ?? '—' }}</dd>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 py-4 gap-1">
                    <dt class="font-semibold text-slate-500">Technicien en charge</dt>
                    <dd class="sm:col-span-2 text-slate-900 font-medium">{{ $report->technician?->name ?? '—' }}</dd>
                </div>
            </dl>

            {{-- Actions de navigation rapides --}}
            <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col sm:flex-row gap-3">
                @if($report->file_path)
                    <a href="{{ route('admin.reports.download', $report) }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white py-3 text-sm font-semibold transition-colors shadow-sm shadow-indigo-600/10">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Télécharger le rapport
                    </a>
                @endif
                <a href="{{ route('admin.reports.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 py-3 text-sm font-semibold transition-colors shadow-sm">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Retour à la liste
                </a>
            </div>
        </x-ui.card>

        {{-- Notes / Observations --}}
        @if($report->notes)
            <div class="p-5 bg-slate-50 border border-slate-200/60 rounded-2xl">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Observations de terrain</h3>
                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line font-medium">{{ $report->notes }}</p>
            </div>
        @endif

        {{-- Motif de rejet si existant --}}
        @if($report->rejection_reason)
            <div class="p-5 bg-rose-50/50 border border-rose-100/50 rounded-2xl animate-fadeIn">
                <h3 class="text-xs font-bold text-rose-900 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-4.5 h-4.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Motif de rejet
                </h3>
                <p class="text-sm text-rose-700 leading-relaxed font-semibold">{{ $report->rejection_reason }}</p>
            </div>
        @endif

        {{-- Visualiseur PDF --}}
        @if($report->file_path)
            <x-ui.card class="border border-slate-200 shadow-sm !p-0 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-4.5 h-4.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Aperçu du document PDF
                    </h3>
                    <a href="{{ route('admin.reports.download', $report) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Télécharger
                    </a>
                </div>
                <div class="w-full bg-slate-100">
                    <iframe src="{{ route('admin.reports.pdf', $report) }}" class="w-full h-[600px] lg:h-[800px] border-0" frameborder="0"></iframe>
                </div>
            </x-ui.card>
        @endif
    </div>

    {{-- ============ Colonne latérale (1/3 de l'espace) ============ --}}
    <div class="xl:col-span-1 space-y-6">

        {{-- État & Validation du rapport --}}
        <x-ui.card class="border border-slate-200/80 shadow-sm">
            <x-slot:header>
                <h3 class="text-base font-bold text-slate-900">Suivi et Validation</h3>
            </x-slot:header>

            <div class="space-y-4">
                {{-- Affichage du statut --}}
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">État actuel</span>
                        <span class="text-sm font-extrabold text-slate-900 mt-0.5 inline-block">{{ $status['label'] }}</span>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full {{ $status['text'] }} {{ $status['bg'] }} border border-slate-200/30">
                        <span class="h-1.5 w-1.5 rounded-full {{ $status['dot'] }}"></span>
                        {{ $report->status === 'pending' ? 'À traiter' : $status['label'] }}
                    </span>
                </div>

                @if($report->status === 'validated' && $report->validated_by)
                    <div class="space-y-3">
                        <div class="p-3 bg-slate-50 rounded-xl text-xs space-y-1">
                            <p class="text-slate-400 font-bold uppercase tracking-wider text-[9px]">Validé par</p>
                            <p class="text-slate-900 font-extrabold">{{ $report->validator?->name ?? '—' }}</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl text-xs space-y-1">
                            <p class="text-slate-400 font-bold uppercase tracking-wider text-[9px]">Date de validation</p>
                            <p class="text-slate-900 font-extrabold">{{ $report->validated_at?->format('d/m/Y \à H:i') ?? '—' }}</p>
                        </div>
                    </div>
                @elseif($report->status === 'rejected')
                    <div class="p-3 bg-slate-50 rounded-xl text-xs space-y-1 animate-fadeIn">
                        <p class="text-slate-400 font-bold uppercase tracking-wider text-[9px]">Rejet enregistré le</p>
                        <p class="text-slate-900 font-extrabold">{{ $report->updated_at?->format('d/m/Y \à H:i') ?? '—' }}</p>
                    </div>
                @else
                    {{-- Actions Administrateur (Uniquement si en attente) --}}
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <div class="space-y-2.5 pt-2">
                            <form method="POST" action="{{ route('admin.reports.validate', $report) }}">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 text-sm font-semibold transition-colors shadow-sm shadow-emerald-600/10">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Valider le rapport
                                </button>
                            </form>
                            <button type="button" @click="rejectModalOpen = true" class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50/50 hover:bg-rose-100 hover:text-rose-700 text-rose-600 py-2.5 text-sm font-semibold transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Rejeter le rapport
                            </button>
                        </div>
                    @else
                        <div class="p-3 bg-slate-50 rounded-xl text-xs space-y-1">
                            <p class="text-slate-400 font-bold uppercase tracking-wider text-[9px]">Statut</p>
                            <p class="text-slate-900 font-extrabold flex items-center gap-1.5">
                                <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                                En attente de validation
                            </p>
                        </div>
                    @endif
                @endif
            </div>
        </x-ui.card>

        {{-- Métadonnées temporelles et techniques unifiées --}}
        <x-ui.card class="border border-slate-200/80 shadow-sm">
            <x-slot:header>
                <h3 class="text-base font-bold text-slate-900">Métadonnées de fichier</h3>
            </x-slot:header>

            <div class="space-y-4">
                <div class="flex items-center justify-between py-2 border-b border-slate-100 text-xs">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Type de rapport</span>
                    <span class="font-extrabold text-slate-800 capitalize">{{ $report->type ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100 text-xs">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Poids du document</span>
                    <span class="font-extrabold text-slate-800">
                        {{ $report->file_size ? number_format($report->file_size / 1024, 2) . ' MB' : '—' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-slate-100 text-xs">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Date d'ajout</span>
                    <span class="font-extrabold text-slate-800">{{ $report->created_at?->format('d/m/Y') ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 text-xs">
                    <span class="font-bold text-slate-400 uppercase tracking-wider text-[9px]">Mise à jour</span>
                    <span class="font-extrabold text-slate-800">{{ $report->updated_at?->format('d/m/Y') ?? '—' }}</span>
                </div>
            </div>
        </x-ui.card>
    </div>

    {{-- Modal de Rejet de rapport (Propulsé purement par Alpine.js) --}}
    <div x-show="rejectModalOpen" 
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/45 backdrop-blur-[1px] px-4"
         x-cloak>
        
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200" @click.stop>
            <h3 class="text-lg font-bold text-slate-900">Spécifier le motif du rejet</h3>
            <p class="mt-1 text-sm text-slate-500">Veuillez indiquer précisément au technicien la raison du rejet de ce document.</p>
            
            <textarea x-model="reason" rows="4"
                      class="mt-4 w-full rounded-xl border border-slate-200 px-3.5 py-3 text-sm text-slate-950 placeholder:text-slate-400 focus:border-rose-500 focus:outline-none focus:ring-4 focus:ring-rose-500/10 transition-all"
                      placeholder="Précisez le motif (ex: données erronées, photo manquante...)"></textarea>
            
            <div class="mt-5 flex items-center justify-end gap-3">
                <button type="button" @click="rejectModalOpen = false"
                        class="px-4 py-2.5 text-xs font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    Annuler
                </button>
                <button type="button" 
                        @click="if(!reason.trim()) { alert('Veuillez saisir un motif de rejet.'); return; } document.getElementById('reject-form').submit();"
                        class="px-4 py-2.5 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-colors shadow-sm">
                    Confirmer le rejet
                </button>
            </div>
        </div>
    </div>
</div>
@endsection