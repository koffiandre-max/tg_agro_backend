<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class VerificationEauData extends Data
{
    public function __construct(
        #[Nullable] public ?string $point_eau_declare = null,
        #[Nullable] public ?string $point_eau_constate = null,
        #[Nullable] public ?bool $point_eau_concorde = null,
        #[Nullable] public ?string $type_point_eau_declare = null,
        #[Nullable] public ?string $type_point_eau_constate = null,
        #[Nullable] public ?bool $type_point_eau_concorde = null,
        #[Nullable] public ?string $distance_point_eau_declare = null,
        #[Nullable] public ?string $distance_point_eau_constate = null,
        #[Nullable] public ?bool $distance_point_eau_concorde = null,
        #[Nullable] public ?string $irrigation_declare = null,
        #[Nullable] public ?string $irrigation_constate = null,
        #[Nullable] public ?bool $irrigation_concorde = null,
        #[Nullable] public ?string $inondations_declare = null,
        #[Nullable] public ?string $inondations_constate = null,
        #[Nullable] public ?bool $inondations_concorde = null,
    ) {}
}
