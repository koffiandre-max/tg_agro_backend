<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Date;

class ProchaineVisiteData extends Data
{
    public function __construct(
        #[Nullable, Date]
        public ?string $date_souhaitee = null,

        #[Nullable]
        public ?string $raison = null,
    ) {}
}
