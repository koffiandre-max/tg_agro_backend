<?php

namespace App\Livewire;

use App\Models\Farm;
use App\Models\User;
use Livewire\Component;

class FarmForm extends Component
{
    public ?int $farmId = null;

    public string $name = '';

    public string $location = '';

    public ?float $latitude = null;

    public ?float $longitude = null;

    public ?float $total_area_hectares = null;

    public string $culture_type = '';

    public string $status = 'active';

    public ?string $expected_harvest_date = null;

    public ?string $crop_stage = null;

    public int $crop_stage_progress = 0;

    public ?string $last_visit_date = null;

    public ?string $notes = null;

    public ?int $user_id = null;

    public bool $isEdit = false;

    public ?string $reference_dossier = null;

    public ?string $date_declaration = null;

    public ?string $nom_client = null;

    public ?string $contact = null;

    public ?string $numero_cadastral = null;

    public ?float $surface_totale = null;

    public ?float $surface_cultivable = null;

    public ?string $forme_parcelle = null;

    public ?string $exposition_principale = null;

    public ?string $pente_moyenne = null;

    public ?int $altitude = null;

    public ?string $topographie = null;

    public ?string $type_sol = null;

    public ?string $couleur_sol = null;

    public ?string $profondeur_sol = null;

    public ?bool $presence_cailloux = null;

    public ?string $commentaire_cailloux = null;

    public ?bool $problemes_erosion = null;

    public ?string $commentaire_erosion = null;

    public ?bool $analyse_sol_realisee = null;

    public ?string $analyse_sol_realisee_precision = null;

    public ?string $commentaire_analyse = null;

    public ?float $ph = null;

    public ?string $source_ph = null;

    public ?bool $point_eau_proximite = null;

    public ?string $commentaire_point_eau = null;

    public ?string $type_point_eau = null;

    public ?int $distance_point_eau = null;

    public ?bool $systeme_irrigation = null;

    public ?string $commentaire_irrigation = null;

    public ?string $type_irrigation = null;

    public ?bool $inondations_saisonnieres = null;

    public ?string $commentaire_inondations = null;

    public ?string $periode_secheresse = null;

    public ?string $occupation_actuelle = null;

    public ?string $cultures_place = null;

    public ?bool $presence_arbres = null;

    public ?string $commentaire_arbres = null;

    public ?string $especes_ligneuses = null;

    public ?string $rendement_actuel = null;

    public ?bool $antecedents_traitement = null;

    public ?string $commentaire_traitement = null;

    public ?bool $produits_herbicides = null;

    public ?bool $produits_pesticides = null;

    public ?bool $produits_engrais = null;

    public ?bool $produits_autre = null;

    public ?string $produits_autre_detail = null;

    public ?bool $acces_carrossable = null;

    public ?string $commentaire_acces = null;

    public ?float $distance_route_principale = null;

    public ?bool $cloture_existante = null;

    public ?string $commentaire_cloture = null;

    public ?bool $batiment_hangar = null;

    public ?string $commentaire_batiment = null;

    public ?bool $electricite_disponible = null;

    public ?string $commentaire_electricite = null;

    public ?bool $reseau_telephonique = null;

    public ?string $commentaire_reseau = null;

    public ?string $observations_libres = null;

    public ?string $signature_date = null;

    public ?string $signature = null;

    public ?int $nombre_criteres = null;

    public ?int $conformes = null;

    public ?int $ecarts = null;

    public ?int $total = null;

    public ?string $ecarts_significatifs = null;

    public ?string $recommandation = null;

    public ?string $verificateur_nom = null;

    public ?string $verificateur_poste = null;

    public ?string $verificateur_date = null;

    public ?string $verificateur_signature = null;

