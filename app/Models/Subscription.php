<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'currency',
        'start_date',
        'end_date',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_code',
        'payment_provider',
        'payment_phone',
        'payment_info',
        'auto_renew',
        'auto_payment',
        'last_payment_at',
        'next_payment_date',
        'next_payment_reminder_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'last_payment_at' => 'datetime',
            'next_payment_date' => 'date',
            'next_payment_reminder_at' => 'datetime',
            'auto_renew' => 'boolean',
            'auto_payment' => 'boolean',
            'payment_info' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
