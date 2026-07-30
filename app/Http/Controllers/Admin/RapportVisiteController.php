<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ConditionMeteo;
use App\Enums\DureeVisite;
use App\Enums\RapportVisiteType;
use App\Enums\StatutRapport;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Farm;
use App\Models\RapportVisite;
use App\Models\User;
use Illuminate\Http\Request;

class RapportVisiteController extends Controller
{
    public function index()
    {
        return view('admin.rapports-visite.datatable');
    }

    public function create()
    {
        $techniciens = User::where('role', 'technician')->orWhere('role', 'admin')->get(['id', 'name']);
        $clients = Client::with('user')->get();
        $farms = Farm::all(['id', 'name']);

        $typeVisiteOptions = collect(RapportVisiteType::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $conditionMeteoOptions = collect(ConditionMeteo::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $dureeVisiteOptions = collect(DureeVisite::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $statutOptions = collect(StatutRapport::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);

        return view('admin.rapports-visite.create', [
            'techniciens' => $techniciens,
            'clients' => $clients,
            'farms' => $farms,
            'typeVisiteOptions' => $typeVisiteOptions,
            'conditionMeteoOptions' => $conditionMeteoOptions,
            'dureeVisiteOptions' => $dureeVisiteOptions,
            'statutOptions' => $statutOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'technicien_id' => ['required', 'exists:users,id'],
            'date_visite' => ['required', 'date'],
            'client_id' => ['required', 'exists:clients,id'],
            'localisation_parcelle' => ['required', 'string', 'max:200'],
            'type_visite' => ['required', 'string'],
            'conditions_meteo' => ['nullable', 'string'],
            'superficie_visitee_ha' => ['nullable', 'numeric', 'min:0'],
            'duree_visite' => ['nullable', 'string'],
            'gps_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'gps_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'cultures_presentes' => ['nullable', 'array'],
            'culture_autre_precision' => ['nullable', 'string', 'max:100'],
            'stade_phenologique' => ['nullable', 'string'],
            'avancement_cycle_pourcent' => ['nullable', 'integer', 'between:0,100'],
            'etat_couvert_vegetal' => ['nullable', 'integer', 'between:1,5'],
            'ravageurs_maladies' => ['nullable', 'array'],
            'ravageur_autre_precision' => ['nullable', 'string', 'max:100'],
            'niveau_infestation' => ['nullable', 'integer', 'between:1,5'],
            'observations_ravageurs' => ['nullable', 'text'],
            'etat_hydrique_sol' => ['nullable', 'string'],
            'irrigation_en_place' => ['nullable', 'boolean'],
            'etat_structure_sol' => ['nullable', 'string'],
            'ph_sol' => ['nullable', 'numeric', 'between:0,14'],
            'entretien_intrants' => ['nullable', 'array'],
            'intrants_utilises' => ['nullable', 'text'],
            'estimation_recolte_kg' => ['nullable', 'numeric', 'min:0'],
            'date_estimee_recolte' => ['nullable', 'date'],
            'animaux_presents' => ['nullable', 'array'],
            'animal_autre_precision' => ['nullable', 'string', 'max:100'],
            'effectif_total' => ['nullable', 'integer', 'min:0'],
            'mortalite_constatee' => ['nullable', 'integer', 'min:0'],
            'naissances_depuis_derniere_visite' => ['nullable', 'integer', 'min:0'],
            'ventes_abattages_depuis_derniere_visite' => ['nullable', 'integer', 'min:0'],
            'etat_corporel_general' => ['nullable', 'integer', 'between:1,5'],
            'signes_cliniques' => ['nullable', 'array'],
            'signe_autre_precision' => ['nullable', 'string', 'max:100'],
            'observations_sanitaires' => ['nullable', 'text'],
            'soins_traitements' => ['nullable', 'array'],
            'produits_administres' => ['nullable', 'text'],
            'etat_alimentation' => ['nullable', 'string'],
            'eau_abreuvement' => ['nullable', 'string'],
            'etat_batiments_enclos' => ['nullable', 'integer', 'between:1,5'],
            'production_laitiere_l_j' => ['nullable', 'numeric', 'min:0'],
            'production_oeufs_nb_j' => ['nullable', 'integer', 'min:0'],
            'gain_poids_kg_mois' => ['nullable', 'numeric', 'min:0'],
            'resume_visite' => ['required', 'text'],
            'niveau_alerte' => ['nullable', 'string', 'in:aucune,faible,moderee,urgente'],
            'description_alerte' => ['nullable', 'text'],
            'prochaine_visite_date' => ['nullable', 'date'],
            'prochaine_visite_raison' => ['nullable', 'string'],
            'note_interne' => ['nullable', 'text'],
            'message_client' => ['nullable', 'text'],
            'statut' => ['nullable', 'string', 'in:brouillon,en_attente_validation,valide,rejete'],
        ]);

        $validated['statut'] = $validated['statut'] ?? 'brouillon';

        RapportVisite::create($validated);

        return redirect()->route('admin.rapports-visite.index')->with('success', 'Rapport de visite créé avec succès.');
    }

    public function show(RapportVisite $rapports_visite)
    {
        $rapports_visite->load([
            'technicien',
            'client.user',
            'farm',
            'visiteCultures.typesCultures',
            'visiteCultures.etatsVegetatifs',
            'visiteCultures.ravageursMaladies',
            'visiteCultures.observationsRavageurs',
            'visiteCultures.solsIrrigations',
            'visiteCultures.entretienIntrants',
            'visiteElevage.typesAnimaux',
            'visiteElevage.etatsElevage',
            'visiteElevage.signesCliniques',
            'visiteElevage.observationsSanitaires',
            'visiteElevage.soinsAnimaux',
            'visiteElevage.alimentationEau',
            'visiteElevage.performancesElevage',
            'observationsFinales',
            'prochaineVisite',
            'notesRapport',
            'photos',
            'validations.admin',
        ]);

        return view('admin.rapports-visite.show', ['rapport' => $rapports_visite]);
    }

    public function edit($id)
    {
        $rapport = RapportVisite::findOrFail($id);

        $techniciens = User::where('role', 'technician')->orWhere('role', 'admin')->get(['id', 'name']);
        $clients = Client::with('user')->get();
        $farms = Farm::all(['id', 'name']);

        return view('admin.rapports-visite.edit', [
            'rapport' => $rapport,
            'techniciens' => $techniciens,
            'clients' => $clients,
            'farms' => $farms,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'technicien_id' => ['required', 'exists:users,id'],
            'date_visite' => ['required', 'date'],
            'client_id' => ['required', 'exists:clients,id'],
            'localisation_parcelle' => ['required', 'string', 'max:200'],
            'type_visite' => ['required', 'string'],
            'conditions_meteo' => ['nullable', 'string'],
            'superficie_visitee_ha' => ['nullable', 'numeric', 'min:0'],
            'duree_visite' => ['nullable', 'string'],
            'gps_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'gps_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'cultures_presentes' => ['nullable', 'array'],
            'culture_autre_precision' => ['nullable', 'string', 'max:100'],
            'stade_phenologique' => ['nullable', 'string'],
            'avancement_cycle_pourcent' => ['nullable', 'integer', 'between:0,100'],
            'etat_couvert_vegetal' => ['nullable', 'integer', 'between:1,5'],
            'ravageurs_maladies' => ['nullable', 'array'],
            'ravageur_autre_precision' => ['nullable', 'string', 'max:100'],
            'niveau_infestation' => ['nullable', 'integer', 'between:1,5'],
            'observations_ravageurs' => ['nullable', 'text'],
            'etat_hydrique_sol' => ['nullable', 'string'],
            'irrigation_en_place' => ['nullable', 'boolean'],
            'etat_structure_sol' => ['nullable', 'string'],
            'ph_sol' => ['nullable', 'numeric', 'between:0,14'],
            'entretien_intrants' => ['nullable', 'array'],
            'intrants_utilises' => ['nullable', 'text'],
            'estimation_recolte_kg' => ['nullable', 'numeric', 'min:0'],
            'date_estimee_recolte' => ['nullable', 'date'],
            'animaux_presents' => ['nullable', 'array'],
            'animal_autre_precision' => ['nullable', 'string', 'max:100'],
            'effectif_total' => ['nullable', 'integer', 'min:0'],
            'mortalite_constatee' => ['nullable', 'integer', 'min:0'],
            'naissances_depuis_derniere_visite' => ['nullable', 'integer', 'min:0'],
            'ventes_abattages_depuis_derniere_visite' => ['nullable', 'integer', 'min:0'],
            'etat_corporel_general' => ['nullable', 'integer', 'between:1,5'],
            'signes_cliniques' => ['nullable', 'array'],
            'signe_autre_precision' => ['nullable', 'string', 'max:100'],
            'observations_sanitaires' => ['nullable', 'text'],
            'soins_traitements' => ['nullable', 'array'],
            'produits_administres' => ['nullable', 'text'],
            'etat_alimentation' => ['nullable', 'string'],
            'eau_abreuvement' => ['nullable', 'string'],
            'etat_batiments_enclos' => ['nullable', 'integer', 'between:1,5'],
            'production_laitiere_l_j' => ['nullable', 'numeric', 'min:0'],
            'production_oeufs_nb_j' => ['nullable', 'integer', 'min:0'],
            'gain_poids_kg_mois' => ['nullable', 'numeric', 'min:0'],
            'resume_visite' => ['required', 'text'],
            'niveau_alerte' => ['nullable', 'string', 'in:aucune,faible,moderee,urgente'],
            'description_alerte' => ['nullable', 'text'],
            'prochaine_visite_date' => ['nullable', 'date'],
            'prochaine_visite_raison' => ['nullable', 'string'],
            'note_interne' => ['nullable', 'text'],
            'message_client' => ['nullable', 'text'],
            'statut' => ['nullable', 'string', 'in:brouillon,en_attente_validation,valide,rejete'],
        ]);

        $rapport = RapportVisite::findOrFail($id);
        $rapport->update($validated);

        return redirect()->route('admin.rapports-visite.show', $rapport)->with('success', 'Rapport de visite mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $rapport = RapportVisite::findOrFail($id);
        $rapport->delete();

        return redirect()->route('admin.rapports-visite.index')->with('success', 'Rapport de visite supprimé avec succès.');
    }
}
