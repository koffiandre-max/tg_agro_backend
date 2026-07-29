<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Date;

class SynthesesVerificationData extends Data
{
    public function __construct(
        #[Nullable, Numeric]
        public ?int $nombre_criteres = null,

        #[Nullable, Numeric]
        public ?int $conformes = null,

        #[Nullable, Numeric]
        public ?int $ecarts = null,

        #[Nullable, Numeric]
        public ?int $total = null,

        #[Nullable]
        public ?string $ecarts_significatifs = null,

        #[Nullable]
        public ?string $recommandation = null,

        #[Nullable]
        public ?string $verificateur_nom = null,

        #[Nullable]
        public ?string $verificateur_poste = null,

        #[Nullable, Date]
        public ?string $verificateur_date = null,

        #[Nullable]
        public ?string $verificateur_signature = null,
    ) {}
}
