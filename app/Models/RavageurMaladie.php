<?php

namespace App\Models;

use App\Enums\TypeProbleme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RavageurMaladie extends Model
{
    protected $table = 'ravageurs_maladies';

    protected $fillable = [
        'visite_cultures_id',
        'type_probleme',
        'probleme_autre_detail',
        'present',
        'niveau_infestation',
    ];

    protected function casts(): array
    {
        return [
            'type_probleme' => TypeProbleme::class,
            'present' => 'boolean',
            'niveau_infestation' => 'integer',
        ];
    }

    public function visiteCulture(): BelongsTo
    {
        return $this->belongsTo(VisiteCulture::class, 'visite_cultures_id');
    }
}
