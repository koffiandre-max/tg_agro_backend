@extends('layouts.app')

@section('page-title', 'Détails du Rapport de Visite')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Retour --}}
        <a href="{{ route('admin.rapports-visite.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4 transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour à la liste
        </a>

        {{-- En-tête --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Rapport de visite</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $rapport->localisation_parcelle }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.rapports-visite.edit', $rapport->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Modifier
                    </a>
                    <form action="{{ route('admin.rapports-visite.destroy', $rapport->id) }}" method="POST" onsubmit="return confirm('Supprimer ce rapport ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Date de visite</span>
                    <p class="font-medium text-gray-900">{{ $rapport->date_visite?->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Technicien</span>
                    <p class="font-medium text-gray-900">{{ $rapport->technicien?->name }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Client</span>
                    <p class="font-medium text-gray-900">{{ $rapport->client?->user?->name ?? $rapport->client?->nom }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Statut</span>
                    <p class="font-medium">
                        @if($rapport->statut)
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($rapport->statut->value === 'Validé') bg-green-100 text-green-700
                                @elseif($rapport->statut->value === 'Rejeté') bg-red-100 text-red-700
                                @elseif($rapport->statut->value === 'En attente de validation') bg-amber-100 text-amber-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ $rapport->statut->label() }}
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Informations générales --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Informations générales</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Type de visite</span>
                    <p class="font-medium text-gray-900">{{ $rapport->type_visite instanceof App\Enums\RapportVisiteType ? $rapport->type_visite->label() : $rapport->type_visite }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Conditions météo</span>
                    <p class="font-medium text-gray-900">{{ $rapport->conditions_meteo instanceof App\Enums\ConditionMeteo ? $rapport->conditions_meteo->label() : ($rapport->conditions_meteo ?? '-') }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Durée de visite</span>
                    <p class="font-medium text-gray-900">{{ $rapport->duree_visite instanceof App\Enums\DureeVisite ? $rapport->duree_visite->label() : ($rapport->duree_visite ?? '-') }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Superficie visitée</span>
                    <p class="font-medium text-gray-900">{{ $rapport->superficie_visitee ? number_format($rapport->superficie_visitee, 2) . ' ha' : '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Latitude</span>
                    <p class="font-medium text-gray-900">{{ $rapport->latitude ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Longitude</span>
                    <p class="font-medium text-gray-900">{{ $rapport->longitude ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Exploitation</span>
                    <p class="font-medium text-gray-900">{{ $rapport->farm?->name ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Étape 2 : Cultures --}}
        @if($rapport->visiteCultures)
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Informations sur les cultures</h2>

            @if($rapport->visiteCultures->typesCultures->isNotEmpty())
            <h3 class="text-sm font-medium text-gray-700 mb-2">Types de cultures</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-4">
                @foreach($rapport->visiteCultures->typesCultures as $type)
                <div class="flex items-center gap-2 text-sm">
                    <span class="{{ $type->present ? 'text-green-600' : 'text-gray-400' }}">
                        @if($type->present) ✓ @else ✗ @endif
                    </span>
                    <span class="{{ $type->present ? 'text-gray-900' : 'text-gray-400' }}">
                        {{ $type->culture_type instanceof App\Enums\CultureType ? $type->culture_type->label() : $type->culture_type }}
                        @if($type->culture_autre_detail) ({{ $type->culture_autre_detail }}) @endif
                    </span>
                </div>
                @endforeach
            </div>
            @endif

            @if($rapport->visiteCultures->etatsVegetatifs->isNotEmpty())
            <h3 class="text-sm font-medium text-gray-700 mb-2">États végétatifs</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm mb-4">
                @foreach($rapport->visiteCultures->etatsVegetatifs as $etat)
                <div>
                    <span class="text-gray-500">Stade: </span>
                    <span class="font-medium text-gray-900">{{ $etat->stade_phenologique instanceof App\Enums\StadePhenologique ? $etat->stade_phenologique->label() : ($etat->stade_phenologique ?? '-') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Avancement: </span>
                    <span class="font-medium text-gray-900">{{ $etat->avancement_cycle ?? '-' }}%</span>
                </div>
                <div>
                    <span class="text-gray-500">État couvert: </span>
                    <span class="font-medium text-gray-900">{{ $etat->etat_couvert ?? '-' }}/5</span>
                </div>
                @endforeach
            </div>
            @endif

            @if($rapport->visiteCultures->ravageursMaladies->isNotEmpty())
            <h3 class="text-sm font-medium text-gray-700 mb-2">Ravageurs et maladies</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-4">
                @foreach($rapport->visiteCultures->ravageursMaladies as $ravageur)
                <div class="flex items-center gap-2 text-sm">
                    <span class="{{ $ravageur->present ? 'text-red-600' : 'text-gray-400' }}">
                        @if($ravageur->present) ✓ @else ✗ @endif
                    </span>
                    <span class="{{ $ravageur->present ? 'text-gray-900' : 'text-gray-400' }}">
                        {{ $ravageur->type_probleme instanceof App\Enums\TypeProbleme ? $ravageur->type_probleme->label() : $ravageur->type_probleme }}
                        @if($ravageur->niveau_infestation) (niveau: {{ $ravageur->niveau_infestation }}/5) @endif
                    </span>
                </div>
                @endforeach
            </div>
            @endif

            @if($rapport->visiteCultures->solsIrrigations->isNotEmpty())
            <h3 class="text-sm font-medium text-gray-700 mb-2">Sol et irrigation</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm mb-4">
                @foreach($rapport->visiteCultures->solsIrrigations as $sol)
                <div>
                    <span class="text-gray-500">État hydrique: </span>
                    <span class="font-medium text-gray-900">{{ $sol->etat_hydrique_sol instanceof App\Enums\EtatHydriqueSol ? $sol->etat_hydrique_sol->label() : ($sol->etat_hydrique_sol ?? '-') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Irrigation en place: </span>
                    <span class="font-medium text-gray-900">{{ $sol->irrigation_place === null ? '-' : ($sol->irrigation_place ? 'Oui' : 'Non') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Structure du sol: </span>
                    <span class="font-medium text-gray-900">{{ $sol->etat_structure_sol instanceof App\Enums\EtatStructureSol ? $sol->etat_structure_sol->label() : ($sol->etat_structure_sol ?? '-') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">pH du sol: </span>
                    <span class="font-medium text-gray-900">{{ $sol->ph_sol_mesure ?? '-' }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if($rapport->visiteCultures->entretienIntrants->isNotEmpty())
            <h3 class="text-sm font-medium text-gray-700 mb-2">Entretien et intrants</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                @foreach($rapport->visiteCultures->entretienIntrants as $entretien)
                <div>Désherbage: <span class="font-medium">{{ $entretien->desherbage_effectue ? 'Oui' : 'Non' }}</span></div>
                <div>Taille/élagage: <span class="font-medium">{{ $entretien->taille_elagage ? 'Oui' : 'Non' }}</span></div>
                <div>Engrais: <span class="font-medium">{{ $entretien->engrais_applique ? 'Oui' : 'Non' }}</span></div>
                <div>Traitement phytosanitaire: <span class="font-medium">{{ $entretien->traitement_phytosanitaire ? 'Oui' : 'Non' }}</span></div>
                <div>Mulching: <span class="font-medium">{{ $entretien->mulching_realise ? 'Oui' : 'Non' }}</span></div>
                <div>Compost: <span class="font-medium">{{ $entretien->compost_apporte ? 'Oui' : 'Non' }}</span></div>
                <div>Intrants utilisés: <span class="font-medium">{{ $entretien->intrants_utilises ?? '-' }}</span></div>
                <div>Estimation récolte: <span class="font-medium">{{ $entretien->estimation_recolte ? number_format($entretien->estimation_recolte, 2) : '-' }}</span></div>
                <div>Date récolte estimée: <span class="font-medium">{{ $entretien->date_estimee_recolte?->format('d/m/Y') ?? '-' }}</span></div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- Étape 3 : Élevage --}}
        @if($rapport->visiteElevage)
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Informations sur l'élevage</h2>

            @if($rapport->visiteElevage->typesAnimaux->isNotEmpty())
            <h3 class="text-sm font-medium text-gray-700 mb-2">Types d'animaux</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm mb-4">
                @foreach($rapport->visiteElevage->typesAnimaux as $animal)
                <div>
                    <span class="{{ $animal->present ? 'text-gray-900' : 'text-gray-400' }}">
                        {{ $animal->animal_type instanceof App\Enums\AnimalType ? $animal->animal_type->label() : $animal->animal_type }}
                    </span>
                    @if($animal->present)
                    <div class="text-xs text-gray-500 mt-1">
                        Effectif: {{ $animal->effectif_total ?? '-' }} |
                        Mortalité: {{ $animal->mortalite_constatee ?? 0 }} |
                        Naissances: {{ $animal->naissances_visite ?? '-' }} |
                        Ventes/abattages: {{ $animal->ventes_abatages ?? '-' }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            @if($rapport->visiteElevage->signesCliniques->isNotEmpty())
            <h3 class="text-sm font-medium text-gray-700 mb-2">Signes cliniques</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-4">
                @foreach($rapport->visiteElevage->signesCliniques as $signe)
                <div class="flex items-center gap-2 text-sm">
                    <span class="{{ $signe->present ? 'text-red-600' : 'text-gray-400' }}">
                        @if($signe->present) ✓ @else ✗ @endif
                    </span>
                    <span>{{ $signe->type_signe instanceof App\Enums\TypeSigneClinique ? $signe->type_signe->label() : $signe->type_signe }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if($rapport->visiteElevage->soinsAnimaux->isNotEmpty())
            <h3 class="text-sm font-medium text-gray-700 mb-2">Soins et traitements</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm mb-4">
                @foreach($rapport->visiteElevage->soinsAnimaux as $soin)
                <div>Vaccination: <span class="font-medium">{{ $soin->vaccination_effectuee ? 'Oui' : 'Non' }}</span></div>
                <div>Déparasitage interne: <span class="font-medium">{{ $soin->deparasitage_interne ? 'Oui' : 'Non' }}</span></div>
                <div>Déparasitage externe: <span class="font-medium">{{ $soin->deparasitage_externe ? 'Oui' : 'Non' }}</span></div>
                <div>Antibiotique: <span class="font-medium">{{ $soin->traitement_antibiotique ? 'Oui' : 'Non' }}</span></div>
                <div>Soins plaies: <span class="font-medium">{{ $soin->soins_plaies ? 'Oui' : 'Non' }}</span></div>
                <div>Consultation vétérinaire: <span class="font-medium">{{ $soin->consultation_veterinaire ? 'Oui' : 'Non' }}</span></div>
                <div>Produits administrés: <span class="font-medium">{{ $soin->produits_administres ?? '-' }}</span></div>
                @endforeach
            </div>
            @endif

            @if($rapport->visiteElevage->alimentationEau->isNotEmpty())
            <h3 class="text-sm font-medium text-gray-700 mb-2">Alimentation, eau et logement</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm mb-4">
                @foreach($rapport->visiteElevage->alimentationEau as $alim)
                <div>Alimentation: <span class="font-medium">{{ $alim->etat_alimentation instanceof App\Enums\EtatAlimentation ? $alim->etat_alimentation->label() : ($alim->etat_alimentation ?? '-') }}</span></div>
                <div>Eau abreuvement: <span class="font-medium">{{ $alim->eau_abreuvement instanceof App\Enums\EauAbreuvement ? $alim->eau_abreuvement->label() : ($alim->eau_abreuvement ?? '-') }}</span></div>
                <div>État bâtiments: <span class="font-medium">{{ $alim->etat_batiments ?? '-' }}/5</span></div>
                @endforeach
            </div>
            @endif

            @if($rapport->visiteElevage->performancesElevage->isNotEmpty())
            <h3 class="text-sm font-medium text-gray-700 mb-2">Performances productives</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                @foreach($rapport->visiteElevage->performancesElevage as $perf)
                <div>Production laitière: <span class="font-medium">{{ $perf->production_laitiere ?? '-' }}</span></div>
                <div>Production œufs: <span class="font-medium">{{ $perf->production_oeufs ?? '-' }}</span></div>
                <div>Gain de poids: <span class="font-medium">{{ $perf->gain_poids ?? '-' }}</span></div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- Étape 4 : Photos --}}
        @if($rapport->photos->isNotEmpty())
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Photos terrain ({{ $rapport->photos->count() }})</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($rapport->photos as $photo)
                <div class="relative group">
                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="{{ $photo->caption ?? 'Photo' }}" class="w-full h-32 object-cover rounded-lg">
                    @if($photo->caption)
                    <p class="text-xs text-gray-500 mt-1">{{ $photo->caption }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Observations finales --}}
        @if($rapport->observationsFinales)
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Observations générales et alertes</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="text-gray-500">Résumé de la visite:</span>
                    <p class="text-gray-900 mt-1">{{ $rapport->observationsFinales->resume_visite }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Niveau d'alerte:</span>
                    <p class="mt-1">
                        @if($rapport->observationsFinales->niveau_alerte)
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($rapport->observationsFinales->niveau_alerte->value === 'Aucune alerte') bg-gray-100 text-gray-700
                                @elseif($rapport->observationsFinales->niveau_alerte->value === 'Alerte faible (information)') bg-blue-100 text-blue-700
                                @elseif($rapport->observationsFinales->niveau_alerte->value === 'Alerte modérée (surveillance)') bg-amber-100 text-amber-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $rapport->observationsFinales->niveau_alerte->label() }}
                            </span>
                        @endif
                    </p>
                </div>
                @if($rapport->observationsFinales->description_alerte)
                <div>
                    <span class="text-gray-500">Description de l'alerte:</span>
                    <p class="text-gray-900 mt-1">{{ $rapport->observationsFinales->description_alerte }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Prochaine visite --}}
        @if($rapport->prochaineVisite)
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Prochaine visite recommandée</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Date souhaitée:</span>
                    <p class="font-medium text-gray-900">{{ $rapport->prochaineVisite->date_souhaitee?->format('d/m/Y') ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500">Raison:</span>
                    <p class="font-medium text-gray-900">{{ $rapport->prochaineVisite->raison instanceof App\Enums\RaisonProchaineVisite ? $rapport->prochaineVisite->raison->label() : ($rapport->prochaineVisite->raison ?? '-') }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Notes --}}
        @if($rapport->notesRapport)
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Notes et messages</h2>
            <div class="space-y-3 text-sm">
                @if($rapport->notesRapport->note_interne)
                <div>
                    <span class="text-gray-500">Note interne:</span>
                    <p class="text-gray-900 mt-1">{{ $rapport->notesRapport->note_interne }}</p>
                </div>
                @endif
                @if($rapport->notesRapport->message_client)
                <div>
                    <span class="text-gray-500">Message au client:</span>
                    <p class="text-gray-900 mt-1">{{ $rapport->notesRapport->message_client }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Historique des validations --}}
        @if($rapport->validations->isNotEmpty())
        <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4">Historique des validations</h2>
            <div class="space-y-3">
                @foreach($rapport->validations as $validation)
                <div class="flex items-center justify-between text-sm border-b border-gray-100 pb-2">
                    <div>
                        <span class="font-medium text-gray-900">{{ $validation->action }}</span>
                        @if($validation->motif)
                        <span class="text-gray-500"> - {{ $validation->motif }}</span>
                        @endif
                    </div>
                    <div class="text-gray-500">
                        {{ $validation->admin?->name ?? 'N/A' }} - {{ $validation->date_action?->format('d/m/Y H:i') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection