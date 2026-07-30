<?php

namespace App\Models;

use App\Enums\AnimalType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TypeAnimal extends Model
{
    protected $table = 'types_animaux';

    protected $fillable = [
        'visite_elevage_id',
        'animal_type',
        'animal_autre_detail',
        'present',
        'effectif_total',
        'mortalite_constatee',
        'naissances_visite',
        'ventes_abatages',
    ];

    protected function casts(): array
    {
        return [
            'animal_type' => AnimalType::class,
            'present' => 'boolean',
            'effectif_total' => 'integer',
            'mortalite_constatee' => 'integer',
            'naissances_visite' => 'integer',
            'ventes_abatages' => 'integer',
        ];
    }

    public function visiteElevage(): BelongsTo
    {
        return $this->belongsTo(VisiteElevage::class, 'visite_elevage_id');
    }
}
