<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceElevage extends Model
{
    protected $table = 'performances_elevage';

    protected $fillable = [
        'visite_elevage_id',
        'production_laitiere',
        'production_oeufs',
        'gain_poids',
    ];

    protected function casts(): array
    {
        return [
            'production_laitiere' => 'decimal:2',
            'production_oeufs' => 'integer',
            'gain_poids' => 'decimal:2',
        ];
    }

    public function visiteElevage(): BelongsTo
    {
        return $this->belongsTo(VisiteElevage::class, 'visite_elevage_id');
    }
}
