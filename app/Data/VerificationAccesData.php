<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class VerificationAccesData extends Data
{
    public function __construct(
        #[Nullable] public ?string $acces_declare = null,
        #[Nullable] public ?string $acces_constate = null,
        #[Nullable] public ?bool $acces_concorde = null,
        #[Nullable] public ?string $distance_route_declare = null,
        #[Nullable] public ?string $distance_route_constate = null,
        #[Nullable] public ?bool $distance_route_concorde = null,
        #[Nullable] public ?string $cloture_declare = null,
        #[Nullable] public ?string $cloture_constate = null,
        #[Nullable] public ?bool $cloture_concorde = null,
        #[Nullable] public ?string $batiment_declare = null,
        #[Nullable] public ?string $batiment_constate = null,
        #[Nullable] public ?bool $batiment_concorde = null,
        #[Nullable] public ?string $electricite_declare = null,
        #[Nullable] public ?string $electricite_constate = null,
        #[Nullable] public ?bool $electricite_concorde = null,
    ) {}
}
