{{--
  <x-ui.card> — Carte conteneur universelle

  Props:
    padding : bool — active/désactive le padding interne sur le slot principal (défaut: true)
    hover   : bool — active l'effet de lift au survol (défaut: false)
    href    : string — rend la carte cliquable comme un <a>

  Slots nommés:
    header  — zone en-tête (avec bordure bottom)
    footer  — zone pied de carte (fond gris, bordure top)

  Usage:
    Simple:
      <x-ui.card>Contenu</x-ui.card>

    Avec header + footer:
      <x-ui.card>
          <x-slot:header><h3>Titre</h3></x-slot:header>
          Contenu principal
          <x-slot:footer>Pied de carte</x-slot:footer>
      </x-ui.card>

    Carte lien avec hover lift:
      <x-ui.card href="..." :hover="true">Contenu cliquable</x-ui.card>
--}}

@props([
    'padding' => true,
    'hover'   => false,
    'href'    => null,
])

@php
$base      = 'bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden';
$hoverCls  = $hover ? ' transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md' : '';
$classes   = $base . $hoverCls;
@endphp

@if($href)
<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
@else
<div {{ $attributes->merge(['class' => $classes]) }}>
@endif

    {{-- Header --}}
    @isset($header)
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            {{ $header }}
        </div>
    @endisset

    {{-- Body --}}
    @if($padding)
        <div class="p-5">{{ $slot }}</div>
    @else
        {{ $slot }}
    @endif

    {{-- Footer --}}
    @isset($footer)
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
            {{ $footer }}
        </div>
    @endisset

@if($href)
</a>
@else
</div>
@endif
