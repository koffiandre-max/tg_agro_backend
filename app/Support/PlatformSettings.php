<?php

namespace App\Support;

/**
 * Paramètres globaux de la plateforme (pas par business).
 * Stockés dans storage/app/platform_settings.json.
 */
class PlatformSettings
{
    protected static ?array $cache = null;

    protected static function path(): string
    {
        return storage_path('app/platform_settings.json');
    }

    protected static function all(): array
    {
        if (static::$cache === null) {
            $path = static::path();
            static::$cache = file_exists($path)
                ? (json_decode(file_get_contents($path), true) ?? [])
                : [];
        }
        return static::$cache;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::all()[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $data = static::all();
        $data[$key] = $value;
        file_put_contents(static::path(), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        static::$cache = $data;
    }
}
