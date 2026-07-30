<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntretienIntrant extends Model
{
    protected $table = 'entretien_intrants';

    protected $fillable = [
        'visite_cultures_id',
        'desherbage_effectue',
        'taille_elagage',
        'engrais_applique',
        'traitement_phytosanitaire',
        'mulching_realise',
        'compost_apporte',
        'intrants_utilises',
        'estimation_recolte',
        'date_estimee_recolte',
    ];

    protected function casts(): array
    {
        return [
            'desherbage_effectue' => 'boolean',
            'taille_elagage' => 'boolean',
            'engrais_applique' => 'boolean',
            'traitement_phytosanitaire' => 'boolean',
            'mulching_realise' => 'boolean',
            'compost_apporte' => 'boolean',
            'estimation_recolte' => 'decimal:2',
            'date_estimee_recolte' => 'date',
        ];
    }

    public function visiteCulture(): BelongsTo
    {
        return $this->belongsTo(VisiteCulture::class, 'visite_cultures_id');
    }
}
