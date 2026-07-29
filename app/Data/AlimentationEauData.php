<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;

class AlimentationEauData extends Data
{
    public function __construct(
        #[Nullable] public ?string $etat_alimentation = null,
        #[Nullable] public ?string $eau_abreuvement = null,
        #[Nullable, Numeric] public ?int $etat_batiments = null,
    ) {}
}
