<?php

namespace App\Enums;

enum TypeIrrigation: string
{
    case GRAVITAIRE = 'Gravitaire';
    case ASPERSION = 'Aspersion';
    case GOUTTE_A_GOUTTE = 'Goutte-à-goutte';
    case AUCUN = 'Aucun';

    public function label(): string
    {
        return match ($this) {
            self::GRAVITAIRE => 'Gravitaire',
            self::ASPERSION => 'Aspersion',
            self::GOUTTE_A_GOUTTE => 'Goutte-à-goutte',
            self::AUCUN => 'Aucun',
        };
    }
}
