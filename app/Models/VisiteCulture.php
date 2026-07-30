<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VisiteCulture extends Model
{
    protected $table = 'visite_cultures';

    protected $fillable = [
        'rapport_id',
    ];

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
