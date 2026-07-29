<?php

namespace App\Enums;

enum StatutRapport: string
{
    case BROUILLON = 'Brouillon';
    case EN_ATTENTE_VALIDATION = 'En attente de validation';
    case VALIDE = 'Validé';
    case REJETE = 'Rejeté';

    public function label(): string
    {
        return match ($this) {
            self::BROUILLON => 'Brouillon',
            self::EN_ATTENTE_VALIDATION => 'En attente de validation',
            self::VALIDE => 'Validé',
            self::REJETE => 'Rejeté',
        };
    }
}
