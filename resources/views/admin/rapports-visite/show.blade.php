@extends('layouts.app')

@section('title', 'Détails du Rapport de Visite - ' . ($rapport->localisation_parcelle ?? 'Rapport'))

@section('content')
@php
    $statutColors = [
        'brouillon' => ['label' => 'Brouillon', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'ring' => 'ring-gray-600/20'],
        'en_attente_validation' => ['label' => 'En attente', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'ring' => 'ring-amber-600/20'],
        'valide' => ['label' => 'Validé', 'bg' => 'bg-green-50', 'text' => 'text-green-700', 'ring' => 'ring-green-600/20'],
        'rejete' => ['label' => 'Rejeté', 'bg' => 'bg-red-50', 'text' => 'text-red-700', 'ring' => 'ring-red-600/20'],
    ];
    $statut = $statutColors[$rapport->statut?->value] ?? ['label' => $rapport->statut?->label() ?? '—', 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'ring' => 'ring-gray-600/20'];
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6" x-data="{ activeTab: 'resume', rejectOpen: false }">

    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <a href="{{ route('admin.rapports-visite.index') }}" class="group inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-800 mb-2 transition-colors duration-150">
                <svg class="w-4 h-4 mr-1.5 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour à la liste
            </a>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                Rapport — <span class="text-indigo-600">{{ $rapport->localisation_parcelle }}</span>
            </h1>
            <p class="mt-1 text-sm text-slate-500">{{ $rapport->technicien?->name ?? '—' }} &middot; {{ $rapport->date_visite?->format('d/m/Y') ?? '—' }}</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statut['bg'] }} {{ $statut['text'] }} ring-1 {{ $statut['ring'] }}">
                {{ $statut['label'] }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            <x-ui.card>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-100 flex items-center justify-center mx-auto">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="mt-3 text-lg font-bold text-gray-900">{{ $rapport->technicien?->name ?? '—' }}</h2>
                    <p class="mt-0.5 text-xs text-gray-500">{{ $rapport->technicien?->email ?? '' }}</p>
                </div>
                <div class="mt-4 space-y-2.5 text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-calendar w-4 text-gray-400"></i>
                        {{ $rapport->date_visite?->format('d/m/Y') ?? '—' }}
                    </div>
                    @if($rapport->client)
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-user w-4 text-gray-400"></i>
                            {{ $rapport->client->user?->name ?? $rapport->client->nom ?? '—' }}
                        </div>
                    @endif
                    @if($rapport->farm)
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-map-marker-alt w-4 text-gray-400"></i>
                            {{ $rapport->farm->name ?? '—' }}
                        </div>
                    @endif
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-clock w-4 text-gray-400"></i>
                        {{ $rapport->duree_visite?->label() ?? $rapport->duree_visite ?? '—' }}
                    </div>
                    @if($rapport->superficie_visitee_ha)
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-ruler w-4 text-gray-400"></i>
                            {{ number_format($rapport->superficie_visitee_ha, 2) }} ha
                        </div>
                    @endif
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-tag w-4 text-gray-400"></i>
                        {{ $rapport->type_activite?->label() ?? '—' }}
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-slot:header>
                    <h3 class="text-sm font-bold text-slate-900">Actions</h3>
                </x-slot:header>
                <div class="space-y-2">
                    <x-ui.btn href="{{ route('admin.rapports-visite.edit', $rapport->id) }}" variant="secondary" icon="edit" class="w-full">
                        Modifier
                    </x-ui.btn>
                    @if($rapport->statut?->value === 'en_attente_validation')
                        <x-ui.btn href="{{ route('admin.rapports-visite.validate', $rapport->id) }}" variant="success" icon="check" class="w-full">
                            Valider
                        </x-ui.btn>
                        <x-ui.btn type="button" @click="rejectOpen = true" variant="danger" icon="x" class="w-full">
                            Rejeter
                        </x-ui.btn>
                    @endif
                    <x-ui.btn href="{{ route('admin.rapports-visite.print', $rapport->id) }}" variant="outline" icon="print" class="w-full">
                        Imprimer
                    </x-ui.btn>
                    <x-ui.btn href="{{ route('admin.rapports-visite.index') }}" variant="ghost" icon="arrow-left" class="w-full">
                        Retour
                    </x-ui.btn>
                </div>
            </x-ui.card>
        </div>

        {{-- Main Content with Tabs --}}
        <div class="lg:col-span-2">
            {{-- Tab Navigation --}}
            <div class="gap-6 mb-6 ">
                <nav class="flex gap-1 overflow-x-auto px-4" role="tablist">
                    @php
                        $tabs = [];
                        if ($rapport->type_activite?->value === 'culture') {
                            $tabs['culture'] = ['label' => 'Cultures', 'icon' => 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14'];
                        } elseif ($rapport->type_activite?->value === 'elevage') {
                            $tabs['elevage'] = ['label' => 'Élevage', 'icon' => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342'];
                        } elseif ($rapport->type_activite?->value === 'autre') {
                            $tabs['autre'] = ['label' => 'Activité', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'];
                        }
                        $tabs['photos'] = ['label' => 'Photos', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'];
                        $tabs['historique'] = ['label' => 'Historique', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'];
                        $tabs['resume'] = ['label' => 'Résumé', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'];
                    @endphp
                    @foreach($tabs as $tabKey => $tab)
                        <button type="button" role="tab"
                            x-on:click="activeTab = '{{ $tabKey }}'"
                            :class="activeTab === '{{ $tabKey }}' ? 'text-white bg-indigo-600 shadow-sm rounded-md ' : 'border-transparent rounded-md text-slate-500 hover:text-slate-700 bg-white'"
                            class="flex items-center gap-1.5 px-4 py-2.5  font-medium text-sm transition-colors duration-150 whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}"/>
                            </svg>
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- Tab Panels --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

                {{-- Cultures --}}
                @if($rapport->type_activite?->value === 'culture' && $rapport->visiteCultures->isNotEmpty())
                    <div x-show="activeTab === 'culture'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="p-6">
                        @foreach($rapport->visiteCultures as $visiteCulture)
                            <div class="space-y-6">
                                @if($visiteCulture->cultures_presentes)
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Types de cultures</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($visiteCulture->cultures_presentes as $culture)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ $culture }}</span>
                                            @endforeach
                                            @if($visiteCulture->culture_autre_precision)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $visiteCulture->culture_autre_precision }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-slate-500">Stade phénologique</span>
                                        <p class="font-medium text-slate-900">{{ $visiteCulture->stade_phenologique ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Avancement</span>
                                        <p class="font-medium text-slate-900">{{ $visiteCulture->avancement_cycle_pourcent ?? '—' }}%</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">État couvert</span>
                                        <p class="font-medium text-slate-900">{{ $visiteCulture->etat_couvert_vegetal ?? '—' }}/5</p>
                                    </div>
                                </div>

                                @if($visiteCulture->ravageurs_maladies)
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Ravageurs et maladies</h4>
                                        <div class="flex flex-wrap gap-2 mb-2">
                                            @foreach($visiteCulture->ravageurs_maladies as $ravageur)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">{{ $ravageur }}</span>
                                            @endforeach
                                            @if($visiteCulture->ravageur_autre_precision)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $visiteCulture->ravageur_autre_precision }}</span>
                                            @endif
                                            @if($visiteCulture->niveau_infestation)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Niveau: {{ $visiteCulture->niveau_infestation }}/5</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-slate-500">État hydrique</span>
                                        <p class="font-medium text-slate-900">{{ $visiteCulture->etat_hydrique_sol ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Irrigation</span>
                                        <p class="font-medium text-slate-900">{{ $visiteCulture->irrigation_en_place === null ? '—' : ($visiteCulture->irrigation_en_place ? 'Oui' : 'Non') }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Structure du sol</span>
                                        <p class="font-medium text-slate-900">{{ $visiteCulture->etat_structure_sol ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">pH du sol</span>
                                        <p class="font-medium text-slate-900">{{ $visiteCulture->ph_sol ?? '—' }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-slate-500">Intrants utilisés</span>
                                        <p class="font-medium text-slate-900">{{ $visiteCulture->intrants_utilises ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Estimation récolte</span>
                                        <p class="font-medium text-slate-900">{{ $visiteCulture->estimation_recolte_kg ? number_format($visiteCulture->estimation_recolte_kg, 2) . ' kg' : '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Date récolte estimée</span>
                                        <p class="font-medium text-slate-900">{{ $visiteCulture->date_estimee_recolte?->format('d/m/Y') ?? '—' }}</p>
                                    </div>
                                </div>

                                @if($visiteCulture->observations_ravageurs)
                                    <div class="text-sm">
                                        <span class="font-semibold text-slate-500">Observations ravageurs</span>
                                        <p class="text-slate-900 mt-1">{{ $visiteCulture->observations_ravageurs }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Élevage --}}
                @if($rapport->type_activite?->value === 'elevage' && $rapport->visiteElevage->isNotEmpty())
                    <div x-show="activeTab === 'elevage'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="p-6">
                        @foreach($rapport->visiteElevage as $visiteElevage)
                            <div class="space-y-6">
                                @if($visiteElevage->animaux_presents)
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Types d'animaux</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($visiteElevage->animaux_presents as $animal)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ $animal }}</span>
                                            @endforeach
                                            @if($visiteElevage->animal_autre_precision)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $visiteElevage->animal_autre_precision }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-slate-500">Effectif total</span>
                                        <p class="font-medium text-slate-900">{{ $visiteElevage->effectif_total ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Mortalité</span>
                                        <p class="font-medium text-slate-900">{{ $visiteElevage->mortalite_constatee ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Naissances</span>
                                        <p class="font-medium text-slate-900">{{ $visiteElevage->naissances_depuis_derniere_visite ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Ventes/Abattages</span>
                                        <p class="font-medium text-slate-900">{{ $visiteElevage->ventes_abattages_depuis_derniere_visite ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">État corporel</span>
                                        <p class="font-medium text-slate-900">{{ $visiteElevage->etat_corporel_general ?? '—' }}/5</p>
                                    </div>
                                </div>

                                @if($visiteElevage->signes_cliniques)
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Signes cliniques</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($visiteElevage->signes_cliniques as $signe)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">{{ $signe }}</span>
                                            @endforeach
                                            @if($visiteElevage->signe_autre_precision)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{{ $visiteElevage->signe_autre_precision }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-slate-500">Alimentation</span>
                                        <p class="font-medium text-slate-900">{{ $visiteElevage->etat_alimentation ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Eau abreuvement</span>
                                        <p class="font-medium text-slate-900">{{ $visiteElevage->eau_abreuvement ?? '—' }}</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">État bâtiments</span>
                                        <p class="font-medium text-slate-900">{{ $visiteElevage->etat_batiments_enclos ?? '—' }}/5</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <span class="text-slate-500">Production laitière</span>
                                        <p class="font-medium text-slate-900">{{ $visiteElevage->production_laitiere_l_j ?? '—' }} L/j</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Production œufs</span>
                                        <p class="font-medium text-slate-900">{{ $visiteElevage->production_oeufs_nb_j ?? '—' }}/j</p>
                                    </div>
                                    <div>
                                        <span class="text-slate-500">Gain de poids</span>
                                        <p class="font-medium text-slate-900">{{ $visiteElevage->gain_poids_kg_mois ?? '—' }} kg/mois</p>
                                    </div>
                                </div>

                                @if($visiteElevage->observations_sanitaires)
                                    <div class="text-sm">
                                        <span class="font-semibold text-slate-500">Observations sanitaires</span>
                                        <p class="text-slate-900 mt-1">{{ $visiteElevage->observations_sanitaires }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Autre activité --}}
                @if($rapport->type_activite?->value === 'autre' && $rapport->visiteAutres->isNotEmpty())
                    <div x-show="activeTab === 'autre'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="p-6">
                        @foreach($rapport->visiteAutres as $visiteAutre)
                            <div class="space-y-3 text-sm">
                                <div>
                                    <span class="text-slate-500">Type d'activité</span>
                                    <p class="font-medium text-slate-900 mt-1">{{ $visiteAutre->type_autre ?? '—' }}</p>
                                </div>
                                @if($visiteAutre->description_activite)
                                    <div>
                                        <span class="text-slate-500">Description</span>
                                        <p class="text-slate-900 mt-1">{{ $visiteAutre->description_activite }}</p>
                                    </div>
                                @endif
                                @if($visiteAutre->observations_specifiques)
                                    <div>
                                        <span class="text-slate-500">Observations spécifiques</span>
                                        <p class="text-slate-900 mt-1">{{ $visiteAutre->observations_specifiques }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Photos --}}
                <div x-show="activeTab === 'photos'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="p-6">
                    @if($rapport->photos->isNotEmpty())
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($rapport->photos as $photo)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $photo->chemin) }}" alt="{{ $photo->caption ?? 'Photo' }}" class="w-full h-32 object-cover rounded-lg">
                                    @if($photo->caption)
                                        <p class="text-xs text-slate-500 mt-1">{{ $photo->caption }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-400 text-center py-8">Aucune photo disponible</p>
                    @endif
                </div>

                {{-- Historique --}}
                <div x-show="activeTab === 'historique'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="p-6">
                    <div class="space-y-6">
                        {{-- Historique des validations --}}
                        @if($rapport->validations->isNotEmpty())
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-3">Historique des validations</h3>
                                <div class="space-y-3">
                                    @foreach($rapport->validations as $validation)
                                        <div class="flex items-center justify-between text-sm border-b border-slate-100 pb-2">
                                            <div>
                                                <span class="font-medium text-slate-900">{{ $validation->action }}</span>
                                                @if($validation->motif)
                                                    <span class="text-slate-500"> - {{ $validation->motif }}</span>
                                                @endif
                                            </div>
                                            <div class="text-slate-500">
                                                {{ $validation->admin?->name ?? 'N/A' }} - {{ $validation->date_action?->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Notes et messages --}}
                        @if($rapport->notesRapport->isNotEmpty())
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-3">Notes et messages</h3>
                                @foreach($rapport->notesRapport as $note)
                                    <div class="space-y-3 text-sm">
                                        @if($note->note_interne)
                                            <div>
                                                <span class="font-semibold text-slate-500">Note interne</span>
                                                <p class="text-slate-900 mt-1">{{ $note->note_interne }}</p>
                                            </div>
                                        @endif
                                        @if($note->message_client)
                                            <div>
                                                <span class="font-semibold text-slate-500">Message au client</span>
                                                <p class="text-slate-900 mt-1">{{ $note->message_client }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Résumé --}}
                <div x-show="activeTab === 'resume'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="p-6">
                    <div class="space-y-6">
                        {{-- Résumé de la visite --}}
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-3">Résumé de la visite</h3>
                            <p class="text-sm text-slate-700 leading-relaxed">{{ $rapport->resume_visite }}</p>
                        </div>

                        {{-- Alertes --}}
                        @if($rapport->niveau_alerte && $rapport->niveau_alerte !== 'aucune')
                            <div class="p-4 bg-red-50 border border-red-100 rounded-xl">
                                <h4 class="text-xs font-bold text-red-900 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Alerte — {{ $rapport->niveau_alerte }}
                                </h4>
                                @if($rapport->description_alerte)
                                    <p class="text-sm text-red-800 mt-1">{{ $rapport->description_alerte }}</p>
                                @endif
                            </div>
                        @endif

                        {{-- Observations finales --}}
                        @if($rapport->observationsFinales->isNotEmpty())
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-3">Observations générales</h3>
                                @foreach($rapport->observationsFinales as $observation)
                                    <div class="space-y-3 text-sm">
                                        <div>
                                            <span class="font-semibold text-slate-500">Résumé</span>
                                            <p class="text-slate-900 mt-1">{{ $observation->resume_visite }}</p>
                                        </div>
                                        @if($observation->niveau_alerte)
                                            <div>
                                                <span class="font-semibold text-slate-500">Niveau d'alerte</span>
                                                <p class="mt-1">
                                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                        {{ $observation->niveau_alerte }}
                                                    </span>
                                                </p>
                                            </div>
                                        @endif
                                        @if($observation->description_alerte)
                                            <div>
                                                <span class="font-semibold text-slate-500">Description</span>
                                                <p class="text-slate-900 mt-1">{{ $observation->description_alerte }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Prochaine visite --}}
                        @if($rapport->prochaineVisite->isNotEmpty())
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-3">Prochaine visite</h3>
                                @foreach($rapport->prochaineVisite as $prochaine)
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-slate-500">Date souhaitée</span>
                                            <p class="font-medium text-slate-900">{{ $prochaine->date_souhaitee?->format('d/m/Y') ?? '—' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-slate-500">Raison</span>
                                            <p class="font-medium text-slate-900">{{ $prochaine->raison ?? '—' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Notes --}}
                        @if($rapport->notesRapport->isNotEmpty())
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-3">Notes et messages</h3>
                                @foreach($rapport->notesRapport as $note)
                                    <div class="space-y-3 text-sm">
                                        @if($note->note_interne)
                                            <div>
                                                <span class="font-semibold text-slate-500">Note interne</span>
                                                <p class="text-slate-900 mt-1">{{ $note->note_interne }}</p>
                                            </div>
                                        @endif
                                        @if($note->message_client)
                                            <div>
                                                <span class="font-semibold text-slate-500">Message au client</span>
                                                <p class="text-slate-900 mt-1">{{ $note->message_client }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- Modal de rejet --}}
<div x-cloak x-show="rejectOpen" class="fixed inset-0 z-[100] overflow-y-auto" role="dialog" aria-modal="true" aria-labelledby="rejectModalTitle">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="rejectOpen = false"></div>
    <div class="relative flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="px-6 py-5 bg-red-600 text-white flex items-start justify-between">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 id="rejectModalTitle" class="text-lg font-bold">Rejeter le rapport</h3>
                        <p class="text-xs text-red-100 mt-0.5">Ce rapport sera marqué comme rejeté.</p>
                    </div>
                </div>
                <button type="button" @click="rejectOpen = false" class="text-white/80 hover:text-white transition-colors" aria-label="Fermer">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.rapports-visite.reject', $rapport->id) }}" class="px-6 py-6 space-y-4">
                @csrf
                <div>
                    <label for="motif_rejet" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Motif du rejet <span class="text-red-600">*</span>
                    </label>
                    <textarea name="motif_rejet" id="motif_rejet" rows="4" required maxlength="500"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-y"
                              placeholder="Expliquez la raison du rejet du rapport..."></textarea>
                    @error('motif_rejet') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="rejectOpen = false"
                            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 border border-red-600 rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Confirmer le rejet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection