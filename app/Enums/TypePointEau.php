<?php

namespace App\Enums;

enum TypePointEau: string
{
    case RIVIERE = 'Rivière';
    case FORAGE = 'Forage';
    case PUITS = 'Puits';
    case MARE = 'Mare';
    case CANAL = 'Canal';
    case AUTRE = 'Autre';

    public function label(): string
    {
        return match ($this) {
            self::RIVIERE => 'Rivière',
            self::FORAGE => 'Forage',
            self::PUITS => 'Puits',
            self::MARE => 'Mare',
            self::CANAL => 'Canal',
            self::AUTRE => 'Autre',
        };
    }
}
