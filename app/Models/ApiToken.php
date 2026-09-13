<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Jeton d'authentification de l'application mobile.
 * Seul le hash (sha256) du jeton est conservé en base.
 */
class ApiToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'device_name',
        'expires_at',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Génère un jeton aléatoire et retourne le couple
     * [jeton brut (à renvoyer au client), hash (à stocker)].
     */
    public static function generate(): array
    {
        $plain = Str::random(40) . '.' . Str::random(16);

        return [$plain, hash('sha256', $plain)];
    }
}