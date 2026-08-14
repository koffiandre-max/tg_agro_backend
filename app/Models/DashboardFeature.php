<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Persistance en base de l'ordre et de l'activation des statistiques
 * du tableau de bord (configurées depuis /settings).
 */
class DashboardFeature extends Model
{
    protected $fillable = [
        'key',
        'label',
        'icon',
        'enabled',
        'position',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'position' => 'integer',
    ];
}
