{{--
  <x-ui.stat-card> — Carte KPI / statistique

  Props:
    label          : string — libellé de la métrique (ex: "CA du mois")
    value          : string — valeur affichée (ex: "2 450 000 XAF")
    icon           : string — nom d'icône (cash, chart, users, document, clock, check, box, tag, star, warning)
                              ou SVG path brut
    color          : string — palette nommée (indigo, emerald, amber, red, blue, purple, orange, teal)
                              ou couleur hex (#10b981)  [défaut: indigo]
    trend          : string — texte affiché (ex: "+12%", "vs mois dernier")
    trend-direction: up | down | null — détermine la couleur de la tendance
    description    : string — sous-texte discret sous la valeur
    href           : string — rend la carte cliquable (ajoute hover lift)

  Slot:
    footer — contenu optionnel affiché en bas de la carte (fond gris)

  Exemples:
    <x-ui.stat-card
        label="CA du mois"
        value="2 450 000 XAF"
        icon="cash"
        color="emerald"
        trend="+12%"
        trend-direction="up"
        description="vs mois dernier"/>

    <x-ui.stat-card label="Factures en retard" value="3" icon="warning" color="#ef4444"
        trend="-1" trend-direction="down" href="{{ route('admin.invoices.index') }}"/>
--}}

@props([
    'label'          => '',
    'value'          => '',
    'icon'           => null,
    'color'          => 'indigo',
    'trend'          => null,
    'trendDirection' => null,
    'description'    => null,
    'href'           => null,
])

@php
// Palettes nommées
$palettes = [
    'indigo'  => ['bg' => 'bg-indigo-100',  'text' => 'text-indigo-600'],
    'emerald' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
    'amber'   => ['bg' => 'bg-amber-100',   'text' => 'text-amber-600'],
    'red'     => ['bg' => 'bg-red-100',     'text' => 'text-red-600'],
    'blue'    => ['bg' => 'bg-blue-100',    'text' => 'text-blue-600'],
    'purple'  => ['bg' => 'bg-purple-100',  'text' => 'text-purple-600'],
    'orange'  => ['bg' => 'bg-orange-100',  'text' => 'text-orange-600'],
    'teal'    => ['bg' => 'bg-teal-100',    'text' => 'text-teal-600'],
    'gray'    => ['bg' => 'bg-gray-100',    'text' => 'text-gray-600'],
];

$isHex    = !isset($palettes[$color]);
$palette  = $palettes[$color] ?? null;

// SVG paths nommés
$iconPaths = [
    'cash'     => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
    'chart'    => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'users'    => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 5.197a4 4 0 00-4-4m4 4a4 4 0 010 4',
    'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'clock'    => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    'check'    => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'box'      => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    'tag'      => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
    'star'     => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
    'warning'  => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    'trending' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
    'calendar' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    'x'        => 'M6 18L18 6M6 6l12 12',
];

$resolvedIconPath = $icon ? ($iconPaths[$icon] ?? $icon) : null;

$baseClass    = 'bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden';
$hoverClass   = $href ? ' transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md' : '';
$wrapperClass = $baseClass . $hoverClass;

// Couleur de trend
$trendColorClass = match($trendDirection) {
    'up'   => 'text-emerald-600',
    'down' => 'text-red-500',
    default => 'text-gray-500',
};
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $wrapperClass]) }}>
@else
<div {{ $attributes->merge(['class' => $wrapperClass]) }}>
@endif

    <div class="p-5">
        <div class="flex items-start justify-between gap-3">

            {{-- Texte --}}
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-500 truncate">{{ $label }}</p>
                <p class="mt-1.5 text-2xl font-bold text-gray-900 tracking-tight">{{ $value }}</p>

                {{-- Tendance --}}
                @if($trend !== null)
                    <div class="mt-1.5 flex items-center gap-1 text-sm font-semibold {{ $trendColorClass }}">
                        @if($trendDirection === 'up')
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @elseif($trendDirection === 'down')
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                        <span>{{ $trend }}</span>
                    </div>
                @endif

                @if($description)
                    <p class="mt-1 text-xs text-gray-400">{{ $description }}</p>
                @endif
            </div>

            {{-- Icône --}}
            @if($resolvedIconPath)
                <div class="shrink-0">
                    @if($isHex)
                        <div class="h-11 w-11 rounded-xl flex items-center justify-center"
                             style="background-color: {{ $color }}1a;">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 stroke-width="2" style="color: {{ $color }}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $resolvedIconPath }}"/>
                            </svg>
                        </div>
                    @else
                        <div class="h-11 w-11 rounded-xl {{ $palette['bg'] }} flex items-center justify-center">
                            <svg class="h-5 w-5 {{ $palette['text'] }}" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $resolvedIconPath }}"/>
                            </svg>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

    {{-- Footer optionnel --}}
    @isset($footer)
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-500">
            {{ $footer }}
        </div>
    @endisset

@if($href)
</a>
@else
</div>
@endif
