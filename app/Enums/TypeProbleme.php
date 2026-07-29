<?php

namespace App\Enums;

enum TypeProbleme: string
{
    case POURRITURE_BRUNE = 'Pourriture brune (Phytophthora)';
    case MIRIDES = 'Mirides (capsides)';
    case MOUCHE_FRUITS = 'Mouche des fruits';
    case ANTHRACNOSE = 'Anthracnose';
    case COCHENILLES = 'Cochenilles';
    case CHENILLES_DEFOLIATRICES = 'Chenilles défoliatrices';
    case FUSARIOSE_CHANCRE = 'Fusariose / Chancre';
    case AUTRE_RAVAGEUR = 'Autre ravageur / maladie';

    public function label(): string
    {
        return match ($this) {
            self::POURRITURE_BRUNE => 'Pourriture brune (Phytophthora)',
            self::MIRIDES => 'Mirides (capsides)',
            self::MOUCHE_FRUITS => 'Mouche des fruits',
            self::ANTHRACNOSE => 'Anthracnose',
            self::COCHENILLES => 'Cochenilles',
            self::CHENILLES_DEFOLIATRICES => 'Chenilles défoliatrices',
            self::FUSARIOSE_CHANCRE => 'Fusariose / Chancre',
            self::AUTRE_RAVAGEUR => 'Autre ravageur / maladie',
        };
    }
}
