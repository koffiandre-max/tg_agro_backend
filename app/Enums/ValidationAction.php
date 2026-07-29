<?php

namespace App\Enums;

enum ValidationAction: string
{
    case ENVOI = 'Envoi';
    case VALIDATION = 'Validation';
    case REJET = 'Rejet';

    public function label(): string
    {
        return match ($this) {
            self::ENVOI => 'Envoi',
            self::VALIDATION => 'Validation',
            self::REJET => 'Rejet',
        };
    }
}
