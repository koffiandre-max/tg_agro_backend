<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EmailAttempt extends Model
{
    protected $fillable = [
        'recipient_email',
        'subject',
        'template',
        'template_data',
        'attempts',
        'max_attempts',
        'status',
        'last_error',
        'last_attempt_at',
        'sent_at',
    ];

    protected $casts = [
        'template_data' => 'array',
        'attempts' => 'integer',
        'max_attempts' => 'integer',
        'last_attempt_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function scopePendingRetry($query)
    {
        return $query->where('status', 'failed')
            ->whereColumn('attempts', '<', 'max_attempts');
    }

    public function scopeAbandoned($query)
    {
        return $query->where('status', 'abandoned');
    }
}
