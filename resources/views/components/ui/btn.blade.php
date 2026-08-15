{{--
  <x-ui.btn> — Bouton universel du Design System

  Props:
    variant   : primary | secondary | outline | ghost | danger | success | warning | light  (défaut: primary)
    size      : xs | sm | md | lg  (défaut: md)
    icon      : nom d'icône (plus, edit, trash, download, send, eye, check, x, arrow-left,
                arrow-right, print, refresh, filter, export) ou SVG path brut
    icon-pos  : left | right  (défaut: left)
    loading   : bool — affiche spinner et désactive le bouton
    href      : string — rend un <a> au lieu d'un <button>
    type      : button | submit | reset  (défaut: button)
    disabled  : bool

  Exemples:
    <x-ui.btn variant="primary" icon="plus">Nouvelle facture</x-ui.btn>
    <x-ui.btn href="{{ route('...') }}" variant="secondary" size="sm">Voir</x-ui.btn>
    <x-ui.btn variant="danger" icon="trash" :loading="$saving">Supprimer</x-ui.btn>
    <x-ui.btn variant="outline" icon="download" icon-pos="right">Exporter</x-ui.btn>
--}}

@props([
    'variant'  => 'primary',
    'size'     => 'md',
    'icon'     => null,
    'iconPos'  => 'left',
    'loading'  => false,
    'href'     => null,
    'type'     => 'button',
    'disabled' => false,
])

@php
$variants = [
    'primary'   => 'bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white border border-indigo-600 shadow-sm',
    'secondary' => 'bg-white hover:bg-gray-50 active:bg-gray-100 text-gray-700 border border-gray-300 shadow-sm',
    'outline'   => 'bg-transparent hover:bg-indigo-50 active:bg-indigo-100 text-indigo-600 border border-indigo-300',
    'ghost'     => 'bg-transparent hover:bg-gray-100 active:bg-gray-200 text-gray-600 border border-transparent',
    'danger'    => 'bg-red-600 hover:bg-red-700 active:bg-red-800 text-white border border-red-600 shadow-sm',
    'success'   => 'bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white border border-emerald-600 shadow-sm',
    'warning'   => 'bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white border border-amber-500 shadow-sm',
    'light'     => 'bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-gray-700 border border-gray-200',
    'link'      => 'bg-transparent text-indigo-600 hover:text-indigo-800 border border-transparent underline-offset-2 hover:underline',
];

$sizes = [
    'xs' => 'px-2.5 py-1 text-xs rounded-md gap-1',
    'sm' => 'px-3 py-1.5 text-sm rounded-md gap-1.5',
    'md' => 'px-4 py-2 text-sm rounded-lg gap-2',
    'lg' => 'px-5 py-2.5 text-base rounded-lg gap-2',
];

$iconSizes = [
    'xs' => 'w-3 h-3',
    'sm' => 'w-3.5 h-3.5',
    'md' => 'w-4 h-4',
    'lg' => 'w-5 h-5',
];

// SVG paths pour les icônes nommées
$iconPaths = [
    'plus'        => 'M12 4v16m8-8H4',
    'edit'        => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
    'trash'       => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
    'download'    => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
    'send'        => 'M12 19l9 2-9-18-9 18 9-2zm0 0v-8',
    'eye'         => 'M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7 -1.274 4.057-5.064 7 -9.542 7 -4.477 0-8.268-2.943-9.542-7z',
    'check'       => 'M5 13l4 4L19 7',
    'x'           => 'M6 18L18 6M6 6l12 12',
    'arrow-left'  => 'M10 19l-7-7m0 0l7-7m-7 7h18',
    'arrow-right' => 'M14 5l7 7m0 0l-7 7m7-7H3',
    'print'       => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
    'refresh'     => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
    'filter'      => 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z',
    'export'      => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
    'copy'        => 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z',
    'mail'        => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'cog'         => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
];

$resolvedIconPath = $icon ? ($iconPaths[$icon] ?? $icon) : null;
$variantClass     = $variants[$variant] ?? $variants['primary'];
$sizeClass        = $sizes[$size] ?? $sizes['md'];
$iconSizeClass    = $iconSizes[$size] ?? $iconSizes['md'];
$isDisabled       = $disabled || $loading;

$base = 'inline-flex items-center justify-center font-medium transition-all duration-150 cursor-pointer ' .
        'disabled:opacity-50 disabled:pointer-events-none focus:outline-none ' .
        'focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 whitespace-nowrap select-none';

$classes = $base . ' ' . $variantClass . ' ' . $sizeClass;
@endphp

@if($href)
<a
    href="{{ $isDisabled ? '#' : $href }}"
    {{ $attributes->merge(['class' => $classes]) }}
    @if($isDisabled) aria-disabled="true" tabindex="-1" @endif
>
@else
<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => $classes]) }}
    @if($isDisabled) disabled @endif
>
@endif

    {{-- Spinner (loading) --}}
    @if($loading)
        <svg class="{{ $iconSizeClass }} animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>

    {{-- Icône gauche --}}
    @elseif($resolvedIconPath && $iconPos === 'left')
        <svg class="{{ $iconSizeClass }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $resolvedIconPath }}"/>
        </svg>
    @endif

    {{-- Label --}}
    @if($slot->isNotEmpty())
        <span>{{ $slot }}</span>
    @endif

    {{-- Icône droite --}}
    @if(!$loading && $resolvedIconPath && $iconPos === 'right')
        <svg class="{{ $iconSizeClass }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $resolvedIconPath }}"/>
        </svg>
    @endif

@if($href)
</a>
@else
</button>
@endif
