@extends('layouts.app')

@section('title', 'Mes Rapports')
@section('page-title', 'Mes Rapports')

@section('content')
@php
    $isReadOnly = isset($subscriptionExpired) && $subscriptionExpired;
@endphp

<div class="space-y-6 p-6">
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

    <div class="">
        {{-- En-tête --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mes Rapports</h1>
                <p class="mt-2 text-sm text-gray-600">Retrouvez et téléchargez les rapports validés vous concernant.</p>
            </div>
        </div>

        @if($reports->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                    <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-sm font-semibold text-gray-900">Aucun rapport disponible</h3>
                <p class="mt-1 text-sm text-gray-500">Vos rapports apparaîtront ici une fois validés par l'administrateur.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 {{ $isReadOnly ? 'opacity-60 grayscale' : '' }}">
                @foreach($reports as $report)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col">
                        <div class="flex items-start justify-between">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Validé
                            </span>
                        </div>

                        <h3 class="mt-4 text-base font-semibold text-gray-900 leading-snug">{{ $report->title }}</h3>
                        <p class="mt-1 text-xs text-gray-500 capitalize">{{ $report->type }}</p>

                        <dl class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Exploitation</dt>
                                <dd class="font-medium text-gray-900 truncate max-w-[55%]">{{ $report->farm?->name ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Validé le</dt>
                                <dd class="font-medium text-gray-900">{{ $report->validated_at?->format('d/m/Y') ?? '—' }}</dd>
                            </div>
                            @if($report->file_size)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Taille</dt>
                                <dd class="font-medium text-gray-900">{{ number_format($report->file_size / 1024, 1) }} MB</dd>
                            </div>
                            @endif
                        </dl>

                        <div class="mt-5 pt-4 border-t border-gray-100 flex items-center gap-3">
                            <a href="{{ route('admin.portail.reports.download', $report) }}"
                               class="flex-1 inline-flex items-center justify-center gap-2 rounded-full bg-emerald-600 py-2.5 text-sm font-medium text-white hover:bg-emerald-700 transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Télécharger
                            </a>
                            @if($report->file_path)
                                <a href="{{ route('admin.portail.reports.download', $report) }}"
                                   target="_blank"
                                   class="inline-flex items-center justify-center h-10 w-10 rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 transition-colors"
                                   title="Ouvrir l'aperçu">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7 -1.274 4.057-5.064 7 -9.542 7 -4.477 0-8.268-2.943-9.542-7z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection