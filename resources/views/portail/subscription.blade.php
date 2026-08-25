@extends('layouts.app')

@section('page-title', 'Mon Abonnement')

@section('content')
@php
    $planColors = [
        'basic'    => ['label' => 'Basique',   'text' => 'text-slate-700',   'bg' => 'bg-slate-100',   'ring' => 'ring-slate-600/20',   'accent' => 'var(--color-text-muted)'],
        'standard' => ['label' => 'Standard',  'text' => 'text-blue-700',     'bg' => 'bg-blue-100',     'ring' => 'ring-blue-600/20',     'accent' => '#3b82f6'],
        'premium'  => ['label' => 'Premium',   'text' => 'text-amber-700',    'bg' => 'bg-amber-100',    'ring' => 'ring-amber-500/30',    'accent' => '#f59e0b'],
    ];
    $plan = $planColors[$client->subscription_type] ?? $planColors['basic'];

    $today = now();
    $expires = $client->subscription_expires_at;
    $daysLeft = $expires ? $today->diffInDays($expires, false) : null;
    $isActive = $expires ? $expires->greaterThanOrEqualTo($today) : false;
    $statusLabel = $isActive ? 'Actif' : 'Expiré';
    $statusColor = $isActive ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700';
@endphp

<div class="space-y-6 p-6">
    {{-- En-tête --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-[var(--color-text-primary)]">Mon Abonnement</h1>
            <p class="mt-1 text-sm text-[var(--color-text-muted)]">Détails et informations de votre souscription</p>
        </div>
        <a href="{{ route('admin.portail.index') }}"
           class="inline-flex items-center gap-1.5 rounded-md border border-[var(--color-border)] bg-[var(--color-bg-card)] px-3 py-2 text-sm font-medium text-[var(--color-text-primary)] shadow-sm transition-colors hover:bg-[var(--color-bg-hover)]">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Retour
        </a>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        {{-- Carte plan actuel --}}
        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-card)] shadow-sm">
                <div class="flex items-center justify-between border-b border-[var(--color-border)] px-5 py-4">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $plan['bg'] }} {{ $plan['text'] }} {{ $plan['ring'] }}">
                            {{ $plan['label'] }}
                        </span>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusColor }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    {{-- Hero plan --}}
                    <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-bg-page)] p-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-[var(--color-text-muted)]">Formule souscrite</p>
                                <p class="mt-1 text-2xl font-semibold tracking-tight text-[var(--color-text-primary)]">{{ $plan['label'] }}</p>
                            </div>
                            <div class="flex size-12 items-center justify-center rounded-xl" style="background: {{ $plan['accent'] }}1a;">
                                <svg class="size-6" style="color: {{ $plan['accent'] }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        @if($expires)
                            <div class="mt-4">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-[var(--color-text-muted)]">
                                        {{ $isActive ? 'Expire dans ' . $daysLeft . ' jour' . ($daysLeft > 1 ? 's' : '') : 'Expiré depuis ' . abs($daysLeft) . ' jour' . (abs($daysLeft) > 1 ? 's' : '') }}
                                    </span>
                                    <span class="font-semibold text-[var(--color-text-primary)]">{{ $expires->format('d/m/Y') }}</span>
                                </div>
                                <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-[var(--color-border-light)]">
                                    @php
                                        $total = $client->subscriptions->count() > 0
                                            ? $client->subscriptions->first()->start_date->diffInDays($expires)
                                            : 365;
                                        $elapsed = $client->subscriptions->count() > 0
                                            ? $client->subscriptions->first()->start_date->diffInDays($today)
                                            : 0;
                                        $pct = $total > 0 ? max(0, min(100, round($elapsed / $total * 100))) : 0;
                                    @endphp
                                    <div class="h-full rounded-full bg-[var(--color-brand-600)]" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Grille d'informations --}}
                    <dl class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-bg-page)] p-3.5">
                            <dt class="text-[11px] font-medium uppercase tracking-wide text-[var(--color-text-muted)]">Type d'abonnement</dt>
                            <dd class="mt-1 text-sm font-semibold capitalize text-[var(--color-text-primary)]">{{ $plan['label'] }}</dd>
                        </div>
                        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-bg-page)] p-3.5">
                            <dt class="text-[11px] font-medium uppercase tracking-wide text-[var(--color-text-muted)]">Statut</dt>
                            <dd class="mt-1 text-sm font-semibold text-[var(--color-text-primary)]">{{ $statusLabel }}</dd>
                        </div>
                        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-bg-page)] p-3.5">
                            <dt class="text-[11px] font-medium uppercase tracking-wide text-[var(--color-text-muted)]">Date d'expiration</dt>
                            <dd class="mt-1 text-sm font-semibold text-[var(--color-text-primary)]">{{ $expires?->format('d/m/Y') ?? '—' }}</dd>
                        </div>
                        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-bg-page)] p-3.5">
                            <dt class="text-[11px] font-medium uppercase tracking-wide text-[var(--color-text-muted)]">Investissement total</dt>
                            <dd class="mt-1 text-sm font-semibold text-[var(--color-text-primary)]">
                                {{ $client->total_investment ? number_format($client->total_investment, 0, ',', ' ') . ' FCFA' : '0 FCFA' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Colonne latérale : résumé client --}}
        <div class="space-y-5">
            <div class="rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-card)] p-5 shadow-sm">
                <h2 class="text-sm font-semibold text-[var(--color-text-primary)]">Mes informations</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-[var(--color-brand-50)] text-sm font-semibold text-[var(--color-brand-700)]">
                            {{ strtoupper(substr($user->name ?? 'C', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="truncate font-medium text-[var(--color-text-primary)]">{{ $user->name ?? '—' }}</p>
                            <p class="truncate text-xs text-[var(--color-text-muted)]">{{ $user->email ?? '' }}</p>
                        </div>
                    </div>
                    <div class="space-y-2 border-t border-[var(--color-border)] pt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--color-text-muted)]">Code client</span>
                            <span class="font-medium text-[var(--color-text-primary)]">{{ $client->code ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--color-text-muted)]">Téléphone</span>
                            <span class="font-medium text-[var(--color-text-primary)]">{{ $client->phone ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--color-text-muted)]">Ville</span>
                            <span class="font-medium text-[var(--color-text-primary)]">{{ $client->city_of_residence ?? '—' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--color-text-muted)]">Pays</span>
                            <span class="font-medium text-[var(--color-text-primary)]">{{ $client->country_of_residence ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-dashed border-[var(--color-border)] bg-[var(--color-bg-card)] p-5 text-center shadow-sm">
                <p class="text-sm font-medium text-[var(--color-text-primary)]">Besoin d'aide ?</p>
                <p class="mt-1 text-xs text-[var(--color-text-muted)]">Contactez notre équipe pour gérer ou renouveler votre abonnement.</p>
                <a href="{{ route('admin.portail.messages') }}"
                   class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-md border border-[var(--color-border)] bg-[var(--color-bg-card)] px-3 py-2 text-sm font-medium text-[var(--color-text-primary)] transition-colors hover:bg-[var(--color-bg-hover)]">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    Messagerie
                </a>
            </div>
        </div>
    </div>

    {{-- Historique des souscriptions --}}
    @if($subscriptions->isNotEmpty())
        <div class="mt-5 overflow-hidden rounded-xl border border-[var(--color-border)] bg-[var(--color-bg-card)] shadow-sm">
            <div class="border-b border-[var(--color-border)] px-5 py-4">
                <h2 class="text-sm font-semibold text-[var(--color-text-primary)]">Historique des souscriptions</h2>
            </div>
            <ul class="divide-y divide-[var(--color-border)]">
                @foreach($subscriptions as $sub)
                    <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center rounded-md bg-[var(--color-border-light)] px-2 py-1 text-xs font-medium capitalize text-[var(--color-text-secondary)]">
                                {{ $sub->type ?? '—' }}
                            </span>
                            <div>
                                <p class="text-sm font-medium text-[var(--color-text-primary)]">
                                    {{ $sub->amount ? number_format($sub->amount, 2) . ' ' . ($sub->currency ?? 'FCFA') : '—' }}
                                </p>
                                <p class="text-xs text-[var(--color-text-muted)]">
                                    {{ $sub->start_date?->format('d/m/Y') ?? '—' }} → {{ $sub->end_date?->format('d/m/Y') ?? '—' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-[var(--color-text-muted)]">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 font-medium
                                @if($sub->status === 'active' || $sub->status === 'actif') bg-emerald-100 text-emerald-700
                                @elseif($sub->status === 'expired' || $sub->status === 'expiré') bg-red-100 text-red-700
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ ucfirst($sub->status ?? '—') }}
                            </span>
                            @if($sub->auto_renew)
                                <span class="inline-flex items-center gap-1 rounded-md bg-[var(--color-brand-50)] px-2 py-0.5 text-[var(--color-brand-700)]">
                                    <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                    Auto-renouvellement
                                </span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
