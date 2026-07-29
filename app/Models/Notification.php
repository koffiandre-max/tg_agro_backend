<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'utilisateur_id',
        'notifiable_type',
        'notifiable_id',
        'type_notification',
        'message',
        'lien',
        'lue',
        'date_creation',
    ];

    protected function casts(): array
    {
        return [
            'lue' => 'boolean',
            'date_creation' => 'datetime',
        ];
    }

    /**
     * Relation polymorphique : la notification peut concerner RapportVisite, Report, etc.
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}