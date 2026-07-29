<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;

class ObservationsFinalesData extends Data
{
    public function __construct(
        #[Required]
        public string $resume_visite,

        #[Nullable]
        public ?string $niveau_alerte = null,

        #[Nullable]
        public ?string $description_alerte = null,
    ) {}
}
