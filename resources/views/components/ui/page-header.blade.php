{{--
  <x-ui.page-header> — En-tête de page standardisé

  Props:
    title       : string — titre principal de la page (obligatoire)
    subtitle    : string — sous-titre / description courte (optionnel)
    breadcrumbs : array  — fil d'ariane [['label'=>'...', 'route'=>'...'], ['label'=>'...']]
                           Le dernier élément sans 'route' est l'élément courant.

  Slot nommé:
    actions — boutons / actions affichés à droite du titre

  Usage:
    Simple:
      <x-ui.page-header title="Factures"/>

    Avec breadcrumb et actions:
      <x-ui.page-header title="Créer" :breadcrumbs="[['label'=>'Factures','route'=>'admin.invoices.index'],['label'=>'Nouvelle']]">
          <x-slot:actions>
              <x-ui.btn href="..." icon="plus">Nouvelle facture</x-ui.btn>
          </x-slot:actions>
      </x-ui.page-header>
--}}

@props([
    'title'       => '',
    'subtitle'    => null,
    'breadcrumbs' => [],
])

<div class="mb-6">

    {{-- Fil d'ariane --}}
    @if(!empty($breadcrumbs))
        <nav aria-label="breadcrumb"
             class="flex items-center gap-1.5 text-xs text-gray-400 mb-2.5 flex-wrap pt-4">

            {{-- Accueil --}}
            <a href="{{ route($homeRoute ?? 'dashboard') }}"
               class="hover:text-gray-600 transition-colors inline-flex items-center"
               title="Accueil">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </a>

            @foreach($breadcrumbs as $crumb)
                {{-- Séparateur --}}
                <svg class="w-3 h-3 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>

                @if(isset($crumb['route']))
                    <a href="{{ route($crumb['route'], $crumb['params'] ?? []) }}"
                       class="hover:text-gray-600 transition-colors truncate max-w-[200px]">
                        {{ $crumb['label'] }}
                    </a>
                @else
                    <span class="text-gray-600 font-medium truncate max-w-[200px]">
                        {{ $crumb['label'] }}
                    </span>
                @endif
            @endforeach
        </nav>
    @endif

    {{-- Titre + Actions --}}
    <div class="flex items-start justify-between gap-4">

        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-gray-900 leading-tight">{{ $title }}</h1>
            @if($subtitle)
                <p class="mt-0.5 text-sm text-gray-500">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                {{ $actions }}
            </div>
        @endisset

    </div>

</div>
