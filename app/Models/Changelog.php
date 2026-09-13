<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Entrée du journal des changements (architecture "changelog").
 *
 * Une entrée décrit une opération (created | updated | deleted | restored)
 * sur une entité donnée afin que le mobile puisse se synchroniser
 * de façon incrémentale avec un curseur égal à son identifiant.
 */
class Changelog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'operation',
        'payload',
        'user_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'entity_id' => 'integer',
            'payload' => 'array',
            'user_id' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}