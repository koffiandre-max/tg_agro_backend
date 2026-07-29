<?php

namespace App\Enums;

enum AnimalType: string
{
    case BOVINS = 'Bovins (vaches, bœufs)';
    case OVINS = 'Ovins (moutons)';
    case CAPRINS = 'Caprins (chèvres)';
    case PORCINS = 'Porcins (porcs)';
    case VOLAILLE = 'Volaille (poulets, dindes, pintades)';
    case LAPINS_CUNICULTURE = 'Lapins / cuniculture';
    case PISCICULTURE = 'Pisciculture';
    case AUTRE_ESPECE = 'Autre espèce';

    public function label(): string
    {
        return match ($this) {
            self::BOVINS => 'Bovins (vaches, bœufs)',
            self::OVINS => 'Ovins (moutons)',
            self::CAPRINS => 'Caprins (chèvres)',
            self::PORCINS => 'Porcins (porcs)',
            self::VOLAILLE => 'Volaille (poulets, dindes, pintades)',
            self::LAPINS_CUNICULTURE => 'Lapins / cuniculture',
            self::PISCICULTURE => 'Pisciculture',
            self::AUTRE_ESPECE => 'Autre espèce',
        };
    }
}
