<?php

namespace App\Models;

use App\Enums\FarmStatus;
use App\Enums\FarmType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Farm extends Model
{
    protected $fillable = [
        'user_id',
        'assigned_technician_id',
        'name',
        'location',
        'latitude',
        'longitude',
        'total_area_hectares',
        'type',
        'culture_type',
        'status',
        'expected_harvest_date',
        'crop_stage',
        'crop_stage_progress',
        'last_visit_date',
        'notes',
        // Champs de parcelles
        'reference_dossier',
        'date_declaration',
        'nom_client',
        'contact',
        'numero_cadastral',
        // Caractéristiques générales
        'surface_totale',
        'surface_cultivable',
        'forme_parcelle',
        'exposition_principale',
        'pente_moyenne',
        'altitude',
        'topographie',
        // Sols
        'type_sol',
        'couleur_sol',
        'profondeur_sol',
        'presence_cailloux',
        'commentaire_cailloux',
        'problemes_erosion',
        'commentaire_erosion',
        'analyse_sol_realisee',
        'commentaire_analyse',
        'ph',
        'source_ph',
        // Ressources en eau
        'point_eau_proximite',
        'commentaire_point_eau',
        'type_point_eau',
        'distance_point_eau',
        'systeme_irrigation',
        'commentaire_irrigation',
        'type_irrigation',
        'inondations_saisonnieres',
        'commentaire_inondations',
        'periode_secheresse',
        // Végétation et usages
        'occupation_actuelle',
        'cultures_place',
        'presence_arbres',
        'commentaire_arbres',
        'especes_ligneuses',
        'rendement_actuel',
        'antecedents_traitement',
        'commentaire_traitement',
        'produits_herbicides',
        'produits_pesticides',
        'produits_engrais',
        'produits_autre',
        'produits_autre_detail',
        // Accès et infrastructures
        'acces_carrossable',
        'commentaire_acces',
        'distance_route_principale',
        'cloture_existante',
        'commentaire_cloture',
        'batiment_hangar',
        'commentaire_batiment',
        'electricite_disponible',
        'commentaire_electricite',
        'reseau_telephonique',
        'commentaire_reseau',
        // Remarques client
        'observations_libres',
        'signature_date',
        'signature',
        // Vérifications - Générales
        'surface_totale_declare',
        'surface_totale_constate',
        'surface_totale_concorde',
        'surface_cultivable_declare',
        'surface_cultivable_constate',
        'surface_cultivable_concorde',
        'exposition_declare',
        'exposition_constate',
        'exposition_concorde',
        'pente_declare',
        'pente_constate',
        'pente_concorde',
        'topographie_declare',
        'topographie_constate',
        'topographie_concorde',
        // Vérifications - Sols
        'type_sol_declare',
        'type_sol_constate',
        'type_sol_concorde',
        'profondeur_sol_declare',
        'profondeur_sol_constate',
        'profondeur_sol_concorde',
        'presence_pierres_declare',
        'presence_pierres_constate',
        'presence_pierres_concorde',
        'erosion_declare',
        'erosion_constate',
        'erosion_concorde',
        'ph_sol_declare',
        'ph_sol_constate',
        'ph_sol_concorde',
        // Vérifications - Eau
        'point_eau_declare',
        'point_eau_constate',
        'point_eau_concorde',
        'type_point_eau_declare',
        'type_point_eau_constate',
        'type_point_eau_concorde',
        'distance_point_eau_declare',
        'distance_point_eau_constate',
        'distance_point_eau_concorde',
        'irrigation_declare',
        'irrigation_constate',
        'irrigation_concorde',
        'inondations_declare',
        'inondations_constate',
        'inondations_concorde',
        // Vérifications - Végétation
        'occupation_declare',
        'occupation_constate',
        'occupation_concorde',
        'cultures_declare',
        'cultures_constate',
        'cultures_concorde',
        'arbres_declare',
        'arbres_constate',
        'arbres_concorde',
        'traitements_declare',
        'traitements_constate',
        'traitements_concorde',
        // Vérifications - Accès
        'acces_declare',
        'acces_constate',
        'acces_concorde',
        'distance_route_declare',
        'distance_route_constate',
        'distance_route_concorde',
        'cloture_declare',
        'cloture_constate',
        'cloture_concorde',
        'batiment_declare',
        'batiment_constate',
        'batiment_concorde',
        'electricite_declare',
        'electricite_constate',
        'electricite_concorde',
        // Synthèse vérification
        'nombre_criteres',
        'conformes',
        'ecarts',
        'total',
        'ecarts_significatifs',
        'recommandation',
        'verificateur_nom',
        'verificateur_poste',
        'verificateur_date',
        'verificateur_signature',
    ];

    protected function casts(): array
    {
        return [
            'type' => FarmType::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'total_area_hectares' => 'decimal:2',
            'expected_harvest_date' => 'date',
            'last_visit_date' => 'date',
            'crop_stage_progress' => 'integer',
            'date_declaration' => 'date',
            'signature_date' => 'date',
            'verificateur_date' => 'date',
            'surface_totale' => 'decimal:2',
            'surface_cultivable' => 'decimal:2',
            'ph' => 'decimal:2',
            'distance_route_principale' => 'decimal:2',
            'presence_cailloux' => 'boolean',
            'problemes_erosion' => 'boolean',
            'analyse_sol_realisee' => 'boolean',
            'point_eau_proximite' => 'boolean',
            'systeme_irrigation' => 'boolean',
            'inondations_saisonnieres' => 'boolean',
            'presence_arbres' => 'boolean',
            'antecedents_traitement' => 'boolean',
            'produits_herbicides' => 'boolean',
            'produits_pesticides' => 'boolean',
            'produits_engrais' => 'boolean',
            'produits_autre' => 'boolean',
            'acces_carrossable' => 'boolean',
            'cloture_existante' => 'boolean',
            'batiment_hangar' => 'boolean',
            'electricite_disponible' => 'boolean',
            'reseau_telephonique' => 'boolean',
            'surface_totale_concorde' => 'boolean',
            'surface_cultivable_concorde' => 'boolean',
            'exposition_concorde' => 'boolean',
            'pente_concorde' => 'boolean',
            'topographie_concorde' => 'boolean',
            'type_sol_concorde' => 'boolean',
            'profondeur_sol_concorde' => 'boolean',
            'presence_pierres_concorde' => 'boolean',
            'erosion_concorde' => 'boolean',
            'ph_sol_concorde' => 'boolean',
            'point_eau_concorde' => 'boolean',
            'type_point_eau_concorde' => 'boolean',
            'distance_point_eau_concorde' => 'boolean',
            'irrigation_concorde' => 'boolean',
            'inondations_concorde' => 'boolean',
            'occupation_concorde' => 'boolean',
            'cultures_concorde' => 'boolean',
            'arbres_concorde' => 'boolean',
            'traitements_concorde' => 'boolean',
            'acces_concorde' => 'boolean',
            'distance_route_concorde' => 'boolean',
            'cloture_concorde' => 'boolean',
            'batiment_concorde' => 'boolean',
            'electricite_concorde' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'assigned_technician_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_farms');
    }

    public function statusLabel(): string
    {
        try {
            return FarmStatus::from($this->status)->label();
        } catch (\ValueError $e) {
            return 'Inconnu';
        }
    }

    public function statusBadgeClasses(): string
    {
        return match ($this->status) {
            FarmStatus::ACTIVE->value => 'bg-green-100 text-green-700',
            FarmStatus::INACTIVE->value => 'bg-red-100 text-red-700',
            FarmStatus::FALLOW->value => 'bg-amber-100 text-amber-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
