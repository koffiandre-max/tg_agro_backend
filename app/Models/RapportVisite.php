<?php

namespace App\Models;

use App\Enums\ConditionMeteo;
use App\Enums\DureeVisite;
use App\Enums\StatutRapport;
use App\Enums\TypeVisite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RapportVisite extends Model
{
    protected $table = 'rapport_visites';

    protected $fillable = [
        'technicien_id',
        'date_visite',
        'client_id',
        'localisation_parcelle',
        'type_visite',
        'conditions_meteo',
        'superficie_visitee_ha',
        'duree_visite',
        'gps_latitude',
        'gps_longitude',
        'cultures_presentes',
        'culture_autre_precision',
        'stade_phenologique',
        'avancement_cycle_pourcent',
        'etat_couvert_vegetal',
        'ravageurs_maladies',
        'ravageur_autre_precision',
        'niveau_infestation',
        'observations_ravageurs',
        'etat_hydrique_sol',
        'irrigation_en_place',
        'etat_structure_sol',
        'ph_sol',
        'entretien_intrants',
        'intrants_utilises',
        'estimation_recolte_kg',
        'date_estimee_recolte',
        'animaux_presents',
        'animal_autre_precision',
        'effectif_total',
        'mortalite_constatee',
        'naissances_depuis_derniere_visite',
        'ventes_abattages_depuis_derniere_visite',
        'etat_corporel_general',
        'signes_cliniques',
        'signe_autre_precision',
        'observations_sanitaires',
        'soins_traitements',
        'produits_administres',
        'etat_alimentation',
        'eau_abreuvement',
        'etat_batiments_enclos',
        'production_laitiere_l_j',
        'production_oeufs_nb_j',
        'gain_poids_kg_mois',
        'resume_visite',
        'niveau_alerte',
        'description_alerte',
        'prochaine_visite_date',
        'prochaine_visite_raison',
        'note_interne',
        'message_client',
        'statut',
        'motif_rejet',
        'valide_par',
        'valide_at',
        'envoye_at',
    ];

    protected function casts(): array
    {
        return [
            'date_visite' => 'date',
            'superficie_visitee_ha' => 'decimal:2',
            'gps_latitude' => 'decimal:7',
            'gps_longitude' => 'decimal:7',
            'cultures_presentes' => 'array',
            'ravageurs_maladies' => 'array',
            'entretien_intrants' => 'array',
            'animaux_presents' => 'array',
            'signes_cliniques' => 'array',
            'soins_traitements' => 'array',
            'avancement_cycle_pourcent' => 'integer',
            'etat_couvert_vegetal' => 'integer',
            'niveau_infestation' => 'integer',
            'irrigation_en_place' => 'boolean',
            'ph_sol' => 'decimal:1',
            'estimation_recolte_kg' => 'decimal:2',
            'date_estimee_recolte' => 'date',
            'effectif_total' => 'integer',
            'mortalite_constatee' => 'integer',
            'naissances_depuis_derniere_visite' => 'integer',
            'ventes_abattages_depuis_derniere_visite' => 'integer',
            'etat_corporel_general' => 'integer',
            'etat_batiments_enclos' => 'integer',
            'production_laitiere_l_j' => 'decimal:2',
            'production_oeufs_nb_j' => 'integer',
            'gain_poids_kg_mois' => 'decimal:2',
            'prochaine_visite_date' => 'date',
            'valide_at' => 'datetime',
            'envoye_at' => 'datetime',
            'type_visite' => TypeVisite::class,
            'conditions_meteo' => ConditionMeteo::class,
            'duree_visite' => DureeVisite::class,
            'statut' => StatutRapport::class,
        ];
    }

    public function technicien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(RapportVisitePhoto::class, 'rapport_visite_id');
    }
}
