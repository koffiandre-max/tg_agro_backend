<?php

namespace App\Enums;

enum ConditionMeteo: string
{
    case ENSOLEILLE = 'Ensoleillé';
    case NUAGEUX = 'Nuageux';
    case PLUIE = 'Pluie';
    case SEC = 'Sec';

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
