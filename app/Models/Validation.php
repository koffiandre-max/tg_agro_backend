<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Validation extends Model
{
    protected $fillable = [
        'validable_type',
        'validable_id',
        'admin_id',
        'action',
        'motif',
        'date_action',
    ];

    protected function casts(): array
    {
        return [
            'date_action' => 'datetime',
        ];
    }

    /**
     * Relation polymorphique : la validation peut concerner RapportVisite, Report, etc.
     */
    public function validable(): MorphTo
    {
        return $this->morphTo();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
