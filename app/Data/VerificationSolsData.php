<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class VerificationSolsData extends Data
{
    public function __construct(
        #[Nullable] public ?string $type_sol_declare = null,
        #[Nullable] public ?string $type_sol_constate = null,
        #[Nullable] public ?bool $type_sol_concorde = null,
        #[Nullable] public ?string $profondeur_sol_declare = null,
        #[Nullable] public ?string $profondeur_sol_constate = null,
        #[Nullable] public ?bool $profondeur_sol_concorde = null,
        #[Nullable] public ?string $presence_pierres_declare = null,
        #[Nullable] public ?string $presence_pierres_constate = null,
        #[Nullable] public ?bool $presence_pierres_concorde = null,
        #[Nullable] public ?string $erosion_declare = null,
        #[Nullable] public ?string $erosion_constate = null,
        #[Nullable] public ?bool $erosion_concorde = null,
        #[Nullable] public ?string $ph_sol_declare = null,
        #[Nullable] public ?string $ph_sol_constate = null,
        #[Nullable] public ?bool $ph_sol_concorde = null,
    ) {}
}
