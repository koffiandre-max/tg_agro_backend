<?php

namespace App\Enums;

enum DureeVisite: string
{
    case MOINS_UN_HEURE = 'moins_1h';
    case UN_A_DEUX_HEURES = '1h_2h';
    case DEUX_A_QUATRE_HEURES = '2h_4h';
    case DEMI_JOURNEE = 'demi_journee';
    case JOURNEE = 'journee';

    public function label(): string
    {
        return match ($this) {
            self::MOINS_UN_HEURE => '< 1h',
            self::UN_A_DEUX_HEURES => '1h - 2h',
            self::DEUX_A_QUATRE_HEURES => '2h - 4h',
            self::DEMI_JOURNEE => 'Demi-journée',
            self::JOURNEE => 'Journée',
        };
    }
}
