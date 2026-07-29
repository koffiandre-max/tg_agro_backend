<?php

namespace App\Enums;

enum Recommandation: string
{
    case DOSSIER_VALIDE = 'Dossier validé';
    case COMPLEMENTS_REQUIS = 'Compléments requis';
    case VISITE_TERRAIN_NECESSAIRE = 'Visite terrain nécessaire';
    case DOSSIER_REFUSE = 'Dossier refusé';

    public function label(): string
    {
        return match ($this) {
            self::DOSSIER_VALIDE => 'Dossier validé',
            self::COMPLEMENTS_REQUIS => 'Compléments requis',
            self::VISITE_TERRAIN_NECESSAIRE => 'Visite terrain nécessaire',
            self::DOSSIER_REFUSE => 'Dossier refusé',
        };
    }
}
