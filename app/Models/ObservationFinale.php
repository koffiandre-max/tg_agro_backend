<?php

namespace App\Models;

use App\Enums\NiveauAlerte;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObservationFinale extends Model
{
    protected $table = 'observations_finales';

    protected $fillable = [
        'rapport_id',
        'resume_visite',
        'niveau_alerte',
        'description_alerte',
    ];

    protected function casts(): array
    {
        return [
            'niveau_alerte' => NiveauAlerte::class,
        ];
    }

    public function rapport(): BelongsTo
    {
        return $this->belongsTo(RapportVisite::class, 'rapport_id');
    }
}
