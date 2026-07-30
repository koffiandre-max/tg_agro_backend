<?php

namespace App\Models;

use App\Enums\CultureType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TypeCulture extends Model
{
    protected $table = 'types_cultures';

    protected $fillable = [
        'visite_cultures_id',
        'culture_type',
        'culture_autre_detail',
        'present',
    ];

    protected function casts(): array
    {
        return [
            'culture_type' => CultureType::class,
            'present' => 'boolean',
        ];
    }

    public function visiteCulture(): BelongsTo
    {
        return $this->belongsTo(VisiteCulture::class, 'visite_cultures_id');
    }
}
