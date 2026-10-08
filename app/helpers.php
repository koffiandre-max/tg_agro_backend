<?php

use App\Models\Client;
use Illuminate\Support\Str;

if (! function_exists('is_technician')) {
    function is_technician(): bool
    {
        return auth()->check() && auth()->user()->role === 'technician';
    }
}

if (! function_exists('is_admin')) {
    function is_admin(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }
}

if (! function_exists('is_client')) {
    function is_client(): bool
    {
        return auth()->check() && auth()->user()->role === 'client';
    }
}
