<?php

namespace App\Enums;

enum EtatAlimentation: string
{
    case SUFFISANTE_BONNE_QUALITE = 'Suffisante-bonne qualité';
    case SUFFISANTE_QUALITE_MOYENNE = 'Suffisante-qualité moy.';
    case INSUFFISANTE_QUANTITE = 'Insuffisante en qté';
    case INSUFFISANTE_QUALITE = 'Insuffisante en qualité';

    public function label(): string
    {
        return match ($this) {
            self::SUFFISANTE_BONNE_QUALITE => 'Suffisante-bonne qualité',
            self::SUFFISANTE_QUALITE_MOYENNE => 'Suffisante-qualité moy.',
            self::INSUFFISANTE_QUANTITE => 'Insuffisante en qté',
            self::INSUFFISANTE_QUALITE => 'Insuffisante en qualité',
        };
    }
}
