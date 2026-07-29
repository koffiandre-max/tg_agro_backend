<?php

namespace App\Enums;

enum EauAbreuvement: string
{
    case PROPRE_ACCESSIBLE = 'Propre et accessible';
    case ACCESSIBLE_TROUBLE = 'Accessible mais trouble';
    case INSUFFISANTE = 'Insuffisante';
    case ABSENTE = 'Absente';

    public function label(): string
    {
        return match ($this) {
            self::PROPRE_ACCESSIBLE => 'Propre et accessible',
            self::ACCESSIBLE_TROUBLE => 'Accessible mais trouble',
            self::INSUFFISANTE => 'Insuffisante',
            self::ABSENTE => 'Absente',
        };
    }
}
