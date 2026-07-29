<?php

namespace App\Enums;

enum CouleurSol: string
{
    case BRUN = 'Brun';
    case ROUGE = 'Rouge';
    case NOIR = 'Noir';
    case JAUNE = 'Jaune';
    case AUTRE = 'Autre';

    public function label(): string
    {
        return match ($this) {
            self::BRUN => 'Brun',
            self::ROUGE => 'Rouge',
            self::NOIR => 'Noir',
            self::JAUNE => 'Jaune',
            self::AUTRE => 'Autre',
        };
    }
}
