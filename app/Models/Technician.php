<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Technician extends Model
{
    protected $fillable = [
        'user_id',
        'phone_secondary',
        'location_base',
        'is_available',
        'max_concurrent_missions',
        'current_workload',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'max_concurrent_missions' => 'integer',
            'current_workload' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function farms(): HasMany
    {
        return $this->hasMany(Farm::class, 'assigned_technician_id');
    }

    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'technician_id', 'user_id');
    }
}
