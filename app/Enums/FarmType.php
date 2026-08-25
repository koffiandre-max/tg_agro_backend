<?php

namespace App\Enums;

enum FarmType: string
{
    case CULTURE = 'culture';
    case ELEVAGE = 'elevage';

    public function label(): string
    {
        return match ($this) {
            self::CULTURE => 'Culture',
            self::ELEVAGE => 'Élevage',
        };
    }
}
