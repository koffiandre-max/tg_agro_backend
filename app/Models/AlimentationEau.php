<?php

namespace App\Models;

use App\Enums\EauAbreuvement;
use App\Enums\EtatAlimentation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlimentationEau extends Model
{
    protected $table = 'alimentation_eau';

    protected $fillable = [
        'visite_elevage_id',
        'etat_alimentation',
        'eau_abreuvement',
        'etat_batiments',
    ];

    protected function casts(): array
    {
        return [
            'etat_alimentation' => EtatAlimentation::class,
            'eau_abreuvement' => EauAbreuvement::class,
            'etat_batiments' => 'integer',
        ];
    }

    public function visiteElevage(): BelongsTo
    {
        return $this->belongsTo(VisiteElevage::class, 'visite_elevage_id');
    }
}
