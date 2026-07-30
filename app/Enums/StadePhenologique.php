<?php

namespace App\Enums;

enum StadePhenologique: string
{
    case GERMINATION = 'germination';
    case CROISSANCE_VEGETATIVE = 'croissance_vegetative';
    case FLORAISON = 'floraison';
    case NOUAISON = 'nouaison';
    case FRUCTIFICATION = 'fructification';
    case MATURATION = 'maturation';
    case RECOLTE = 'recolte';
    case POST_RECOLTE = 'post_recolte';

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
