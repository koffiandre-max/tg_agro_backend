<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;

class AccesInfrastructuresData extends Data
{
    public function __construct(
        #[Nullable]
        public ?bool $acces_carrossable = null,

        #[Nullable]
        public ?string $commentaire_acces = null,

        #[Nullable, Numeric]
        public ?float $distance_route_principale = null,

        #[Nullable]
        public ?bool $cloture_existante = null,

        #[Nullable]
        public ?string $commentaire_cloture = null,

        #[Nullable]
        public ?bool $batiment_hangar = null,

        #[Nullable]
        public ?string $commentaire_batiment = null,

        #[Nullable]
        public ?bool $electricite_disponible = null,

        #[Nullable]
        public ?string $commentaire_electricite = null,

        #[Nullable]
        public ?bool $reseau_telephonique = null,

        #[Nullable]
        public ?string $commentaire_reseau = null,
    ) {}
}
