<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Date;

class EntretienIntrantsData extends Data
{
    public function __construct(
        #[Nullable] public ?bool $desherbage_effectue = null,
        #[Nullable] public ?bool $taille_elagage = null,
        #[Nullable] public ?bool $engrais_applique = null,
        #[Nullable] public ?bool $traitement_phytosanitaire = null,
        #[Nullable] public ?bool $mulching_realise = null,
        #[Nullable] public ?bool $compost_apporte = null,
        #[Nullable] public ?string $intrants_utilises = null,
        #[Nullable, Numeric] public ?float $estimation_recolte = null,
        #[Nullable, Date] public ?string $date_estimee_recolte = null,
    ) {}
}
