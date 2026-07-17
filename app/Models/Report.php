<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'farm_id',
        'client_id',
        'technician_id',
        'title',
        'type',
        'file_path',
        'file_original_name',
        'file_size',
        'notes',
        'status',
        'rejection_reason',
        'is_validated',
        'validated_by',
        'validated_at',
        'seen_by_client',
    ];

    protected function casts(): array
    {
        return [
            'seen_by_client' => 'boolean',
            'file_size' => 'integer',
            'is_validated' => 'boolean',
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
