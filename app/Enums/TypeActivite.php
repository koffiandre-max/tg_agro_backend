<?php

namespace App\Enums;

enum TypeActivite: string
{
    case CULTURE = 'culture';
    case ELEVAGE = 'elevage';
    case AUTRE = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::CULTURE => 'Culture',
            self::ELEVAGE => 'Élevage',
            self::AUTRE => 'Autre',
        };
    }
}
