<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisiteElevage extends Model
{
    protected $table = 'visite_elevage';

    protected $fillable = [
        'rapport_id',
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
    ];

    protected function casts(): array
    {
        return [
            'animaux_presents' => 'array',
            'signes_cliniques' => 'array',
            'soins_traitements' => 'array',
            'effectif_total' => 'integer',
            'mortalite_constatee' => 'integer',
            'naissances_depuis_derniere_visite' => 'integer',
            'ventes_abattages_depuis_derniere_visite' => 'integer',
            'etat_corporel_general' => 'integer',
            'etat_batiments_enclos' => 'integer',
            'production_laitiere_l_j' => 'decimal:2',
            'production_oeufs_nb_j' => 'integer',
            'gain_poids_kg_mois' => 'decimal:2',
        ];
    }

    public function rapport(): BelongsTo
    {
        return $this->belongsTo(RapportVisite::class, 'rapport_id');
    }

    public function typesAnimaux(): HasMany
    {
        return $this->hasMany(TypeAnimal::class, 'visite_elevage_id');
    }

    public function etatsElevage(): HasMany
    {
        return $this->hasMany(EtatElevage::class, 'visite_elevage_id');
    }

    public function signesCliniques(): HasMany
    {
        return $this->hasMany(SigneClinique::class, 'visite_elevage_id');
    }

    public function observationsSanitaires(): HasMany
    {
        return $this->hasMany(ObservationSanitaire::class, 'visite_elevage_id');
    }

    public function soinsAnimaux(): HasMany
    {
        return $this->hasMany(SoinAnimal::class, 'visite_elevage_id');
    }

    public function alimentationEau(): HasMany
    {
        return $this->hasMany(AlimentationEau::class, 'visite_elevage_id');
    }

    public function performancesElevage(): HasMany
    {
        return $this->hasMany(PerformanceElevage::class, 'visite_elevage_id');
    }
}
