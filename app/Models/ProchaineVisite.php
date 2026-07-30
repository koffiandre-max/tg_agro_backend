<?php

namespace App\Models;

use App\Enums\RaisonProchaineVisite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProchaineVisite extends Model
{
    protected $table = 'prochaine_visite';

    protected $fillable = [
        'rapport_id',
        'date_souhaitee',
        'raison',
    ];

    protected function casts(): array
    {
        return [
            'date_souhaitee' => 'date',
            'raison' => RaisonProchaineVisite::class,
        ];
    }

    public function rapport(): BelongsTo
    {
        return $this->belongsTo(RapportVisite::class, 'rapport_id');
    }
}
