<?php

namespace App\Enums;

enum StadePhenologique: string
{
    case GERMINATION = 'Germination';
    case CROISSANCE_VEGETATIVE = 'Croissance végétative';
    case FLORAISON = 'Floraison';
    case NOUAISON = 'Nouaison';
    case FRUCTIFICATION = 'Fructification';
    case MATURATION = 'Maturation';
    case RECOLTE = 'Récolte';
    case POST_RECOLTE = 'Post-récolte';

    public function label(): string
    {
        return match ($this) {
            self::GERMINATION => 'Germination',
            self::CROISSANCE_VEGETATIVE => 'Croissance végétative',
            self::FLORAISON => 'Floraison',
            self::NOUAISON => 'Nouaison',
            self::FRUCTIFICATION => 'Fructification',
            self::MATURATION => 'Maturation',
            self::RECOLTE => 'Récolte',
            self::POST_RECOLTE => 'Post-récolte',
        };
    }
}
