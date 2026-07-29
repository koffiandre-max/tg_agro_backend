<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;

class SolsIrrigationsData extends Data
{
    public function __construct(
        #[Nullable] public ?string $etat_hydrique_sol = null,
        #[Nullable] public ?bool $irrigation_place = null,
        #[Nullable] public ?string $etat_structure_sol = null,
        #[Nullable, Numeric] public ?float $ph_sol_mesure = null,
    ) {}
}
