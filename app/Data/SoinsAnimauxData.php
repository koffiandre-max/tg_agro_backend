<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class SoinsAnimauxData extends Data
{
    public function __construct(
        #[Nullable] public ?bool $vaccination_effectuee = null,
        #[Nullable] public ?bool $deparasitage_interne = null,
        #[Nullable] public ?bool $deparasitage_externe = null,
        #[Nullable] public ?bool $traitement_antibiotique = null,
        #[Nullable] public ?bool $soins_plaies = null,
        #[Nullable] public ?bool $consultation_veterinaire = null,
        #[Nullable] public ?string $produits_administres = null,
    ) {}
}
