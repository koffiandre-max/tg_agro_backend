<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EtatElevage extends Model
{
    protected $table = 'etats_elevage';

    protected $fillable = [
        'visite_elevage_id',
        'etat_corporel',
    ];

    protected function casts(): array
    {
        return [
            'etat_corporel' => 'integer',
        ];
    }

    public function visiteElevage(): BelongsTo
    {
        return $this->belongsTo(VisiteElevage::class, 'visite_elevage_id');
    }
}
