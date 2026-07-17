<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketPrice extends Model
{
    protected $fillable = [
        'product_name',
        'unit',
        'price_per_unit',
        'currency',
        'region',
        'recorded_at',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'date',
            'price_per_unit' => 'decimal:2',
        ];
    }
}
