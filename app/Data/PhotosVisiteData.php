<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;

class PhotosVisiteData extends Data
{
    public function __construct(
        #[Nullable] public ?array $photos = null,
    ) {}
}
