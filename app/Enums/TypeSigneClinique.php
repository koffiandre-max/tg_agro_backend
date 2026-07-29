<?php

namespace App\Enums;

enum TypeSigneClinique: string
{
    case DIARRHEE = 'Diarrhée';
    case TOUX_DIFFICULTES_RESPIRATOIRES = 'Toux / difficultés respiratoires';
    case BOITERIE = 'Boiterie';
    case AMAIGRISSEMENT = 'Amaigrissement';
    case PLAIES_LESIONS_CUTANEES = 'Plaies / lésions cutanées';
    case AVORTEMENT = 'Avortement';
    case ECOULEMENTS_ANORMAUX = 'Écoulements anormaux';
    case COMPORTEMENT_ANORMAL_APATHIE = 'Comportement anormal / apathie';
    case AUCUN_SIGNE_CLINIQUE = 'Aucun signe clinique';
    case AUTRE_SYMPTOME = 'Autre symptôme';

    public function label(): string
    {
        return match ($this) {
            self::DIARRHEE => 'Diarrhée',
            self::TOUX_DIFFICULTES_RESPIRATOIRES => 'Toux / difficultés respiratoires',
            self::BOITERIE => 'Boiterie',
            self::AMAIGRISSEMENT => 'Amaigrissement',
            self::PLAIES_LESIONS_CUTANEES => 'Plaies / lésions cutanées',
            self::AVORTEMENT => 'Avortement',
            self::ECOULEMENTS_ANORMAUX => 'Écoulements anormaux',
            self::COMPORTEMENT_ANORMAL_APATHIE => 'Comportement anormal / apathie',
            self::AUCUN_SIGNE_CLINIQUE => 'Aucun signe clinique',
            self::AUTRE_SYMPTOME => 'Autre symptôme',
        };
    }
}