    public function mount(?int $farmId = null)
    {
        $this->farmId = $farmId;

        if ($farmId) {
            $this->isEdit = true;
            $farm = Farm::with('user')->findOrFail($farmId);

            $this->name = $farm->name;
            $this->location = $farm->location;
            $this->latitude = $farm->latitude;
            $this->longitude = $farm->longitude;
            $this->total_area_hectares = $farm->total_area_hectares;
            $this->culture_type = $farm->culture_type;
            $this->status = $farm->status;
            $this->expected_harvest_date = $farm->expected_harvest_date?->format('Y-m-d');
            $this->crop_stage = $farm->crop_stage;
            $this->crop_stage_progress = $farm->crop_stage_progress;
            $this->last_visit_date = $farm->last_visit_date?->format('Y-m-d');
            $this->notes = $farm->notes;
            $this->user_id = $farm->user_id;

            $this->reference_dossier = $farm->reference_dossier;
            $this->date_declaration = $farm->date_declaration?->format('Y-m-d');
            $this->nom_client = $farm->nom_client;
            $this->contact = $farm->contact;
            $this->numero_cadastral = $farm->numero_cadastral;

            $this->surface_totale = $farm->surface_totale;
            $this->surface_cultivable = $farm->surface_cultivable;
            $this->forme_parcelle = $farm->forme_parcelle;
            $this->exposition_principale = $farm->exposition_principale;
            $this->pente_moyenne = $farm->pente_moyenne;
            $this->altitude = $farm->altitude;
            $this->topographie = $farm->topographie;

            $this->type_sol = $farm->type_sol;
            $this->couleur_sol = $farm->couleur_sol;
            $this->profondeur_sol = $farm->profondeur_sol;
            $this->presence_cailloux = $farm->presence_cailloux;
            $this->commentaire_cailloux = $farm->commentaire_cailloux;
            $this->problemes_erosion = $farm->problemes_erosion;
            $this->commentaire_erosion = $farm->commentaire_erosion;
            $this->analyse_sol_realisee = $farm->analyse_sol_realisee;
            $this->commentaire_analyse = $farm->commentaire_analyse;
            $this->ph = $farm->ph;
            $this->source_ph = $farm->source_ph;

            $this->point_eau_proximite = $farm->point_eau_proximite;
            $this->commentaire_point_eau = $farm->commentaire_point_eau;
            $this->type_point_eau = $farm->type_point_eau;
            $this->distance_point_eau = $farm->distance_point_eau;
            $this->systeme_irrigation = $farm->systeme_irrigation;
            $this->commentaire_irrigation = $farm->commentaire_irrigation;
            $this->type_irrigation = $farm->type_irrigation;
            $this->inondations_saisonnieres = $farm->inondations_saisonnieres;
            $this->commentaire_inondations = $farm->commentaire_inondations;
            $this->periode_secheresse = $farm->periode_secheresse;

            $this->occupation_actuelle = $farm->occupation_actuelle;
            $this->cultures_place = $farm->cultures_place;
            $this->presence_arbres = $farm->presence_arbres;
            $this->commentaire_arbres = $farm->commentaire_arbres;
            $this->especes_ligneuses = $farm->especes_ligneuses;
            $this->rendement_actuel = $farm->rendement_actuel;
            $this->antecedents_traitement = $farm->antecedents_traitement;
            $this->commentaire_traitement = $farm->commentaire_traitement;
            $this->produits_herbicides = $farm->produits_herbicides;
            $this->produits_pesticides = $farm->produits_pesticides;
            $this->produits_engrais = $farm->produits_engrais;
            $this->produits_autre = $farm->produits_autre;
            $this->produits_autre_detail = $farm->produits_autre_detail;

            $this->acces_carrossable = $farm->acces_carrossable;
            $this->commentaire_acces = $farm->commentaire_acces;
            $this->distance_route_principale = $farm->distance_route_principale;
            $this->cloture_existante = $farm->cloture_existante;
            $this->commentaire_cloture = $farm->commentaire_cloture;
            $this->batiment_hangar = $farm->batiment_hangar;
            $this->commentaire_batiment = $farm->commentaire_batiment;
            $this->electricite_disponible = $farm->electricite_disponible;
            $this->commentaire_electricite = $farm->commentaire_electricite;
            $this->reseau_telephonique = $farm->reseau_telephonique;
            $this->commentaire_reseau = $farm->commentaire_reseau;

            $this->observations_libres = $farm->observations_libres;
            $this->signature_date = $farm->signature_date?->format('Y-m-d');
            $this->signature = $farm->signature;

            $this->nombre_criteres = $farm->nombre_criteres;
            $this->conformes = $farm->conformes;
            $this->ecarts = $farm->ecarts;
            $this->total = $farm->total;
            $this->ecarts_significatifs = $farm->ecarts_significatifs;
            $this->recommandation = $farm->recommandation;
            $this->verificateur_nom = $farm->verificateur_nom;
            $this->verificateur_poste = $farm->verificateur_poste;
            $this->verificateur_date = $farm->verificateur_date?->format('Y-m-d');
            $this->verificateur_signature = $farm->verificateur_signature;
        }
    }

    public function submit()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'total_area_hectares' => 'required|numeric|min:0',
            'culture_type' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,fallow',
            'expected_harvest_date' => 'nullable|date',
            'crop_stage' => 'nullable|string|max:100',
            'crop_stage_progress' => 'nullable|integer|min:0|max:100',
            'last_visit_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'user_id' => 'required|exists:users,id',

