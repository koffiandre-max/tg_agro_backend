<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoinAnimal extends Model
{
    protected $table = 'soins_animaux';

    protected $fillable = [
        'visite_elevage_id',
        'vaccination_effectuee',
        'deparasitage_interne',
        'deparasitage_externe',
        'traitement_antibiotique',
        'soins_plaies',
        'consultation_veterinaire',
        'produits_administres',
    ];

    protected function casts(): array
    {
        return [
            'vaccination_effectuee' => 'boolean',
            'deparasitage_interne' => 'boolean',
            'deparasitage_externe' => 'boolean',
            'traitement_antibiotique' => 'boolean',
            'soins_plaies' => 'boolean',
            'consultation_veterinaire' => 'boolean',
        ];
    }

    public function visiteElevage(): BelongsTo
    {
        return $this->belongsTo(VisiteElevage::class, 'visite_elevage_id');
    }
}
