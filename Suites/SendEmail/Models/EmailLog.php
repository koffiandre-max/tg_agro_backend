<?php

namespace Modules\SendEmail\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Journal d'un email envoyé (ou tenté) via le module SendEmail.
 */
class EmailLog extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    protected $table = 'suite_email_logs';

    protected $fillable = [
        'business_id',
        'user_id',
        'to_email',
        'to_name',
        'subject',
        'body',
        'is_html',
        'status',
        'error',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'is_html' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    public function scopeForBusiness(Builder $query, int $businessId): Builder
    {
        return $query->where('business_id', $businessId);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_SENT => 'Envoyé',
            self::STATUS_FAILED => 'Échec',
            default => ucfirst((string) $this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_SENT => 'bg-green-100 text-green-800',
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            default => 'bg-red-100 text-red-800',
        };
    }
}
