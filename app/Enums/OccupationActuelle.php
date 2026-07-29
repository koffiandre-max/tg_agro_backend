<?php

namespace App\Enums;

enum OccupationActuelle: string
{
    case CULTURE = 'Culture';
    case JACHERE = 'Jachère';
    case FRICHE = 'Friche';
    case FORET = 'Forêt';
    case PATURAGE = 'Pâturage';
    case AUTRE = 'Autre';

    public function label(): string
    {
        return match ($this) {
            self::CULTURE => 'Culture',
            self::JACHERE => 'Jachère',
            self::FRICHE => 'Friche',
            self::FORET => 'Forêt',
            self::PATURAGE => 'Pâturage',
            self::AUTRE => 'Autre',
        };
    }
}
