<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisiteAutre extends Model
{
    protected $table = 'visite_autres';

    protected $fillable = [
        'rapport_id',
        'type_autre',
        'description_activite',
        'observations_specifiques',
    ];

    public function rapport(): BelongsTo
    {
        return $this->belongsTo(RapportVisite::class, 'rapport_id');
    }
}
