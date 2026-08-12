{{--
  <x-ui.chart> — Carte de graphique Chart.js

  Props:
    title   : string — titre affiché en en-tête
    id      : string — id du <canvas> (requis pour Chart.js)
    center  : bool   — centre le contenu (utile pour polar/doughnut)
    type    : string — type de graphique (bar, line, polarArea, doughnut) — info seulement
    muted   : bool   — grise la carte (fonctionnalité désactivée)
--}}

@props([
    'title'  => '',
    'id'     => '',
    'center' => false,
    'type'   => null,
    'muted'  => false,
])

@php
$mutedClass = $muted ? 'opacity-50 grayscale' : '';
$bodyClass = $center ? 'h-72 flex items-center justify-center' : 'h-72';
@endphp

<div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm {{ $mutedClass }}">
    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
        @if($type)
        <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 text-[10px] font-semibold uppercase tracking-wider">{{ $type }}</span>
        @endif
        {{ $title }}
    </h3>
    <div class="{{ $bodyClass }}">
        <canvas id="{{ $id }}"></canvas>
    </div>
</div>
