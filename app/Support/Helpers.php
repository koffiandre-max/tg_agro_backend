<?php

namespace App\Support;

use App\Models\Client;
use Illuminate\Support\Str;

class Helpers
{
    public static function generateUniqueClientCode(int $maxAttempts = 10): string
    {
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $code = 'CLI-' . strtoupper(Str::random(6));

            if (!Client::where('code', $code)->exists()) {
                return $code;
            }
        }

        throw new \RuntimeException('Impossible de générer un code client unique après ' . $maxAttempts . ' tentatives.');
    }
}


function is_technician(): bool
{
    return auth()->user()->role === 'technician';
}

function is_admin(): bool
{
    return auth()->user()->role === 'admin';
}

function is_client(): bool
{
    return auth()->user()->role === 'client';
}