            'reference_dossier' => 'nullable|string|max:50',
            'date_declaration' => 'nullable|date',
            'nom_client' => 'nullable|string|max:100',
            'contact' => 'nullable|string|max:100',
            'numero_cadastral' => 'nullable|string|max:50',
            'surface_totale' => 'nullable|numeric|min:0',
            'surface_cultivable' => 'nullable|numeric|min:0',
            'forme_parcelle' => 'nullable|string|max:255',
            'exposition_principale' => 'nullable|string|max:255',
            'pente_moyenne' => 'nullable|string|max:255',
            'altitude' => 'nullable|integer',
            'topographie' => 'nullable|string|max:255',

            'type_sol' => 'nullable|string|max:255',
            'couleur_sol' => 'nullable|string|max:255',
            'profondeur_sol' => 'nullable|string|max:20',
            'presence_cailloux' => 'nullable|boolean',
            'commentaire_cailloux' => 'nullable|string',
            'problemes_erosion' => 'nullable|boolean',
            'commentaire_erosion' => 'nullable|string',
            'analyse_sol_realisee' => 'nullable|boolean',
            'analyse_sol_realisee_precision' => 'nullable|string|max:255',
            'commentaire_analyse' => 'nullable|string',
            'ph' => 'nullable|numeric|min:0|max:14',
            'source_ph' => 'nullable|string|max:100',

            'point_eau_proximite' => 'nullable|boolean',
            'commentaire_point_eau' => 'nullable|string',
            'type_point_eau' => 'nullable|string|max:255',
            'distance_point_eau' => 'nullable|integer|min:0',
            'systeme_irrigation' => 'nullable|boolean',
            'commentaire_irrigation' => 'nullable|string',
            'type_irrigation' => 'nullable|string|max:255',
            'inondations_saisonnieres' => 'nullable|boolean',
            'commentaire_inondations' => 'nullable|string',
            'periode_secheresse' => 'nullable|string|max:100',

            'occupation_actuelle' => 'nullable|string|max:255',
            'cultures_place' => 'nullable|string|max:200',
            'presence_arbres' => 'nullable|boolean',
            'commentaire_arbres' => 'nullable|string',
            'especes_ligneuses' => 'nullable|string',
            'rendement_actuel' => 'nullable|string|max:50',
            'antecedents_traitement' => 'nullable|boolean',
            'commentaire_traitement' => 'nullable|string',
            'produits_herbicides' => 'nullable|boolean',
            'produits_pesticides' => 'nullable|boolean',
            'produits_engrais' => 'nullable|boolean',
            'produits_autre' => 'nullable|boolean',
            'produits_autre_detail' => 'nullable|string|max:100',

            'acces_carrossable' => 'nullable|boolean',
            'commentaire_acces' => 'nullable|string',
            'distance_route_principale' => 'nullable|numeric|min:0',
            'cloture_existante' => 'nullable|boolean',
            'commentaire_cloture' => 'nullable|string',
            'batiment_hangar' => 'nullable|boolean',
            'commentaire_batiment' => 'nullable|string',
            'electricite_disponible' => 'nullable|boolean',
            'commentaire_electricite' => 'nullable|string',
            'reseau_telephonique' => 'nullable|boolean',
            'commentaire_reseau' => 'nullable|string',

            'observations_libres' => 'nullable|string',
            'signature_date' => 'nullable|date',
            'signature' => 'nullable|string',

