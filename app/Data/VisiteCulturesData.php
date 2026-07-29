<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class VisiteCulturesData extends Data
{
    public function __construct(
        #[Nullable]
        public ?array $types_cultures = null,

        #[Nullable]
        public ?array $etats_vegetatifs = null,

        #[Nullable]
        public ?array $ravageurs_maladies = null,

        #[Nullable]
        public ?string $observations_ravageurs = null,

        #[Nullable]
        public ?SolsIrrigationsData $sols_irrigations = null,

        #[Nullable]
        public ?EntretienIntrantsData $entretien_intrants = null,
    ) {}
}
