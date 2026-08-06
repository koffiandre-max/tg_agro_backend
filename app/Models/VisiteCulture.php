<?php

namespace App\Models;

use App\Enums\StadePhenologique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisiteCulture extends Model
{
    protected $table = 'visite_cultures';

    protected $fillable = [
        'rapport_id',
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
    ];

    protected function casts(): array
    {
        return [
            'cultures_presentes' => 'array',
            'ravageurs_maladies' => 'array',
            'entretien_intrants' => 'array',
            'stade_phenologique' => StadePhenologique::class,
            'avancement_cycle_pourcent' => 'integer',
            'etat_couvert_vegetal' => 'integer',
            'niveau_infestation' => 'integer',
            'irrigation_en_place' => 'boolean',
            'ph_sol' => 'decimal:1',
            'estimation_recolte_kg' => 'decimal:2',
            'date_estimee_recolte' => 'date',
        ];
    }

    public function rapport(): BelongsTo
    {
        return $this->belongsTo(RapportVisite::class, 'rapport_id');
    }

    public function typesCultures(): HasMany
    {
        return $this->hasMany(TypeCulture::class, 'visite_cultures_id');
    }

    public function etatsVegetatifs(): HasMany
    {
        return $this->hasMany(EtatVegetatif::class, 'visite_cultures_id');
    }

    public function ravageursMaladies(): HasMany
    {
        return $this->hasMany(RavageurMaladie::class, 'visite_cultures_id');
    }

    public function observationsRavageurs(): HasMany
    {
        return $this->hasMany(ObservationRavageur::class, 'visite_cultures_id');
    }

    public function solsIrrigations(): HasMany
    {
        return $this->hasMany(SolIrrigation::class, 'visite_cultures_id');
    }

    public function entretienIntrants(): HasMany
    {
        return $this->hasMany(EntretienIntrant::class, 'visite_cultures_id');
    }
}
