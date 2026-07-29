<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class VisiteElevageData extends Data
{
    public function __construct(
        #[Nullable] public ?array $types_animaux = null,
        #[Nullable] public ?array $signes_cliniques = null,
        #[Nullable] public ?string $observations_sanitaires = null,
        #[Nullable] public ?SoinsAnimauxData $soins_animaux = null,
        #[Nullable] public ?AlimentationEauData $alimentation_eau = null,
        #[Nullable] public ?PerformancesElevageData $performances_elevage = null,
    ) {}
}
