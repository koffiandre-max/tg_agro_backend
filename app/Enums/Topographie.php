<?php

namespace App\Enums;

enum Topographie: string
{
    case PLAINE = 'Plaine';
    case PLATEAU = 'Plateau';
    case COTEAU = 'Coteau';
    case VALLON = 'Vallon';
    case AUTRE = 'Autre';

    public function label(): string
    {
        return match ($this) {
            self::PLAINE => 'Plaine',
            self::PLATEAU => 'Plateau',
            self::COTEAU => 'Coteau',
            self::VALLON => 'Vallon',
            self::AUTRE => 'Autre',
        };
    }
}
