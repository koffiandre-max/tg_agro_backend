<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Date;

class RemarquesClientData extends Data
{
    public function __construct(
        #[Nullable]
        public ?string $observations_libres = null,

        #[Nullable, Date]
        public ?string $signature_date = null,

        #[Nullable]
        public ?string $signature = null,
    ) {}
}
