<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Enregistrement d'une opération hors-ligne du mobile (outbox).
 *
 * Un `client_uuid` (ID généré localement par l'appareil) mappe une opération
 * à l'entité serveur créée, garantissant l'idempotence lors des renvois
 * (retry après perte de réseau).
 */
class SyncOperation extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'client_uuid',
        'entity_type',
        'entity_id',
        'operation',
        'status',
        'error_message',
        'applied_at',
    ];

    protected function casts(): array
    {
        return [
            'entity_id' => 'integer',
            'applied_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}