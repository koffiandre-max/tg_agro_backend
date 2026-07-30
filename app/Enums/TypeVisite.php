<?php

namespace App\Enums;

enum TypeVisite: string
{
    case VISITE_MENSUELLE = 'visite_mensuelle';
    case ANALYSE_SOL = 'analyse_sol';
    case BILAN_RECOLTE = 'bilan_recolte';
    case TRAITEMENT_PHYTOSANITAIRE = 'traitement_phytosanitaire';
    case MISE_EN_PLACE_CULTURE = 'mise_en_place_culture';
    case VISITE_URGENCE = 'visite_urgence';

    public function label(): string
    {
        return match ($this) {
            self::VISITE_MENSUELLE => 'Visite mensuelle de suivi',
            self::ANALYSE_SOL => 'Analyse de sol',
            self::BILAN_RECOLTE => 'Bilan de récolte',
            self::TRAITEMENT_PHYTOSANITAIRE => 'Traitement phytosanitaire',
            self::MISE_EN_PLACE_CULTURE => 'Mise en place culture',
            self::VISITE_URGENCE => 'Visite d\'urgence',
        };
    }
}
