<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ConditionMeteo;
use App\Enums\DureeVisite;
use App\Enums\RapportVisiteType;
use App\Enums\StatutRapport;
use App\Enums\TypeActivite;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Farm;
use App\Models\RapportVisite;
use App\Models\User;
use App\Models\VisiteAutre;
use App\Models\VisiteCulture;
use App\Models\VisiteElevage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RapportVisiteController extends Controller
{
    public function index()
    {
        return view('admin.rapports-visite.datatable');
    }

    public function create()
    {
        $techniciens = User::where('role', 'technician')->orWhere('role', 'admin')->get(['id', 'name', 'role', 'type_technicien']);
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
        $typeActiviteOptions = collect(TypeActivite::cases())->map(fn($case) => [
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
            'typeActiviteOptions' => $typeActiviteOptions,
        ]);
    }

    public function store(Request $request)
    {
        // Validation des champs communs (rapport_visites)
        $validated = $request->validate([
            'technicien_id' => ['required', 'exists:users,id'],
            'date_visite' => ['required', 'date'],
            'client_id' => ['required', 'exists:clients,id'],
            'farm_id' => ['nullable', 'exists:farms,id'],
            'localisation_parcelle' => ['required', 'string', 'max:200'],
            'type_visite' => ['required', 'string'],
            'type_activite' => ['required', 'string', 'in:culture,elevage,autre'],
            'conditions_meteo' => ['nullable', 'string'],
            'superficie_visitee_ha' => ['nullable', 'numeric', 'min:0'],
            'duree_visite' => ['nullable', 'string'],
            'gps_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'gps_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'resume_visite' => ['required', 'string'],
            'niveau_alerte' => ['nullable', 'string', 'in:aucune,faible,moderee,urgente'],
            'description_alerte' => ['nullable', 'string'],
            'prochaine_visite_date' => ['nullable', 'date'],
            'prochaine_visite_raison' => ['nullable', 'string'],
            'note_interne' => ['nullable', 'string'],
            'message_client' => ['nullable', 'string'],
            'statut' => ['nullable', 'string', 'in:brouillon,en_attente_validation,valide,rejete'],
        ]);

        $validated['statut'] = $validated['statut'] ?? 'brouillon';

        // Créer le rapport principal
        $rapport = RapportVisite::create($validated);

        // Créer l'enregistrement dans la table annexe selon le type d'activité
        $typeActivite = $validated['type_activite'];

        if ($typeActivite === 'culture') {
            $cultureData = $request->validate([
                'cultures_presentes' => ['nullable', 'array'],
                'culture_autre_precision' => ['nullable', 'string', 'max:100'],
                'stade_phenologique' => ['nullable', 'string'],
                'avancement_cycle_pourcent' => ['nullable', 'integer', 'between:0,100'],
                'etat_couvert_vegetal' => ['nullable', 'integer', 'between:1,5'],
                'ravageurs_maladies' => ['nullable', 'array'],
                'ravageur_autre_precision' => ['nullable', 'string', 'max:100'],
                'niveau_infestation' => ['nullable', 'integer', 'between:1,5'],
                'observations_ravageurs' => ['nullable', 'string'],
                'etat_hydrique_sol' => ['nullable', 'string'],
                'irrigation_en_place' => ['nullable', 'boolean'],
                'etat_structure_sol' => ['nullable', 'string'],
                'ph_sol' => ['nullable', 'numeric', 'between:0,14'],
                'entretien_intrants' => ['nullable', 'array'],
                'intrants_utilises' => ['nullable', 'string'],
                'estimation_recolte_kg' => ['nullable', 'numeric', 'min:0'],
                'date_estimee_recolte' => ['nullable', 'date'],
            ]);
            $cultureData['rapport_id'] = $rapport->id;
            VisiteCulture::create($cultureData);
        } elseif ($typeActivite === 'elevage') {
            $elevageData = $request->validate([
                'animaux_presents' => ['nullable', 'array'],
                'animal_autre_precision' => ['nullable', 'string', 'max:100'],
                'effectif_total' => ['nullable', 'integer', 'min:0'],
                'mortalite_constatee' => ['nullable', 'integer', 'min:0'],
                'naissances_depuis_derniere_visite' => ['nullable', 'integer', 'min:0'],
                'ventes_abattages_depuis_derniere_visite' => ['nullable', 'integer', 'min:0'],
                'etat_corporel_general' => ['nullable', 'integer', 'between:1,5'],
                'signes_cliniques' => ['nullable', 'array'],
                'signe_autre_precision' => ['nullable', 'string', 'max:100'],
                'observations_sanitaires' => ['nullable', 'string'],
                'soins_traitements' => ['nullable', 'array'],
                'produits_administres' => ['nullable', 'string'],
                'etat_alimentation' => ['nullable', 'string'],
                'eau_abreuvement' => ['nullable', 'string'],
                'etat_batiments_enclos' => ['nullable', 'integer', 'between:1,5'],
                'production_laitiere_l_j' => ['nullable', 'numeric', 'min:0'],
                'production_oeufs_nb_j' => ['nullable', 'integer', 'min:0'],
                'gain_poids_kg_mois' => ['nullable', 'numeric', 'min:0'],
            ]);
            $elevageData['rapport_id'] = $rapport->id;
            VisiteElevage::create($elevageData);
        } else {
            // type_activite === 'autre'
            $autreData = $request->validate([
                'type_autre' => ['nullable', 'string', 'max:200'],
                'description_activite' => ['nullable', 'string'],
                'observations_specifiques' => ['nullable', 'string'],
            ]);
            $autreData['rapport_id'] = $rapport->id;
            VisiteAutre::create($autreData);
        }

        return redirect()->route('admin.rapports-visite.index')->with('success', 'Rapport de visite créé avec succès.');
    }

    public function show(RapportVisite $rapports_visite)
    {
        $rapports_visite->load([
            'technicien',
            'client.user',
            'farm',
            'visiteCultures',
            'visiteElevage',
            'visiteAutres',
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
        $rapport = RapportVisite::with(['visiteCultures', 'visiteElevage', 'visiteAutres'])->findOrFail($id);

        $techniciens = User::where('role', 'technician')->orWhere('role', 'admin')->get(['id', 'name', 'role', 'type_technicien']);
        $clients = Client::with('user')->get();
        $farms = Farm::all(['id', 'name']);

        $typeActiviteOptions = collect(TypeActivite::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);

        return view('admin.rapports-visite.edit', [
            'rapport' => $rapport,
            'techniciens' => $techniciens,
            'clients' => $clients,
            'farms' => $farms,
            'typeActiviteOptions' => $typeActiviteOptions,
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validation des champs communs (rapport_visites)
        $validated = $request->validate([
            'technicien_id' => ['required', 'exists:users,id'],
            'date_visite' => ['required', 'date'],
            'client_id' => ['required', 'exists:clients,id'],
            'farm_id' => ['nullable', 'exists:farms,id'],
            'localisation_parcelle' => ['required', 'string', 'max:200'],
            'type_visite' => ['required', 'string'],
            'type_activite' => ['required', 'string', 'in:culture,elevage,autre'],
            'conditions_meteo' => ['nullable', 'string'],
            'superficie_visitee_ha' => ['nullable', 'numeric', 'min:0'],
            'duree_visite' => ['nullable', 'string'],
            'gps_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'gps_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'resume_visite' => ['required', 'string'],
            'niveau_alerte' => ['nullable', 'string', 'in:aucune,faible,moderee,urgente'],
            'description_alerte' => ['nullable', 'string'],
            'prochaine_visite_date' => ['nullable', 'date'],
            'prochaine_visite_raison' => ['nullable', 'string'],
            'note_interne' => ['nullable', 'string'],
            'message_client' => ['nullable', 'string'],
            'statut' => ['nullable', 'string', 'in:brouillon,en_attente_validation,valide,rejete'],
        ]);

        $rapport = RapportVisite::findOrFail($id);
        $rapport->update($validated);

        // Gérer la table annexe selon le type d'activité
        $typeActivite = $validated['type_activite'];

        // Supprimer les enregistrements des autres types d'activité
        if ($typeActivite !== 'culture') {
            $rapport->visiteCultures()->delete();
        }
        if ($typeActivite !== 'elevage') {
            $rapport->visiteElevage()->delete();
        }
        if ($typeActivite !== 'autre') {
            $rapport->visiteAutres()->delete();
        }

        if ($typeActivite === 'culture') {
            $cultureData = $request->validate([
                'cultures_presentes' => ['nullable', 'array'],
                'culture_autre_precision' => ['nullable', 'string', 'max:100'],
                'stade_phenologique' => ['nullable', 'string'],
                'avancement_cycle_pourcent' => ['nullable', 'integer', 'between:0,100'],
                'etat_couvert_vegetal' => ['nullable', 'integer', 'between:1,5'],
                'ravageurs_maladies' => ['nullable', 'array'],
                'ravageur_autre_precision' => ['nullable', 'string', 'max:100'],
                'niveau_infestation' => ['nullable', 'integer', 'between:1,5'],
                'observations_ravageurs' => ['nullable', 'string'],
                'etat_hydrique_sol' => ['nullable', 'string'],
                'irrigation_en_place' => ['nullable', 'boolean'],
                'etat_structure_sol' => ['nullable', 'string'],
                'ph_sol' => ['nullable', 'numeric', 'between:0,14'],
                'entretien_intrants' => ['nullable', 'array'],
                'intrants_utilises' => ['nullable', 'string'],
                'estimation_recolte_kg' => ['nullable', 'numeric', 'min:0'],
                'date_estimee_recolte' => ['nullable', 'date'],
            ]);

            $visiteCulture = $rapport->visiteCultures()->first();
            if ($visiteCulture) {
                $visiteCulture->update($cultureData);
            } else {
                $cultureData['rapport_id'] = $rapport->id;
                VisiteCulture::create($cultureData);
            }
        } elseif ($typeActivite === 'elevage') {
            $elevageData = $request->validate([
                'animaux_presents' => ['nullable', 'array'],
                'animal_autre_precision' => ['nullable', 'string', 'max:100'],
                'effectif_total' => ['nullable', 'integer', 'min:0'],
                'mortalite_constatee' => ['nullable', 'integer', 'min:0'],
                'naissances_depuis_derniere_visite' => ['nullable', 'integer', 'min:0'],
                'ventes_abattages_depuis_derniere_visite' => ['nullable', 'integer', 'min:0'],
                'etat_corporel_general' => ['nullable', 'integer', 'between:1,5'],
                'signes_cliniques' => ['nullable', 'array'],
                'signe_autre_precision' => ['nullable', 'string', 'max:100'],
                'observations_sanitaires' => ['nullable', 'string'],
                'soins_traitements' => ['nullable', 'array'],
                'produits_administres' => ['nullable', 'string'],
                'etat_alimentation' => ['nullable', 'string'],
                'eau_abreuvement' => ['nullable', 'string'],
                'etat_batiments_enclos' => ['nullable', 'integer', 'between:1,5'],
                'production_laitiere_l_j' => ['nullable', 'numeric', 'min:0'],
                'production_oeufs_nb_j' => ['nullable', 'integer', 'min:0'],
                'gain_poids_kg_mois' => ['nullable', 'numeric', 'min:0'],
            ]);

            $visiteElevage = $rapport->visiteElevage()->first();
            if ($visiteElevage) {
                $visiteElevage->update($elevageData);
            } else {
                $elevageData['rapport_id'] = $rapport->id;
                VisiteElevage::create($elevageData);
            }
        } else {
            // type_activite === 'autre'
            $autreData = $request->validate([
                'type_autre' => ['nullable', 'string', 'max:200'],
                'description_activite' => ['nullable', 'string'],
                'observations_specifiques' => ['nullable', 'string'],
            ]);

            $visiteAutre = $rapport->visiteAutres()->first();
            if ($visiteAutre) {
                $visiteAutre->update($autreData);
            } else {
                $autreData['rapport_id'] = $rapport->id;
                VisiteAutre::create($autreData);
            }
        }

        return redirect()->route('admin.rapports-visite.show', $rapport)->with('success', 'Rapport de visite mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $rapport = RapportVisite::findOrFail($id);
        $rapport->delete();

        return redirect()->route('admin.rapports-visite.index')->with('success', 'Rapport de visite supprimé avec succès.');
    }

    public function validate($id)
    {
        $rapport = RapportVisite::findOrFail($id);

        if ($rapport->statut !== StatutRapport::EN_ATTENTE_VALIDATION) {
            return redirect()->back()->with('error', 'Ce rapport ne peut pas être validé dans son état actuel.');
        }

        $rapport->update([
            'statut' => StatutRapport::VALIDE,
            'valide_par' => Auth::id(),
            'valide_at' => now(),
        ]);

        return redirect()->route('admin.rapports-visite.show', $rapport)->with('success', 'Rapport validé avec succès.');
    }

    public function reject(Request $request, $id)
    {
        $rapport = RapportVisite::findOrFail($id);

        if ($rapport->statut !== StatutRapport::EN_ATTENTE_VALIDATION) {
            return redirect()->back()->with('error', 'Ce rapport ne peut pas être rejeté dans son état actuel.');
        }

        $rapport->update([
            'statut' => StatutRapport::REJETE,
            'valide_par' => Auth::id(),
            'valide_at' => now(),
            'motif_rejet' => $request->input('motif_rejet', 'Non précisé'),
        ]);

        return redirect()->route('admin.rapports-visite.show', $rapport)->with('success', 'Rapport rejeté.');
    }

    public function print($id)
    {
        $rapport = RapportVisite::with([
            'technicien',
            'client.user',
            'farm',
            'visiteCultures',
            'visiteElevage',
            'visiteAutres',
            'observationsFinales',
            'prochaineVisite',
            'notesRapport',
            'photos',
        ])->findOrFail($id);

        return view('admin.rapports-visite.pdf', compact('rapport'));
    }
}
