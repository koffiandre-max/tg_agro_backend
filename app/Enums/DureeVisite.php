<?php

namespace App\Enums;

enum DureeVisite: string
{
    case MOINS_UN_HEURE = '<1h';
    case UN_A_DEUX_HEURES = '1h-2h';
    case DEUX_A_QUATRE_HEURES = '2h-4h';
    case DEMI_JOURNEE = 'Demi-journée';
    case JOURNEE = 'Journée';

    public function label(): string
    {
        return match ($this) {
            self::MOINS_UN_HEURE => '<1h',
            self::UN_A_DEUX_HEURES => '1h-2h',
            self::DEUX_A_QUATRE_HEURES => '2h-4h',
            self::DEMI_JOURNEE => 'Demi-journée',
            self::JOURNEE => 'Journée',
        };
    }
}
