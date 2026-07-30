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
    ];

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
