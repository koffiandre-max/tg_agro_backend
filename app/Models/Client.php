<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'country_of_residence',
        'country_of_origin',
        'city_of_residence',
        'subscription_type',
        'subscription_expires_at',
        'total_investment',
        'avatar',
        'assigned_technician_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subscription_expires_at' => 'date',
            'assigned_technician_id' => 'integer',
            'total_investment' => 'decimal:2',
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

    public function farms(): HasMany
    {
        return $this->hasMany(Farm::class, 'user_id', 'user_id');
    }

    public function clientFarms()
    {
        return $this->hasMany(ClientFarm::class);
    }

    public function assignedFarms()
    {
        return $this->belongsToMany(Farm::class, 'client_farms');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'client_id', 'id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class, 'client_id', 'id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'user_id', 'user_id');
    }
}
