<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;

class SolsData extends Data
{
    public function __construct(
        #[Nullable]
        public ?string $type_sol = null,

        #[Nullable]
        public ?string $couleur_sol = null,

        #[Nullable]
        public ?string $profondeur_sol = null,

        #[Nullable]
        public ?bool $presence_cailloux = null,

        #[Nullable]
        public ?string $commentaire_cailloux = null,

        #[Nullable]
        public ?bool $problemes_erosion = null,

        #[Nullable]
        public ?string $commentaire_erosion = null,

        #[Nullable]
        public ?bool $analyse_sol_realisee = null,

        #[Nullable]
        public ?string $commentaire_analyse = null,

        #[Nullable, Numeric]
        public ?float $ph = null,

        #[Nullable]
        public ?string $source_ph = null,
    ) {}
}
