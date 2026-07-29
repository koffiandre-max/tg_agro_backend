<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class VerificationGeneralesData extends Data
{
    public function __construct(
        #[Nullable] public ?string $surface_totale_declare = null,
        #[Nullable] public ?string $surface_totale_constate = null,
        #[Nullable] public ?bool $surface_totale_concorde = null,
        #[Nullable] public ?string $surface_cultivable_declare = null,
        #[Nullable] public ?string $surface_cultivable_constate = null,
        #[Nullable] public ?bool $surface_cultivable_concorde = null,
        #[Nullable] public ?string $exposition_declare = null,
        #[Nullable] public ?string $exposition_constate = null,
        #[Nullable] public ?bool $exposition_concorde = null,
        #[Nullable] public ?string $pente_declare = null,
        #[Nullable] public ?string $pente_constate = null,
        #[Nullable] public ?bool $pente_concorde = null,
        #[Nullable] public ?string $topographie_declare = null,
        #[Nullable] public ?string $topographie_constate = null,
        #[Nullable] public ?bool $topographie_concorde = null,
    ) {}
}
