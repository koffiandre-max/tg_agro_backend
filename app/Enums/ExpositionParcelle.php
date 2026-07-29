<?php

namespace App\Enums;

enum ExpositionParcelle: string
{
    case NORD = 'Nord';
    case SUD = 'Sud';
    case EST = 'Est';
    case OUEST = 'Ouest';
    case VARIABLE = 'Variable';

    public function label(): string
    {
        return match ($this) {
            self::NORD => 'Nord',
            self::SUD => 'Sud',
            self::EST => 'Est',
            self::OUEST => 'Ouest',
            self::VARIABLE => 'Variable',
        };
    }
}
