<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class VerificationVegetationData extends Data
{
    public function __construct(
        #[Nullable] public ?string $occupation_declare = null,
        #[Nullable] public ?string $occupation_constate = null,
        #[Nullable] public ?bool $occupation_concorde = null,
        #[Nullable] public ?string $cultures_declare = null,
        #[Nullable] public ?string $cultures_constate = null,
        #[Nullable] public ?bool $cultures_concorde = null,
        #[Nullable] public ?string $arbres_declare = null,
        #[Nullable] public ?string $arbres_constate = null,
        #[Nullable] public ?bool $arbres_concorde = null,
        #[Nullable] public ?string $traitements_declare = null,
        #[Nullable] public ?string $traitements_constate = null,
        #[Nullable] public ?bool $traitements_concorde = null,
    ) {}
}
