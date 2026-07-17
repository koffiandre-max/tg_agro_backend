<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farm extends Model
{
    protected $fillable = [
        'user_id',
        'assigned_technician_id',
        'name',
        'location',
        'latitude',
        'longitude',
        'total_area_hectares',
        'culture_type',
        'status',
        'expected_harvest_date',
        'crop_stage',
        'crop_stage_progress',
        'last_visit_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'total_area_hectares' => 'decimal:2',
            'expected_harvest_date' => 'date',
            'last_visit_date' => 'date',
            'crop_stage_progress' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTechnician(): BelongsTo
    {
        return $this->belongsTo(Technician::class, 'assigned_technician_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_farms');
    }

    // Classes du badge de statut
    public function statusBadgeClasses(): string
    {
        return match ($this->status) {
            'active' => 'bg-green-100 text-green-700',
            'inactive' => 'bg-red-100 text-red-700',
            'fallow' => 'bg-amber-100 text-amber-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }
}
