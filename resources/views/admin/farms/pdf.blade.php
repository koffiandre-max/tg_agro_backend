@extends('layouts.app')

@section('title', 'Fiche exploitation - ' . ($farm->name ?? 'Exploitation'))

@section('content')
<div class="min-h-screen bg-white">
    <div class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
            <a href="{{ route('admin.farms.show', $farm->id) }}" class="inline-flex items-center text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Retour à la fiche
            </a>
            <button type="button" id="btn-print-farm" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-slate-900 rounded-lg hover:bg-slate-800 active:bg-slate-950 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.548 42.548 0 0110.56 0m-10.56 0L6.34 17.5m-3.12-3.12l1.06-1.06M18.66 17.5l-1.06-1.06m-12.02 3.06l.72.096m10.56 0l-.72-.096M6.34 17.5h11.32M17.5 6.34v11.32" />
                </svg>
                Imprimer le PDF
            </button>
        </div>
    </div>

    <div id="farm-print-content" class="p-10 font-serif text-slate-800">
        {{-- LETTERHEAD --}}
        <div class="text-center pb-4 mb-1 border-b-2 border-double border-slate-800">
            <p class="text-[10px] tracking-[0.3em] uppercase text-slate-500 mb-1">TG Agro Consulting · Diaspor'Invest</p>
            <h1 class="text-2xl font-bold uppercase tracking-wide text-slate-900">Fiche d'Exploitation Agricole</h1>
            <p class="text-[11px] text-slate-500 mt-2 italic">Fiche N° {{ $farm->reference_dossier ?? '—' }} — Établie le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>

        {{-- ÉTAPE 1 : INFORMATIONS GÉNÉRALES --}}
        <div class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
                I. Informations générales
            </h2>
            <div class="grid grid-cols-2 gap-x-10 gap-y-0.5 text-[12.5px]">
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Nom exploitation</span>
                    <span class="w-[58%]">{{ $farm->name ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Client (propriétaire)</span>
                    <span class="w-[58%]">{{ $farm->user->name ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Localisation</span>
                    <span class="w-[58%]">{{ $farm->location ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Type de culture</span>
                    <span class="w-[58%]">{{ $farm->culture_type ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Type d'exploitation</span>
                    <span class="w-[58%]">{{ $farm->type?->label() ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Surface totale</span>
                    <span class="w-[58%]">{{ number_format($farm->total_area_hectares, 2) }} ha</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Statut</span>
                    <span class="w-[58%]">{{ $farm->statusLabel() }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Stade de culture</span>
                    <span class="w-[58%]">{{ $farm->crop_stage ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Progression</span>
                    <span class="w-[58%]">{{ $farm->crop_stage_progress ?? 0 }}%</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Récolte prévue</span>
                    <span class="w-[58%]">{{ $farm->expected_harvest_date?->format('d/m/Y') ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Dernière visite</span>
                    <span class="w-[58%]">{{ $farm->last_visit_date?->format('d/m/Y') ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- ÉTAPE 2 : CARACTÉRISTIQUES PARCELLE --}}
        <div class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
                II. Caractéristiques de la parcelle
            </h2>
            <div class="grid grid-cols-2 gap-x-10 gap-y-0.5 text-[12.5px]">
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Numéro cadastral</span>
                    <span class="w-[58%]">{{ $farm->numero_cadastral ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Date déclaration</span>
                    <span class="w-[58%]">{{ $farm->date_declaration?->format('d/m/Y') ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Nom client</span>
                    <span class="w-[58%]">{{ $farm->nom_client ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Contact</span>
                    <span class="w-[58%]">{{ $farm->contact ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Surface totale</span>
                    <span class="w-[58%]">{{ $farm->surface_totale ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Surface cultivable</span>
                    <span class="w-[58%]">{{ $farm->surface_cultivable ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Forme parcelle</span>
                    <span class="w-[58%]">{{ $farm->forme_parcelle ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Exposition</span>
                    <span class="w-[58%]">{{ $farm->exposition_principale ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Pente</span>
                    <span class="w-[58%]">{{ $farm->pente_moyenne ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Altitude</span>
                    <span class="w-[58%]">{{ $farm->altitude ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Topographie</span>
                    <span class="w-[58%]">{{ $farm->topographie ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- ÉTAPE 3 : SOLS --}}
        <div class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
                III. Sols
            </h2>
            <div class="grid grid-cols-2 gap-x-10 gap-y-0.5 text-[12.5px]">
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Type de sol</span>
                    <span class="w-[58%]">{{ $farm->type_sol ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Couleur</span>
                    <span class="w-[58%]">{{ $farm->couleur_sol ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Profondeur</span>
                    <span class="w-[58%]">{{ $farm->profondeur_sol ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Présence cailloux</span>
                    <span class="w-[58%]">{{ $farm->presence_cailloux ? 'Oui' : 'Non' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">pH</span>
                    <span class="w-[58%]">{{ $farm->ph ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Source pH</span>
                    <span class="w-[58%]">{{ $farm->source_ph ?? '—' }}</span>
                </div>
                @if($farm->analyse_sol_realisee_precision)
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Précision analyse sol</span>
                    <span class="w-[58%]">{{ $farm->analyse_sol_realisee_precision }}</span>
                </div>
                @endif
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Commentaire analyse</span>
                    <span class="w-[58%]">{{ $farm->commentaire_analyse ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- ÉTAPE 4 : EAU / IRRIGATION --}}
        <div class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
                IV. Eau / Irrigation
            </h2>
            <div class="grid grid-cols-2 gap-x-10 gap-y-0.5 text-[12.5px]">
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Point d'eau à proximité</span>
                    <span class="w-[58%]">{{ $farm->point_eau_proximite ? 'Oui' : 'Non' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Type point d'eau</span>
                    <span class="w-[58%]">{{ $farm->type_point_eau ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Distance</span>
                    <span class="w-[58%]">{{ $farm->distance_point_eau ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Système irrigation</span>
                    <span class="w-[58%]">{{ $farm->systeme_irrigation ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Type irrigation</span>
                    <span class="w-[58%]">{{ $farm->type_irrigation ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Inondations saisonnières</span>
                    <span class="w-[58%]">{{ $farm->inondations_saisonnieres ? 'Oui' : 'Non' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Période sèche</span>
                    <span class="w-[58%]">{{ $farm->periode_secheresse ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- ÉTAPE 5 : VÉGÉTATION --}}
        <div class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
                V. Végétation
            </h2>
            <div class="grid grid-cols-2 gap-x-10 gap-y-0.5 text-[12.5px]">
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Occupation actuelle</span>
                    <span class="w-[58%]">{{ $farm->occupation_actuelle ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Cultures en place</span>
                    <span class="w-[58%]">{{ $farm->cultures_place ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Présence arbres</span>
                    <span class="w-[58%]">{{ $farm->presence_arbres ? 'Oui' : 'Non' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Espèces ligneuses</span>
                    <span class="w-[58%]">{{ $farm->especes_ligneuses ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Rendement actuel</span>
                    <span class="w-[58%]">{{ $farm->rendement_actuel ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Antécédents traitement</span>
                    <span class="w-[58%]">{{ $farm->antecedents_traitement ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- ÉTAPE 6 : PRODUITS PHYTOSANITAIRES --}}
        <div class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
                VI. Produits phytosanitaires
            </h2>
            <div class="grid grid-cols-2 gap-x-10 gap-y-0.5 text-[12.5px]">
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Herbicides</span>
                    <span class="w-[58%]">{{ $farm->produits_herbicides ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Pesticides</span>
                    <span class="w-[58%]">{{ $farm->produits_pesticides ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Engrais</span>
                    <span class="w-[58%]">{{ $farm->produits_engrais ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Autres</span>
                    <span class="w-[58%]">{{ $farm->produits_autre ?? '—' }}</span>
                </div>
                @if($farm->produits_autre_detail)
                <div class="col-span-2 flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[21%]">Détail autres</span>
                    <span class="w-[79%]">{{ $farm->produits_autre_detail }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- ÉTAPE 7 : ACCÈS / INFRASTRUCTURES --}}
        <div class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
                VII. Accès / Infrastructures
            </h2>
            <div class="grid grid-cols-2 gap-x-10 gap-y-0.5 text-[12.5px]">
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Accès carrossable</span>
                    <span class="w-[58%]">{{ $farm->acces_carrossable ? 'Oui' : 'Non' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Distance route principale</span>
                    <span class="w-[58%]">{{ $farm->distance_route_principale ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Clôture existante</span>
                    <span class="w-[58%]">{{ $farm->cloture_existante ? 'Oui' : 'Non' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Bâtiment / hangar</span>
                    <span class="w-[58%]">{{ $farm->batiment_hangar ? 'Oui' : 'Non' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Électricité</span>
                    <span class="w-[58%]">{{ $farm->electricite_disponible ? 'Oui' : 'Non' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Réseau téléphonique</span>
                    <span class="w-[58%]">{{ $farm->reseau_telephonique ? 'Oui' : 'Non' }}</span>
                </div>
            </div>
        </div>

        {{-- ÉTAPE 8 : OBSERVATIONS --}}
        @if($farm->notes)
        <div class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
                VIII. Observations
            </h2>
            <p class="text-[12.5px] text-slate-700 whitespace-pre-line">{{ $farm->notes }}</p>
        </div>
        @endif

        {{-- ÉTAPE 9 : VÉRIFICATION --}}
        <div class="mb-8">
            <h2 class="text-sm font-bold uppercase tracking-[0.15em] text-slate-900 border-b border-slate-800 pb-1 mb-3">
                IX. Vérification
            </h2>
            <div class="grid grid-cols-2 gap-x-10 gap-y-0.5 text-[12.5px]">
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Vérificateur</span>
                    <span class="w-[58%]">{{ $farm->verificateur_nom ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Poste</span>
                    <span class="w-[58%]">{{ $farm->verificateur_poste ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Date vérification</span>
                    <span class="w-[58%]">{{ $farm->verificateur_date?->format('d/m/Y') ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Recommandation</span>
                    <span class="w-[58%]">
                        @php
                            $recommandationValue = $farm->recommandation;
                            if ($recommandationValue) {
                                try {
                                    $recommandationEnum = \App\Enums\Recommandation::from($recommandationValue);
                                    echo $recommandationEnum->label();
                                } catch (\ValueError $e) {
                                    echo $recommandationValue;
                                }
                            } else {
                                echo '—';
                            }
                        @endphp
                    </span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Nb critères</span>
                    <span class="w-[58%]">{{ $farm->nombre_criteres ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Conformes</span>
                    <span class="w-[58%]">{{ $farm->conformes ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Écarts</span>
                    <span class="w-[58%]">{{ $farm->ecarts ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Total</span>
                    <span class="w-[58%]">{{ $farm->total ?? '—' }}</span>
                </div>
                <div class="flex border-b border-slate-200 py-1.5">
                    <span class="font-semibold text-slate-700 w-[42%]">Écarts significatifs</span>
                    <span class="w-[58%]">{{ $farm->ecarts_significatifs ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- SIGNATURES --}}
        <div class="flex justify-between mt-12 pt-6 border-t-2 border-double border-slate-800">
            <div class="text-center w-[42%]">
                <p class="border-t border-slate-800 pt-1.5 mt-8 text-[11.5px] font-semibold">{{ $farm->user->name ?? '________________________' }}</p>
                <p class="text-[10px] text-slate-500 mt-0.5 uppercase tracking-wide">Client / Propriétaire</p>
            </div>
            <div class="text-center w-[42%]">
                <p class="border-t border-slate-800 pt-1.5 mt-8 text-[11.5px] font-semibold">{{ $farm->verificateur_nom ?? '________________________' }}</p>
                <p class="text-[10px] text-slate-500 mt-0.5 uppercase tracking-wide">Vérificateur</p>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="mt-10 text-center text-[10px] text-slate-400 border-t border-slate-200 pt-4">
            <p>TG AGRO CONSULTING · Montpellier · {{ now()->format('F Y') }} · Document généré automatiquement</p>
            <p class="mt-0.5">Fiche #{{ $farm->reference_dossier ?? $farm->id }} · {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>

    <div class="mt-6 text-center text-xs text-gray-400 no-print">
        Document généré automatiquement par TG Invest.
    </div>
</div>
@endsection

@section('scripts')
<script src="/print/print.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('btn-print-farm');
        if (btn && typeof $.EPrint === 'function') {
            btn.addEventListener('click', function() {
                $.EPrint('farm-print-content', {
                    titre: 'Fiche exploitation - {{ $farm->name }}',
                    popupWidth: 900,
                    popupHeight: 1100,
                    pageMargin: '1cm',
                    keepOpen: true,
                    closeDelay: 800,
                    styles: [],
                    scripts: []
                }).print();
            });
        }
    });
</script>
@endsection






