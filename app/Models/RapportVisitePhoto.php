<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RapportVisitePhoto extends Model
{
    protected $table = 'rapport_visite_photos';

    protected $fillable = [
        'rapport_visite_id',
        'chemin',
        'legende',
        'gps_latitude',
        'gps_longitude',
        'pris_le',
    ];

    protected function casts(): array
    {
        return [
            'gps_latitude' => 'decimal:7',
            'gps_longitude' => 'decimal:7',
            'pris_le' => 'datetime',
        ];
    }

    public function rapportVisite(): BelongsTo
    {
        return $this->belongsTo(RapportVisite::class, 'rapport_visite_id');
    }
}
