<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;

class RessourcesEauData extends Data
{
    public function __construct(
        #[Nullable]
        public ?bool $point_eau_proximite = null,

        #[Nullable]
        public ?string $commentaire_point_eau = null,

        #[Nullable]
        public ?string $type_point_eau = null,

        #[Nullable, Numeric]
        public ?int $distance_point_eau = null,

        #[Nullable]
        public ?bool $systeme_irrigation = null,

        #[Nullable]
        public ?string $commentaire_irrigation = null,

        #[Nullable]
        public ?string $type_irrigation = null,

        #[Nullable]
        public ?bool $inondations_saisonnieres = null,

        #[Nullable]
        public ?string $commentaire_inondations = null,

        #[Nullable]
        public ?string $periode_secheresse = null,
    ) {}
}
