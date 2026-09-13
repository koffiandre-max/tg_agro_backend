<?php

namespace Modules\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Transaction de paiement (carte bancaire ou mobile money) via une passerelle.
 *
 * NB : aucune donnée de carte complète n'est stockée (conformité PCI) —
 * la saisie se fait sur la page sécurisée de la passerelle ; seuls la marque
 * et les 4 derniers chiffres sont archivés.
 */
class Payment extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_FAILED = 'failed';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_REFUNDED = 'refunded';

    public const METHOD_CARD = 'card';
    public const METHOD_MOBILE_MONEY = 'mobile_money';

    protected $table = 'suite_payments';

    protected $fillable = [
        'reference',
        'business_id',
        'user_id',
        'subscription_id',
        'plan_id',
        'amount',
        'currency',
        'description',
        'method',
        'provider',
        'provider_reference',
        'status',
        'payer_name',
        'payer_phone',
        'card_brand',
        'card_last4',
        'is_renewal',
        'metadata',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_renewal' => 'boolean',
            'metadata' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Les routes du module identifient les paiements par leur référence.
     */
    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSucceeded(): bool
    {
        return $this->status === self::STATUS_SUCCEEDED;
    }

    public function methodLabel(): string
    {
        return match ($this->method) {
            self::METHOD_CARD => 'Carte bancaire',
            self::METHOD_MOBILE_MONEY => 'Mobile Money',
            default => '—',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'En attente',
            self::STATUS_SUCCEEDED => 'Payé',
            self::STATUS_FAILED => 'Échoué',
            self::STATUS_CANCELED => 'Annulé',
            self::STATUS_REFUNDED => 'Remboursé',
            default => ucfirst((string) $this->status),
        };
    }

    public function formattedAmount(): string
    {
        return number_format((float) $this->amount, 0, ',', ' ').' '.$this->currency;
    }
}
