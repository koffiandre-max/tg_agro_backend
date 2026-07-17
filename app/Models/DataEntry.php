<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataEntry extends Model
{
    protected $fillable = [
        'farm_id',
        'client_id',
        'technician_id',
        'crop_stage',
        'crop_stage_progress',
        'estimated_harvest_date',
        'inputs_used',
        'observations',
        'weather_conditions',
        'status',
        'validated_by',
        'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'crop_stage_progress' => 'integer',
            'estimated_harvest_date' => 'date',
            'validated_at' => 'datetime',
        ];
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

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
