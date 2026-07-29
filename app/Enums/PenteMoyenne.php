<?php

namespace App\Enums;

enum PenteMoyenne: string
{
    case PLAT = 'Plat (<3%)';
    case LEGERE = 'Légère (3-10%)';
    case FORTE = 'Forte (>10%)';

    public function label(): string
    {
        return match ($this) {
            self::PLAT => 'Plat (<3%)',
            self::LEGERE => 'Légère (3-10%)',
            self::FORTE => 'Forte (>10%)',
        };
    }
}
