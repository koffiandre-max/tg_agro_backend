<?php

namespace App\Enums;

enum NiveauAlerte: string
{
    case AUCUNE_ALERTE = 'Aucune alerte';
    case ALERTE_FAIBLE = 'Alerte faible (information)';
    case ALERTE_MODEREE = 'Alerte modérée (surveillance)';
    case ALERTE_URGENTE = 'Alerte urgente (action immédiate requise)';

    public function label(): string
    {
        return match ($this) {
            self::AUCUNE_ALERTE => 'Aucune alerte',
            self::ALERTE_FAIBLE => 'Alerte faible (information)',
            self::ALERTE_MODEREE => 'Alerte modérée (surveillance)',
            self::ALERTE_URGENTE => 'Alerte urgente (action immédiate requise)',
        };
    }
}
