<?php

namespace App\Enums;

enum CultureType: string
{
    case CACAO = 'Cacao';
    case ANACARDE = 'Anacarde (noix de cajou)';
    case HEVEA = 'Hévéa';
    case CAFE = 'Café';
    case PLANTAIN_BANANE = 'Plantain / banane';
    case MANIOC = 'Manioc';
    case MAIS = 'Maïs';
    case MARAICHAGE = 'Maraîchage';
    case RIZ = 'Riz';
    case AUTRE = 'Autre';

    public function label(): string
    {
        return match ($this) {
            self::CACAO => 'Cacao',
            self::ANACARDE => 'Anacarde (noix de cajou)',
            self::HEVEA => 'Hévéa',
            self::CAFE => 'Café',
            self::PLANTAIN_BANANE => 'Plantain / banane',
            self::MANIOC => 'Manioc',
            self::MAIS => 'Maïs',
            self::MARAICHAGE => 'Maraîchage',
            self::RIZ => 'Riz',
            self::AUTRE => 'Autre',
        };
    }
}
