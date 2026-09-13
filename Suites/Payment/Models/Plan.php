<?php

namespace Modules\Payment\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Offre d'abonnement (ex: Pro mensuel, Entreprise annuel).
 */
class Plan extends Model
{
    protected $table = 'suite_plans';

    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'currency',
        'period_months',
        'features',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'period_months' => 'integer',
            'features' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('price');
    }

    public function isFree(): bool
    {
        return (float) $this->price == 0.0;
    }

    public function billingCycleLabel(): string
    {
        return $this->period_months >= 12 ? 'an' : 'mois';
    }

    public function formattedPrice(): string
    {
        if ($this->isFree()) {
            return 'Gratuit';
        }

        return number_format((float) $this->price, 0, ',', ' ').' '.$this->currency;
    }
}
