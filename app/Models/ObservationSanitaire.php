<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservationSanitaire extends Model
{
    protected $table = 'observations_sanitaires';

    protected $fillable = [
        'visite_elevage_id',
        'observations',
    ];

    public function visiteElevage(): BelongsTo
    {
        return $this->belongsTo(VisiteElevage::class, 'visite_elevage_id');
    }
}
