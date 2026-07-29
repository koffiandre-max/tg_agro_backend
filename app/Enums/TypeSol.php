<?php

namespace App\Enums;

enum TypeSol: string
{
    case ARGILEUX = 'Argileux';
    case LIMONEUX = 'Limoneux';
    case SABLEUX = 'Sableux';
    case MIXTE = 'Mixte';
    case INCONNU = 'Inconnu';

    public function label(): string
    {
        return match ($this) {
            self::ARGILEUX => 'Argileux',
            self::LIMONEUX => 'Limoneux',
            self::SABLEUX => 'Sableux',
            self::MIXTE => 'Mixte',
            self::INCONNU => 'Inconnu',
        };
    }
}
