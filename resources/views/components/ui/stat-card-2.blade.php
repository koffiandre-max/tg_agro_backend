{{--
  <x-ui.stat-card-2> — Carte KPI du tableau de bord

  Props:
    label   : string — libellé (ex: "Clients")
    value   : string|int — valeur principale
    icon    : string — SVG path brut
    color   : string — palette nommée (indigo, emerald, purple, amber, ...) [défaut: indigo]
    trend   : float|int — variation en % (ex: 8, -20)
    suffix  : string — sous-texte de la tendance [défaut: "vs mois dernier"]
--}}

@props([
    'label'   => '',
    'value'   => '',
    'icon'    => null,
    'color'   => 'indigo',
    'trend'   => null,
    'suffix'  => 'vs mois dernier',
])

@php
$trendUp = $trend !== null && $trend >= 0;
$trendText = $trend !== null ? (($trend > 0 ? '+' : '').$trend.'%') : '';
@endphp

<div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $label }}</p>
            <p class="text-3xl font-black text-slate-900 mt-2">{{ $value }}</p>
        </div>
        @if($icon)
        <div class="h-11 w-11 bg-{{ $color }}-50 text-{{ $color }}-600 rounded-xl flex items-center justify-center border border-{{ $color }}-100/50">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/>
            </svg>
        </div>
        @endif
    </div>
    @if($trend !== null)
    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-1.5 text-xs">
        <span class="inline-flex items-center gap-0.5 font-bold px-1.5 py-0.5 rounded-md {{ $trendUp ? 'text-emerald-600 bg-emerald-50' : 'text-amber-600 bg-amber-50' }}">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $trendUp ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 13l-7 7-7-7m7 7V3' }}"/>
            </svg>
            {{ $trendText }}
        </span>
        <span class="text-slate-400">{{ $suffix }}</span>
    </div>
    @endif
</div>
