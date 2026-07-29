<?php

namespace App\Enums;

enum RapportVisiteType: string
{
    case SUIVI_MENSUEL = 'Visite mensuelle de suivi';
    case ANALYSE_SOL = 'Analyse de sol';
    case BILAN_RECOLTE = 'Bilan de récolte';
    case TRAITEMENT_PHYTOSANITAIRE = 'Traitement phytosanitaire';
    case MISE_EN_PLACE_CULTURE = 'Mise en place culture';
    case VISITE_URGENCE = "Visite d urgence";

    public function label(): string
    {
        return match ($this) {
            self::SUIVI_MENSUEL => 'Visite mensuelle de suivi',
            self::ANALYSE_SOL => 'Analyse de sol',
            self::BILAN_RECOLTE => 'Bilan de récolte',
            self::TRAITEMENT_PHYTOSANITAIRE => 'Traitement phytosanitaire',
            self::MISE_EN_PLACE_CULTURE => 'Mise en place culture',
            self::VISITE_URGENCE => "Visite d'urgence",
        };
    }
}
