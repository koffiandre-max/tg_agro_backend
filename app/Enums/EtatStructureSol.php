<?php

namespace App\Enums;

enum EtatStructureSol: string
{
    case BON_MEUBLE = 'Bon-meuble';
    case COMPACT = 'Compact';
    case EROSION_VISIBLE = 'Érosion visible';
    case CROUTE = 'Croûte';

    public function label(): string
    {
        return match ($this) {
            self::BON_MEUBLE => 'Bon-meuble',
            self::COMPACT => 'Compact',
            self::EROSION_VISIBLE => 'Érosion visible',
            self::CROUTE => 'Croûte',
        };
    }
}
