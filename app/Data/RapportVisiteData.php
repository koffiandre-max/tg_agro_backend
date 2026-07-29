<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;

class RapportVisiteData extends Data
{
    public function __construct(
        #[Required, Exists('users', 'id')]
        public int $technicien_id,

        #[Required, Date]
        public string $date_visite,

        #[Required, Exists('clients', 'id')]
        public int $client_id,

        #[Nullable, Exists('farms', 'id')]
        public ?int $farm_id = null,

        #[Required]
        public string $localisation_parcelle,

        #[Required]
        public string $type_visite,

        #[Nullable]
        public ?string $conditions_meteo = null,

        #[Nullable, Numeric]
        public ?float $superficie_visitee = null,

        #[Nullable]
        public ?string $duree_visite = null,

        #[Nullable]
        public ?string $latitude = null,

        #[Nullable]
        public ?string $longitude = null,

        #[Required]
        public string $statut = 'Brouillon',

        // Sous-données imbriquées (optionnelles)
        #[Nullable]
        public ?VisiteCulturesData $cultures = null,

        #[Nullable]
        public ?VisiteElevageData $elevage = null,

        #[Nullable]
        public ?PhotosVisiteData $photos = null,

        #[Nullable]
        public ?ObservationsFinalesData $observations_finales = null,

        #[Nullable]
        public ?ProchaineVisiteData $prochaine_visite = null,

        #[Nullable]
        public ?NotesRapportData $notes = null,
    ) {}
}