            'nombre_criteres' => 'nullable|integer|min:0',
            'conformes' => 'nullable|integer|min:0',
            'ecarts' => 'nullable|integer|min:0',
            'total' => 'nullable|integer|min:0',
            'ecarts_significatifs' => 'nullable|string',
            'recommandation' => 'nullable|string|max:255',
            'verificateur_nom' => 'nullable|string|max:100',
            'verificateur_poste' => 'nullable|string|max:100',
            'verificateur_date' => 'nullable|date',
            'verificateur_signature' => 'nullable|string',
        ];

        $this->validate($rules, [
            'name.required' => 'Le nom de l\'exploitation est obligatoire.',
            'location.required' => 'La localisation est obligatoire.',
            'total_area_hectares.required' => 'La surface est obligatoire.',
            'culture_type.required' => 'Le type de culture est obligatoire.',
            'status.required' => 'Le statut est obligatoire.',
            'user_id.required' => 'Le propriétaire est obligatoire.',
        ]);

        $data = [
            'name' => $this->name,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'total_area_hectares' => $this->total_area_hectares,
            'culture_type' => $this->culture_type,
            'status' => $this->status,
            'expected_harvest_date' => $this->expected_harvest_date ?: null,
            'crop_stage' => $this->crop_stage,
            'crop_stage_progress' => $this->crop_stage_progress,
            'last_visit_date' => $this->last_visit_date ?: null,
            'notes' => $this->notes,
            'user_id' => $this->user_id,

            'reference_dossier' => $this->reference_dossier,
            'date_declaration' => $this->date_declaration ?: null,
            'nom_client' => $this->nom_client,
            'contact' => $this->contact,
            'numero_cadastral' => $this->numero_cadastral,
            'surface_totale' => $this->surface_totale,
            'surface_cultivable' => $this->surface_cultivable,
            'forme_parcelle' => $this->forme_parcelle,
            'exposition_principale' => $this->exposition_principale,
            'pente_moyenne' => $this->pente_moyenne,
            'altitude' => $this->altitude,
            'topographie' => $this->topographie,

            'type_sol' => $this->type_sol,
            'couleur_sol' => $this->couleur_sol,
            'profondeur_sol' => $this->profondeur_sol,
            'presence_cailloux' => $this->presence_cailloux,
            'commentaire_cailloux' => $this->commentaire_cailloux,
            'problemes_erosion' => $this->problemes_erosion,
            'commentaire_erosion' => $this->commentaire_erosion,
            'analyse_sol_realisee' => $this->analyse_sol_realisee,
            'commentaire_analyse' => $this->commentaire_analyse,
            'ph' => $this->ph,
            'source_ph' => $this->source_ph,

            'point_eau_proximite' => $this->point_eau_proximite,
            'commentaire_point_eau' => $this->commentaire_point_eau,
            'type_point_eau' => $this->type_point_eau,
            'distance_point_eau' => $this->distance_point_eau,
            'systeme_irrigation' => $this->systeme_irrigation,
            'commentaire_irrigation' => $this->commentaire_irrigation,
            'type_irrigation' => $this->type_irrigation,
            'inondations_saisonnieres' => $this->inondations_saisonnieres,
            'commentaire_inondations' => $this->commentaire_inondations,
            'periode_secheresse' => $this->periode_secheresse,

            'occupation_actuelle' => $this->occupation_actuelle,
            'cultures_place' => $this->cultures_place,
            'presence_arbres' => $this->presence_arbres,
            'commentaire_arbres' => $this->commentaire_arbres,
            'especes_ligneuses' => $this->especes_ligneuses,
            'rendement_actuel' => $this->rendement_actuel,
            'antecedents_traitement' => $this->antecedents_traitement,
            'commentaire_traitement' => $this->commentaire_traitement,
            'produits_herbicides' => $this->produits_herbicides,
            'produits_pesticides' => $this->produits_pesticides,
            'produits_engrais' => $this->produits_engrais,
            'produits_autre' => $this->produits_autre,
            'produits_autre_detail' => $this->produits_autre_detail,

            'acces_carrossable' => $this->acces_carrossable,
            'commentaire_acces' => $this->commentaire_acces,
            'distance_route_principale' => $this->distance_route_principale,
            'cloture_existante' => $this->cloture_existante,
            'commentaire_cloture' => $this->commentaire_cloture,
            'batiment_hangar' => $this->batiment_hangar,
            'commentaire_batiment' => $this->commentaire_batiment,
            'electricite_disponible' => $this->electricite_disponible,
            'commentaire_electricite' => $this->commentaire_electricite,
            'reseau_telephonique' => $this->reseau_telephonique,
            'commentaire_reseau' => $this->commentaire_reseau,

            'observations_libres' => $this->observations_libres,
            'signature_date' => $this->signature_date ?: null,
            'signature' => $this->signature,

            'nombre_criteres' => $this->nombre_criteres,
            'conformes' => $this->conformes,
            'ecarts' => $this->ecarts,
            'total' => $this->total,
            'ecarts_significatifs' => $this->ecarts_significatifs,
            'recommandation' => $this->recommandation,
            'verificateur_nom' => $this->verificateur_nom,
            'verificateur_poste' => $this->verificateur_poste,
            'verificateur_date' => $this->verificateur_date ?: null,
            'verificateur_signature' => $this->verificateur_signature,
        ];

        if ($this->isEdit) {
            $farm = Farm::findOrFail($this->farmId);
            $farm->update($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Exploitation mise à jour avec succès.']);
        } else {
            Farm::create($data);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Exploitation créée avec succès.']);
        }

        return $this->redirect(route('admin.farms.index'));
    }

    public function render()
    {
        $clients = User::where('role', 'client')->get(['id', 'name']);

        return view('livewire.farm-form', [
            'clients' => $clients,
        ]);
    }
}
