<?php

namespace App\Enums;

enum EtatHydriqueSol: string
{
    case TRES_SEC = 'Très sec';
    case SEC = 'Sec';
    case HUMIDE_NORMAL = 'Humide (normal)';
    case SATURE_EXCES = 'Saturé (excès)';

    public function label(): string
    {
        return match ($this) {
            self::TRES_SEC => 'Très sec',
            self::SEC => 'Sec',
            self::HUMIDE_NORMAL => 'Humide (normal)',
            self::SATURE_EXCES => 'Saturé (excès)',
        };
    }
}
