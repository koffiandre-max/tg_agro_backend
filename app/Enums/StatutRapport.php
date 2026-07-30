<?php

namespace App\Enums;

enum StatutRapport: string
{
    case BROUILLON = 'brouillon';
    case EN_ATTENTE_VALIDATION = 'en_attente_validation';
    case VALIDE = 'valide';
    case REJETE = 'rejete';

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
