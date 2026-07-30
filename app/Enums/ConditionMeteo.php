<?php

namespace App\Enums;

enum ConditionMeteo: string
{
    case ENSOLEILLE = 'ensoleille';
    case NUAGEUX = 'nuageux';
    case PLUIE = 'pluie';
    case SEC = 'sec';

    public function label(): string
    {
        return match ($this) {
            self::ENSOLEILLE => 'Ensoleillé',
            self::NUAGEUX => 'Nuageux',
            self::PLUIE => 'Pluie',
            self::SEC => 'Sec',
        };
    }
}
