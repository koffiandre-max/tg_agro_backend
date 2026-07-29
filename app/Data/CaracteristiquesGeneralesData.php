<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;

class CaracteristiquesGeneralesData extends Data
{
    public function __construct(
        #[Nullable, Numeric]
        public ?float $surface_totale = null,

        #[Nullable, Numeric]
        public ?float $surface_cultivable = null,

        #[Nullable]
        public ?string $forme_parcelle = null,

        #[Nullable]
        public ?string $exposition_principale = null,

        #[Nullable]
        public ?string $pente_moyenne = null,

        #[Nullable, Numeric]
        public ?int $altitude = null,

        #[Nullable]
        public ?string $topographie = null,
    ) {}
}
