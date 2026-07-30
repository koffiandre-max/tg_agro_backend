<?php

namespace App\Models;

use App\Enums\EtatHydriqueSol;
use App\Enums\EtatStructureSol;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolIrrigation extends Model
{
    protected $table = 'sols_irrigations';

    protected $fillable = [
        'visite_cultures_id',
        'etat_hydrique_sol',
        'irrigation_place',
        'etat_structure_sol',
        'ph_sol_mesure',
    ];

    protected function casts(): array
    {
        return [
            'etat_hydrique_sol' => EtatHydriqueSol::class,
            'irrigation_place' => 'boolean',
            'etat_structure_sol' => EtatStructureSol::class,
            'ph_sol_mesure' => 'decimal:2',
        ];
    }

    public function visiteCulture(): BelongsTo
    {
        return $this->belongsTo(VisiteCulture::class, 'visite_cultures_id');
    }
}
