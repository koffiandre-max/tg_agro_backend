<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;

class PerformancesElevageData extends Data
{
    public function __construct(
        #[Nullable, Numeric] public ?float $production_laitiere = null,
        #[Nullable, Numeric] public ?int $production_oeufs = null,
        #[Nullable, Numeric] public ?float $gain_poids = null,
    ) {}
}
