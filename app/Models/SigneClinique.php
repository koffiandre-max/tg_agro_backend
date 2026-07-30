<?php

namespace App\Models;

use App\Enums\TypeSigneClinique;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SigneClinique extends Model
{
    protected $table = 'signes_cliniques';

    protected $fillable = [
        'visite_elevage_id',
        'type_signe',
        'signe_autre_detail',
        'present',
    ];

    protected function casts(): array
    {
        return [
            'type_signe' => TypeSigneClinique::class,
            'present' => 'boolean',
        ];
    }

    public function visiteElevage(): BelongsTo
    {
        return $this->belongsTo(VisiteElevage::class, 'visite_elevage_id');
    }
}
