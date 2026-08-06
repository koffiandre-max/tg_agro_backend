@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white p-10 font-serif text-slate-800">

    {{-- LETTERHEAD --}}
    <div class="text-center pb-4 mb-1 border-b-2 border-double border-slate-800">
        <p class="text-[10px] tracking-[0.3em] uppercase text-slate-500 mb-1">TG Agro Consulting · Diaspor'Invest</p>
        <h1 class="text-2xl font-bold uppercase tracking-wide text-slate-900">Rapport de Visite Terrain</h1>
        <p class="text-[11px] text-slate-500 mt-2 italic">Rapport N° {{ $rapport->id }} — Établi le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    {{-- STATUT --}}
    <div class="text-center mt-2 mb-8">
        @php
            $statutClasses = match($rapport->statut?->value) {
                'valide' => 'border-slate-800 text-slate-800',
                'en_attente_validation' => 'border-slate-500 text-slate-600',
                'rejete' => 'border-slate-800 text-slate-800',
                default => 'border-slate-400 text-slate-500',
            };
            $statutLabel = $rapport->statut?->label() ?? 'Brouillon';
        @endphp
        {{-- <span class="inline-block px-6 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] border {{ $statutClasses }}">
            {{ $statutLabel }}
        </span> --}}
    </div>

    {{-- ÉTAPE 1 : INFOS GÉNÉRALES --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
            I. Informations générales
        </h2>
        <div class="grid grid-cols-2 gap-x-10 gap-y-0.5 text-[12.5px]">
            <div class="flex border-b border-slate-200 py-1.5">
                <span class="font-semibold text-slate-700 w-[42%]">Technicien</span>
                <span class="w-[58%]">{{ $rapport->technicien?->name ?? '—' }}</span>
            </div>
            <div class="flex border-b border-slate-200 py-1.5">
                <span class="font-semibold text-slate-700 w-[42%]">Date de visite</span>
                <span class="w-[58%]">{{ $rapport->date_visite?->format('d/m/Y') ?? '—' }}</span>
            </div>
            <div class="flex border-b border-slate-200 py-1.5">
                <span class="font-semibold text-slate-700 w-[42%]">Client (propriétaire)</span>
                <span class="w-[58%]">{{ $rapport->client?->user?->name ?? $rapport->client?->nom ?? '—' }}</span>
            </div>
            <div class="flex border-b border-slate-200 py-1.5">
                <span class="font-semibold text-slate-700 w-[42%]">Exploitation</span>
                <span class="w-[58%]">{{ $rapport->farm?->name ?? '—' }}</span>
            </div>
            <div class="col-span-2 flex border-b border-slate-200 py-1.5">
                <span class="font-semibold text-slate-700 w-[21%]">Localisation parcelle</span>
                <span class="w-[79%]">{{ $rapport->localisation_parcelle ?? '—' }}</span>
            </div>
            <div class="flex border-b border-slate-200 py-1.5">
                <span class="font-semibold text-slate-700 w-[42%]">Type de visite</span>
                <span class="w-[58%]">{{ $rapport->type_visite ?? '—' }}</span>
            </div>
            <div class="flex border-b border-slate-200 py-1.5">
                <span class="font-semibold text-slate-700 w-[42%]">Conditions météo</span>
                <span class="w-[58%]">{{ $rapport->conditions_meteo ?? '—' }}</span>
            </div>
            <div class="flex border-b border-slate-200 py-1.5">
                <span class="font-semibold text-slate-700 w-[42%]">Durée de la visite</span>
                <span class="w-[58%]">{{ $rapport->duree_visite ?? '—' }}</span>
            </div>
            <div class="flex border-b border-slate-200 py-1.5">
                <span class="font-semibold text-slate-700 w-[42%]">Superficie visitée (ha)</span>
                <span class="w-[58%]">{{ $rapport->superficie_visitee_ha ? number_format($rapport->superficie_visitee_ha, 2) . ' ha' : '—' }}</span>
            </div>
            <div class="col-span-2 flex border-b border-slate-200 py-1.5">
                <span class="font-semibold text-slate-700 w-[21%]">Coordonnées GPS</span>
                <span class="w-[79%]">{{ $rapport->gps_latitude ?? '—' }}, {{ $rapport->gps_longitude ?? '—' }}</span>
            </div>
        </div>
    </div>

    {{-- ÉTAPE 2 : CULTURES --}}
    @if($rapport->type_activite?->value === 'culture' && $rapport->visiteCultures->isNotEmpty())
    @foreach($rapport->visiteCultures as $visiteCulture)
    <div class="mb-8">
        <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
            II. Cultures
        </h2>
        <table class="w-full border-collapse text-[12px]">
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2 w-[18%]">Cultures présentes</td>
                <td class="border border-slate-300 p-2">{{ is_array($visiteCulture->cultures_presentes) ? implode(', ', $visiteCulture->cultures_presentes) : ($visiteCulture->cultures_presentes ?? '—') }}</td>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2 w-[18%]">Stade phénologique</td>
                <td class="border border-slate-300 p-2">{{ $visiteCulture->stade_phenologique ?? '—' }}</td>
            </tr>
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Avancement du cycle (%)</td>
                <td class="border border-slate-300 p-2">{{ $visiteCulture->avancement_cycle_pourcent ? $visiteCulture->avancement_cycle_pourcent . '%' : '—' }}</td>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">État couvert végétal (/5)</td>
                <td class="border border-slate-300 p-2">{{ $visiteCulture->etat_couvert_vegetal ? $visiteCulture->etat_couvert_vegetal . '/5' : '—' }}</td>
            </tr>
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Ravageurs / Maladies</td>
                <td class="border border-slate-300 p-2">{{ is_array($visiteCulture->ravageurs_maladies) ? implode(', ', $visiteCulture->ravageurs_maladies) : ($visiteCulture->ravageurs_maladies ?? '—') }}</td>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Niveau infestation (/5)</td>
                <td class="border border-slate-300 p-2">{{ $visiteCulture->niveau_infestation ? $visiteCulture->niveau_infestation . '/5' : '—' }}</td>
            </tr>
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">État hydrique du sol</td>
                <td class="border border-slate-300 p-2">{{ $visiteCulture->etat_hydrique_sol ?? '—' }}</td>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Irrigation en place</td>
                <td class="border border-slate-300 p-2">{{ $visiteCulture->irrigation_en_place === null ? '—' : ($visiteCulture->irrigation_en_place ? 'Oui' : 'Non') }}</td>
            </tr>
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Structure du sol</td>
                <td class="border border-slate-300 p-2">{{ $visiteCulture->etat_structure_sol ?? '—' }}</td>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">pH du sol</td>
                <td class="border border-slate-300 p-2">{{ $visiteCulture->ph_sol ?? '—' }}</td>
            </tr>
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Estimation récolte (kg)</td>
                <td class="border border-slate-300 p-2">{{ $visiteCulture->estimation_recolte_kg ? number_format($visiteCulture->estimation_recolte_kg, 2) . ' kg' : '—' }}</td>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Date récolte estimée</td>
                <td class="border border-slate-300 p-2">{{ $visiteCulture->date_estimee_recolte?->format('d/m/Y') ?? '—' }}</td>
            </tr>
            @if($visiteCulture->observations_ravageurs)
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Observations ravageurs</td>
                <td class="border border-slate-300 p-2" colspan="3">{{ $visiteCulture->observations_ravageurs }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endforeach
    @endif

    {{-- ÉTAPE 3 : ÉLEVAGE --}}
    @if($rapport->type_activite?->value === 'elevage' && $rapport->visiteElevage->isNotEmpty())
    @foreach($rapport->visiteElevage as $visiteElevage)
    <div class="mb-8">
        <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
            III. Élevage
        </h2>
        <table class="w-full border-collapse text-[12px]">
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2 w-[18%]">Animaux présents</td>
                <td class="border border-slate-300 p-2">{{ is_array($visiteElevage->animaux_presents) ? implode(', ', $visiteElevage->animaux_presents) : ($visiteElevage->animaux_presents ?? '—') }}</td>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2 w-[18%]">Effectif total</td>
                <td class="border border-slate-300 p-2">{{ $visiteElevage->effectif_total ?? '—' }}</td>
            </tr>
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Mortalité constatée</td>
                <td class="border border-slate-300 p-2">{{ $visiteElevage->mortalite_constatee ?? '—' }}</td>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Naissances</td>
                <td class="border border-slate-300 p-2">{{ $visiteElevage->naissances_depuis_derniere_visite ?? '—' }}</td>
            </tr>
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Ventes / abattages</td>
                <td class="border border-slate-300 p-2">{{ $visiteElevage->ventes_abattages_depuis_derniere_visite ?? '—' }}</td>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">État corporel (/5)</td>
                <td class="border border-slate-300 p-2">{{ $visiteElevage->etat_corporel_general ? $visiteElevage->etat_corporel_general . '/5' : '—' }}</td>
            </tr>
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Signes cliniques</td>
                <td class="border border-slate-300 p-2">{{ is_array($visiteElevage->signes_cliniques) ? implode(', ', $visiteElevage->signes_cliniques) : ($visiteElevage->signes_cliniques ?? '—') }}</td>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">État alimentation</td>
                <td class="border border-slate-300 p-2">{{ $visiteElevage->etat_alimentation ?? '—' }}</td>
            </tr>
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Eau d'abreuvement</td>
                <td class="border border-slate-300 p-2">{{ $visiteElevage->eau_abreuvement ?? '—' }}</td>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">État bâtiments (/5)</td>
                <td class="border border-slate-300 p-2">{{ $visiteElevage->etat_batiments_enclos ? $visiteElevage->etat_batiments_enclos . '/5' : '—' }}</td>
            </tr>
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Production laitière (L/j)</td>
                <td class="border border-slate-300 p-2">{{ $visiteElevage->production_laitiere_l_j ? $visiteElevage->production_laitiere_l_j . ' L/j' : '—' }}</td>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Production œufs (/j)</td>
                <td class="border border-slate-300 p-2">{{ $visiteElevage->production_oeufs_nb_j ? $visiteElevage->production_oeufs_nb_j . '/j' : '—' }}</td>
            </tr>
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Gain de poids (kg/mois)</td>
                <td class="border border-slate-300 p-2" colspan="3">{{ $visiteElevage->gain_poids_kg_mois ? $visiteElevage->gain_poids_kg_mois . ' kg/mois' : '—' }}</td>
            </tr>
            @if($visiteElevage->observations_sanitaires)
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Observations sanitaires</td>
                <td class="border border-slate-300 p-2" colspan="3">{{ $visiteElevage->observations_sanitaires }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endforeach
    @endif

    {{-- ÉTAPE 3 : AUTRE --}}
    @if($rapport->type_activite?->value === 'autre' && $rapport->visiteAutres->isNotEmpty())
    @foreach($rapport->visiteAutres as $visiteAutre)
    <div class="mb-8">
        <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
            III. Autre activité
        </h2>
        <table class="w-full border-collapse text-[12px]">
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2 w-[18%]">Type d'activité</td>
                <td class="border border-slate-300 p-2">{{ $visiteAutre->type_autre ?? '—' }}</td>
            </tr>
            @if($visiteAutre->description_activite)
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Description</td>
                <td class="border border-slate-300 p-2">{{ $visiteAutre->description_activite }}</td>
            </tr>
            @endif
            @if($visiteAutre->observations_specifiques)
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2">Observations spécifiques</td>
                <td class="border border-slate-300 p-2">{{ $visiteAutre->observations_specifiques }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endforeach
    @endif

    {{-- ÉTAPE 4 : PHOTOS & OBSERVATIONS --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
            IV. Photos &amp; observations finales
        </h2>

        {{-- PHOTOS --}}
        <div class="mb-4">
            <p class="font-semibold text-slate-700 text-[12px] mb-1.5">Photos terrain</p>
            <div class="flex flex-wrap gap-2">
                @if(isset($rapport->photos) && count($rapport->photos) > 0)
                    @foreach($rapport->photos as $photo)
                        <div class="border border-slate-300 px-3 py-2 text-[11px] text-slate-600">
                            {{ $photo->legende ?? 'Photo' }}
                        </div>
                    @endforeach
                @else
                    <div class="border border-slate-300 px-3 py-2 text-[11px] text-slate-500 italic">
                        Aucune photo jointe
                    </div>
                @endif
            </div>
            <p class="text-slate-400 text-[10px] mt-1.5 italic">Max 10 photos · horodatées &amp; géolocalisées</p>
        </div>

        {{-- RÉSUMÉ --}}
        <table class="w-full border-collapse text-[12px]">
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2 w-[20%]">Résumé de la visite</td>
                <td class="border border-slate-300 p-2">{{ $rapport->resume_visite ?? '—' }}</td>
            </tr>
        </table>

        {{-- ALERTE --}}
        @if($rapport->niveau_alerte)
            @php
                $alertLabel = match($rapport->niveau_alerte) {
                    'urgente' => 'Alerte urgente — action immédiate',
                    'moderee' => 'Alerte modérée — surveillance',
                    'faible' => 'Alerte faible — information',
                    default => 'Aucune alerte',
                };
            @endphp
            <div class="border-l-4 border-slate-800 bg-slate-50 p-3 mt-3 text-[12px]">
                <strong class="uppercase tracking-wide">{{ $alertLabel }}</strong>
                @if($rapport->description_alerte)
                    <br>{{ $rapport->description_alerte }}
                @endif
            </div>
        @endif

        {{-- PROCHAINE VISITE --}}
        @if($rapport->prochaine_visite_date || $rapport->prochaine_visite_raison)
        <div class="mt-4">
            <p class="font-semibold text-slate-700 text-[12px] mb-1">Prochaine visite recommandée</p>
            <div class="grid grid-cols-2 gap-x-10 gap-y-0.5 text-[12.5px]">
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Date souhaitée</span>
                    <span class="w-[58%]">{{ $rapport->prochaine_visite_date?->format('d/m/Y') ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Raison</span>
                    <span class="w-[58%]">{{ $rapport->prochaine_visite_raison ?? '—' }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ÉTAPE 5 : RÉSUMÉ & ENVOI --}}
    <div class="mb-8">
        <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
            V. Résumé &amp; envoi
        </h2>

        @if($rapport->note_interne || $rapport->message_client)
        <table class="w-full border-collapse text-[12px]">
            @if($rapport->note_interne)
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2 w-[20%]">Note interne (admin)</td>
                <td class="border border-slate-300 p-2">{{ $rapport->note_interne }}</td>
            </tr>
            @endif
            @if($rapport->message_client)
            <tr>
                <td class="border border-slate-300 bg-slate-50 font-semibold text-slate-700 p-2 w-[20%]">Message client</td>
                <td class="border border-slate-300 p-2">{{ $rapport->message_client }}</td>
            </tr>
            @endif
        </table>
        @else
        <p class="text-slate-500 text-[12px] italic py-1">Aucune note ou message enregistré.</p>
        @endif

        <div class="mt-3 border border-slate-300 px-3.5 py-2 text-[11.5px] text-slate-700">
            <span class="font-semibold">Statut du rapport :</span>
            @if($rapport->statut?->value === 'valide')
                Validé par l'admin · visible par le client
            @elseif($rapport->statut?->value === 'en_attente_validation')
                En attente de validation admin
            @elseif($rapport->statut?->value === 'rejete')
                Rejeté · motif disponible
            @else
                Brouillon · non envoyé
            @endif
        </div>
    </div>

    {{-- SIGNATURES --}}
    <div class="flex justify-between mt-12 pt-6 border-t-2 border-double border-slate-800">
        <div class="text-center w-[42%]">
            <p class="border-t border-slate-800 pt-1.5 mt-8 text-[11.5px] font-semibold">{{ $rapport->technicien?->name ?? '________________________' }}</p>
            <p class="text-[10px] text-slate-500 mt-0.5 uppercase tracking-wide">Technicien</p>
        </div>
        <div class="text-center w-[42%]">
            <p class="border-t border-slate-800 pt-1.5 mt-8 text-[11.5px] font-semibold">{{ $rapport->validePar?->name ?? '________________________' }}</p>
            <p class="text-[10px] text-slate-500 mt-0.5 uppercase tracking-wide">Validé par (admin)</p>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="mt-10 text-center text-[10px] text-slate-400 border-t border-slate-200 pt-4">
        <p>TG AGRO CONSULTING · Montpellier · Juillet 2026 · Document généré automatiquement</p>
        <p class="mt-0.5">Rapport #{{ $rapport->id }} · {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

</div>
@endsection