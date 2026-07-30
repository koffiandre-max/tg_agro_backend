<?php

namespace App\Models;

use App\Enums\StadePhenologique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtatVegetatif extends Model
{
    protected $table = 'etats_vegetatifs';

    protected $fillable = [
        'visite_cultures_id',
        'stade_phenologique',
        'avancement_cycle',
        'etat_couvert',
    ];

    protected function casts(): array
    {
        return [
            'stade_phenologique' => StadePhenologique::class,
            'avancement_cycle' => 'integer',
            'etat_couvert' => 'integer',
        ];
    }

    public function visiteCulture(): BelongsTo
    {
        return $this->belongsTo(VisiteCulture::class, 'visite_cultures_id');
    }
}
