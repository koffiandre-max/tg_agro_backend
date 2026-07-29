<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Photo extends Model
{
    protected $fillable = [
        'user_id',
        'farm_id',
        'client_id',
        'technician_id',
        'photo_path',
        'thumbnail_path',
        'caption',
        'latitude',
        'longitude',
        'taken_at',
        'is_visible_to_client',
        'is_validated',
        'validated_at',
        'file_size',
        'photoable_type',
        'photoable_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'taken_at' => 'datetime',
            'is_visible_to_client' => 'boolean',
            'is_validated' => 'boolean',
            'validated_at' => 'datetime',
            'file_size' => 'integer',
        ];
    }

    /**
     * Relation polymorphique : la photo peut appartenir à Farm, RapportVisite, etc.
     */
    public function photoable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
