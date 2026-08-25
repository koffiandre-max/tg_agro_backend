<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CouleurSol;
use App\Enums\CultureType;
use App\Enums\ExpositionParcelle;
use App\Enums\FarmStatus;
use App\Enums\FarmType;
use App\Enums\FormeParcelle;
use App\Enums\OccupationActuelle;
use App\Enums\PenteMoyenne;
use App\Enums\Recommandation;
use App\Enums\StadePhenologique;
use App\Enums\Topographie;
use App\Enums\TypeIrrigation;
use App\Enums\TypePointEau;
use App\Enums\TypeSol;
use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\User;
use App\Services\SendmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmController extends Controller
{
    public function __construct(private SendmailService $mailer) {}

    /**
     * Génère une référence de dossier unique au format DOS-YYYY-NNNN.
     */
    private function generateReferenceDossier(): string
    {
        do {
            $reference = 'DOS-' . date('Y') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Farm::where('reference_dossier', $reference)->exists());

        return $reference;
    }

    public function index()
    {
        return view('admin.farms.datatable');
    }

    public function create()
    {
        $clients = User::where('role', 'client')->get(['id', 'name']);

        $typeSolOptions = collect(TypeSol::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $typePointEauOptions = collect(TypePointEau::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $typeIrrigationOptions = collect(TypeIrrigation::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $topographieOptions = collect(Topographie::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $penteMoyenneOptions = collect(PenteMoyenne::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $formeParcelleOptions = collect(FormeParcelle::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $expositionParcelleOptions = collect(ExpositionParcelle::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $occupationActuelleOptions = collect(OccupationActuelle::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $couleurSolOptions = collect(CouleurSol::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $recommandationOptions = collect(Recommandation::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $farmStatusOptions = collect(FarmStatus::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $cultureTypeOptions = collect(CultureType::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $farmTypeOptions = collect(FarmType::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $stadePhenologiqueOptions = collect(StadePhenologique::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);

        return view('admin.farms.create', [
            'clients' => $clients,
            'typeSolOptions' => $typeSolOptions,
            'typePointEauOptions' => $typePointEauOptions,
            'typeIrrigationOptions' => $typeIrrigationOptions,
            'topographieOptions' => $topographieOptions,
            'penteMoyenneOptions' => $penteMoyenneOptions,
            'formeParcelleOptions' => $formeParcelleOptions,
            'expositionParcelleOptions' => $expositionParcelleOptions,
            'occupationActuelleOptions' => $occupationActuelleOptions,
            'couleurSolOptions' => $couleurSolOptions,
            'recommandationOptions' => $recommandationOptions,
            'farmStatusOptions' => $farmStatusOptions,
            'cultureTypeOptions' => $cultureTypeOptions,
            'farmTypeOptions' => $farmTypeOptions,
            'stadePhenologiqueOptions' => $stadePhenologiqueOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id'],
            'location' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:culture,elevage'],
            'culture_type' => ['required', 'string', 'max:255'],
            'total_area_hectares' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive,fallow'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'crop_stage' => ['nullable', 'string', 'max:100'],
            'crop_stage_progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'expected_harvest_date' => ['nullable', 'date'],
            'last_visit_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'reference_dossier' => ['nullable', 'string', 'max:50'],
            'date_declaration' => ['nullable', 'date'],
            'nom_client' => ['nullable', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:100'],
            'numero_cadastral' => ['nullable', 'string', 'max:50'],
            'surface_totale' => ['nullable', 'numeric', 'min:0'],
            'surface_cultivable' => ['nullable', 'numeric', 'min:0'],
            'forme_parcelle' => ['nullable', 'string', 'max:255'],
            'exposition_principale' => ['nullable', 'string', 'max:255'],
            'pente_moyenne' => ['nullable', 'string', 'max:255'],
            'altitude' => ['nullable', 'integer'],
            'topographie' => ['nullable', 'string', 'max:255'],
            'type_sol' => ['nullable', 'string', 'max:255'],
            'couleur_sol' => ['nullable', 'string', 'max:255'],
            'profondeur_sol' => ['nullable', 'string', 'max:20'],
            'presence_cailloux' => ['nullable', 'boolean'],
            'commentaire_cailloux' => ['nullable', 'string'],
            'problemes_erosion' => ['nullable', 'boolean'],
            'commentaire_erosion' => ['nullable', 'string'],
            'analyse_sol_realisee' => ['nullable', 'boolean'],
            'commentaire_analyse' => ['nullable', 'string'],
            'ph' => ['nullable', 'numeric', 'min:0', 'max:14'],
            'source_ph' => ['nullable', 'string', 'max:100'],
            'point_eau_proximite' => ['nullable', 'boolean'],
            'commentaire_point_eau' => ['nullable', 'string'],
            'type_point_eau' => ['nullable', 'string', 'max:255'],
            'distance_point_eau' => ['nullable', 'integer', 'min:0'],
            'systeme_irrigation' => ['nullable', 'boolean'],
            'commentaire_irrigation' => ['nullable', 'string'],
            'type_irrigation' => ['nullable', 'string', 'max:255'],
            'inondations_saisonnieres' => ['nullable', 'boolean'],
            'commentaire_inondations' => ['nullable', 'string'],
            'periode_secheresse' => ['nullable', 'string', 'max:100'],
            'occupation_actuelle' => ['nullable', 'string', 'max:255'],
            'cultures_place' => ['nullable', 'string', 'max:200'],
            'presence_arbres' => ['nullable', 'boolean'],
            'commentaire_arbres' => ['nullable', 'string'],
            'especes_ligneuses' => ['nullable', 'string'],
            'rendement_actuel' => ['nullable', 'string', 'max:50'],
            'antecedents_traitement' => ['nullable', 'boolean'],
            'commentaire_traitement' => ['nullable', 'string'],
            'produits_herbicides' => ['nullable', 'boolean'],
            'produits_pesticides' => ['nullable', 'boolean'],
            'produits_engrais' => ['nullable', 'boolean'],
            'produits_autre' => ['nullable', 'boolean'],
            'produits_autre_detail' => ['nullable', 'string', 'max:100'],
            'acces_carrossable' => ['nullable', 'boolean'],
            'commentaire_acces' => ['nullable', 'string'],
            'distance_route_principale' => ['nullable', 'numeric', 'min:0'],
            'cloture_existante' => ['nullable', 'boolean'],
            'commentaire_cloture' => ['nullable', 'string'],
            'batiment_hangar' => ['nullable', 'boolean'],
            'commentaire_batiment' => ['nullable', 'string'],
            'electricite_disponible' => ['nullable', 'boolean'],
            'commentaire_electricite' => ['nullable', 'string'],
            'reseau_telephonique' => ['nullable', 'boolean'],
            'commentaire_reseau' => ['nullable', 'string'],
            'observations_libres' => ['nullable', 'string'],
            'signature_date' => ['nullable', 'date'],
            'signature' => ['nullable', 'string'],
            'nombre_criteres' => ['nullable', 'integer', 'min:0'],
            'conformes' => ['nullable', 'integer', 'min:0'],
            'ecarts' => ['nullable', 'integer', 'min:0'],
            'total' => ['nullable', 'integer', 'min:0'],
            'ecarts_significatifs' => ['nullable', 'string'],
            'recommandation' => ['nullable', 'string', 'max:255'],
            'verificateur_nom' => ['nullable', 'string', 'max:100'],
            'verificateur_poste' => ['nullable', 'string', 'max:100'],
            'verificateur_date' => ['nullable', 'date'],
            'verificateur_signature' => ['nullable', 'string'],
        ]);

        $validated['reference_dossier'] = $this->generateReferenceDossier();

        $farm = Farm::create($validated);

        if ($farm->user?->email) {
            $this->mailer->sendView(
                $farm->user->email,
                'Nouvelle exploitation assignée : ' . $farm->name,
                'emails.farms.assigned_to_client',
                ['farm' => $farm]
            );
        }

        return redirect()->route('admin.farms.index')->with('success', 'Exploitation créée avec succès.');
    }

    public function show(Farm $farm)
    {
        $farm->load('user', 'photos', 'reports', 'clients.user', 'assignedTechnician.user');

        return view('admin.farms.show', compact('farm'));
    }

    public function pdf(Farm $farm)
    {
        $farm->load('user', 'photos', 'reports', 'clients.user', 'assignedTechnician.user');

        return view('admin.farms.pdf', compact('farm'));
    }

    public function edit($id)
    {
        $farm = Farm::findOrFail($id);
        $farm->load('user');

        $typeSolOptions = collect(TypeSol::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $typePointEauOptions = collect(TypePointEau::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $typeIrrigationOptions = collect(TypeIrrigation::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $topographieOptions = collect(Topographie::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $penteMoyenneOptions = collect(PenteMoyenne::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $formeParcelleOptions = collect(FormeParcelle::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $expositionParcelleOptions = collect(ExpositionParcelle::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $occupationActuelleOptions = collect(OccupationActuelle::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $couleurSolOptions = collect(CouleurSol::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $recommandationOptions = collect(Recommandation::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $farmStatusOptions = collect(FarmStatus::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $cultureTypeOptions = collect(CultureType::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $farmTypeOptions = collect(FarmType::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);
        $stadePhenologiqueOptions = collect(StadePhenologique::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label()
        ]);

        $clients = User::where('role', 'client')->get(['id', 'name']);

        return view('admin.farms.edit', [
            'farm' => $farm,
            'clients' => $clients,
            'typeSolOptions' => $typeSolOptions,
            'typePointEauOptions' => $typePointEauOptions,
            'typeIrrigationOptions' => $typeIrrigationOptions,
            'topographieOptions' => $topographieOptions,
            'penteMoyenneOptions' => $penteMoyenneOptions,
            'formeParcelleOptions' => $formeParcelleOptions,
            'expositionParcelleOptions' => $expositionParcelleOptions,
            'occupationActuelleOptions' => $occupationActuelleOptions,
            'couleurSolOptions' => $couleurSolOptions,
            'recommandationOptions' => $recommandationOptions,
            'farmStatusOptions' => $farmStatusOptions,
            'cultureTypeOptions' => $cultureTypeOptions,
            'farmTypeOptions' => $farmTypeOptions,
            'stadePhenologiqueOptions' => $stadePhenologiqueOptions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id'],
            'location' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:culture,elevage'],
            'culture_type' => ['required', 'string', 'max:255'],
            'total_area_hectares' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive,fallow'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'crop_stage' => ['nullable', 'string', 'max:100'],
            'crop_stage_progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'expected_harvest_date' => ['nullable', 'date'],
            'last_visit_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'reference_dossier' => ['nullable', 'string', 'max:50'],
            'date_declaration' => ['nullable', 'date'],
            'nom_client' => ['nullable', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:100'],
            'numero_cadastral' => ['nullable', 'string', 'max:50'],
            'surface_totale' => ['nullable', 'numeric', 'min:0'],
            'surface_cultivable' => ['nullable', 'numeric', 'min:0'],
            'forme_parcelle' => ['nullable', 'string', 'max:255'],
            'exposition_principale' => ['nullable', 'string', 'max:255'],
            'pente_moyenne' => ['nullable', 'string', 'max:255'],
            'altitude' => ['nullable', 'integer'],
            'topographie' => ['nullable', 'string', 'max:255'],
            'type_sol' => ['nullable', 'string', 'max:255'],
            'couleur_sol' => ['nullable', 'string', 'max:255'],
            'profondeur_sol' => ['nullable', 'string', 'max:20'],
            'presence_cailloux' => ['nullable', 'boolean'],
            'commentaire_cailloux' => ['nullable', 'string'],
            'problemes_erosion' => ['nullable', 'boolean'],
            'commentaire_erosion' => ['nullable', 'string'],
            'analyse_sol_realisee' => ['nullable', 'boolean'],
            'commentaire_analyse' => ['nullable', 'string'],
            'ph' => ['nullable', 'numeric', 'min:0', 'max:14'],
            'source_ph' => ['nullable', 'string', 'max:100'],
            'point_eau_proximite' => ['nullable', 'boolean'],
            'commentaire_point_eau' => ['nullable', 'string'],
            'type_point_eau' => ['nullable', 'string', 'max:255'],
            'distance_point_eau' => ['nullable', 'integer', 'min:0'],
            'systeme_irrigation' => ['nullable', 'boolean'],
            'commentaire_irrigation' => ['nullable', 'string'],
            'type_irrigation' => ['nullable', 'string', 'max:255'],
            'inondations_saisonnieres' => ['nullable', 'boolean'],
            'commentaire_inondations' => ['nullable', 'string'],
            'periode_secheresse' => ['nullable', 'string', 'max:100'],
            'occupation_actuelle' => ['nullable', 'string', 'max:255'],
            'cultures_place' => ['nullable', 'string', 'max:200'],
            'presence_arbres' => ['nullable', 'boolean'],
            'commentaire_arbres' => ['nullable', 'string'],
            'especes_ligneuses' => ['nullable', 'string'],
            'rendement_actuel' => ['nullable', 'string', 'max:50'],
            'antecedents_traitement' => ['nullable', 'boolean'],
            'commentaire_traitement' => ['nullable', 'string'],
            'produits_herbicides' => ['nullable', 'boolean'],
            'produits_pesticides' => ['nullable', 'boolean'],
            'produits_engrais' => ['nullable', 'boolean'],
            'produits_autre' => ['nullable', 'boolean'],
            'produits_autre_detail' => ['nullable', 'string', 'max:100'],
            'acces_carrossable' => ['nullable', 'boolean'],
            'commentaire_acces' => ['nullable', 'string'],
            'distance_route_principale' => ['nullable', 'numeric', 'min:0'],
            'cloture_existante' => ['nullable', 'boolean'],
            'commentaire_cloture' => ['nullable', 'string'],
            'batiment_hangar' => ['nullable', 'boolean'],
            'commentaire_batiment' => ['nullable', 'string'],
            'electricite_disponible' => ['nullable', 'boolean'],
            'commentaire_electricite' => ['nullable', 'string'],
            'reseau_telephonique' => ['nullable', 'boolean'],
            'commentaire_reseau' => ['nullable', 'string'],
            'observations_libres' => ['nullable', 'string'],
            'signature_date' => ['nullable', 'date'],
            'signature' => ['nullable', 'string'],
            'nombre_criteres' => ['nullable', 'integer', 'min:0'],
            'conformes' => ['nullable', 'integer', 'min:0'],
            'ecarts' => ['nullable', 'integer', 'min:0'],
            'total' => ['nullable', 'integer', 'min:0'],
            'ecarts_significatifs' => ['nullable', 'string'],
            'recommandation' => ['nullable', 'string', 'max:255'],
            'verificateur_nom' => ['nullable', 'string', 'max:100'],
            'verificateur_poste' => ['nullable', 'string', 'max:100'],
            'verificateur_date' => ['nullable', 'date'],
            'verificateur_signature' => ['nullable', 'string'],
        ]);

        $farm = Farm::findOrFail($id);

        if (empty($validated['reference_dossier'])) {
            $validated['reference_dossier'] = $this->generateReferenceDossier();
        }

        $farm->update($validated);

        if ($farm->user?->email) {
            $this->mailer->sendView(
                $farm->user->email,
                'Exploitation mise à jour : ' . $farm->name,
                'emails.farms.assigned_to_client',
                ['farm' => $farm]
            );
        }

        return redirect()->route('admin.farms.show', $farm)->with('success', 'Exploitation mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $farm = Farm::findOrFail($id);
        $farm->delete();

        return redirect()->route('admin.farms.index')
            ->with('success', 'Exploitation supprimée avec succès.');
    }
}
