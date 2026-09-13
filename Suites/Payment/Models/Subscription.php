<?php

namespace Modules\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Abonnement d'un tenant (business) à une offre.
 *
 * Statuts :
 *  - pending   : en attente du premier paiement.
 *  - active    : payé et valide jusqu'à ends_at.
 *  - past_due  : renouvellement automatique en échec (période de grâce).
 *  - canceled  : annulé par l'utilisateur (à la fin de la période courante).
 *  - expired   : période terminée sans renouvellement.
 */
class Subscription extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAST_DUE = 'past_due';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_EXPIRED = 'expired';

    protected $table = 'suite_subscriptions';

    protected $fillable = [
        'business_id',
        'user_id',
        'plan_id',
        'status',
        'period_months',
        'amount',
        'currency',
        'starts_at',
        'ends_at',
        'next_payment_at',
        'auto_renew',
        'payment_method',
        'payment_details',
        'canceled_at',
    ];

    protected function casts(): array
    {
        return [
            'period_months' => 'integer',
            'amount' => 'decimal:2',
            'starts_at' => 'date',
            'ends_at' => 'date',
            'next_payment_at' => 'date',
            'auto_renew' => 'boolean',
            'payment_details' => 'array',
            'canceled_at' => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'subscription_id');
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->ends_at !== null
            && $this->ends_at->isFuture();
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Jours restants avant la fin de la période (0 si dépassé).
     */
    public function daysRemaining(): int
    {
        if ($this->ends_at === null) {
            return 0;
        }

        return (int) max(0, ceil(now()->startOfDay()->floatDiffInDays($this->ends_at->copy()->endOfDay())));
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'En attente de paiement',
            self::STATUS_ACTIVE => 'Actif',
            self::STATUS_PAST_DUE => 'Paiement en retard',
            self::STATUS_CANCELED => 'Annulé',
            self::STATUS_EXPIRED => 'Expiré',
            default => ucfirst((string) $this->status),
        };
    }

    public function methodLabel(): string
    {
        return match ($this->payment_method) {
            Payment::METHOD_CARD => 'Carte bancaire',
            Payment::METHOD_MOBILE_MONEY => 'Mobile Money',
            default => '—',
        };
    }
}
