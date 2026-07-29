<?php

namespace App\Enums;

enum FormeParcelle: string
{
    case REGULIERE = 'Régulière';
    case IRREGULIERE = 'Irrégulière';
    case EN_PENTE = 'En pente';
    case AUTRE = 'Autre';

    public function label(): string
    {
        return match ($this) {
            self::REGULIERE => 'Régulière',
            self::IRREGULIERE => 'Irrégulière',
            self::EN_PENTE => 'En pente',
            self::AUTRE => 'Autre',
        };
    }
}
