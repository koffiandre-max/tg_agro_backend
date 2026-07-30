<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservationRavageur extends Model
{
    protected $table = 'observations_ravageurs';

    protected $fillable = [
        'visite_cultures_id',
        'observations',
    ];

    public function visiteCulture(): BelongsTo
    {
        return $this->belongsTo(VisiteCulture::class, 'visite_cultures_id');
    }
}
