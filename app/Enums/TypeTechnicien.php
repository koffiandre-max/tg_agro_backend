<?php

namespace App\Enums;

enum TypeTechnicien: string
{
    case CULTURE = 'culture';
    case ELEVAGE = 'elevage';
    case LES_DEUX = 'les_deux';

    public function label(): string
    {
        return match ($this) {
            self::CULTURE => 'Culture',
            self::ELEVAGE => 'Élevage',
            self::LES_DEUX => 'Élevage et Culture',
        };
    }
}
