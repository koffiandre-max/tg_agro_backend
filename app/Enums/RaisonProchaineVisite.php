<?php

namespace App\Enums;

enum RaisonProchaineVisite: string
{
    case SUIVI_MENSUEL = 'Suivi mensuel';
    case TRAITEMENT_CONTROLER = 'Traitement à contrôler';
    case RECOLTE_SURVEILLER = 'Récolte à surveiller';
    case URGENCE_SANITAIRE = 'Urgence sanitaire';

    public function label(): string
    {
        return match ($this) {
            self::SUIVI_MENSUEL => 'Suivi mensuel',
            self::TRAITEMENT_CONTROLER => 'Traitement à contrôler',
            self::RECOLTE_SURVEILLER => 'Récolte à surveiller',
            self::URGENCE_SANITAIRE => 'Urgence sanitaire',
        };
    }
}
