<?php

namespace App\Enums;

enum FarmStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case FALLOW = 'fallow';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::INACTIVE => 'Inactif',
            self::FALLOW => 'En jachère',
        };
    }
}
