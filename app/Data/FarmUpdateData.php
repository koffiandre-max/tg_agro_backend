<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Date;

class FarmUpdateData extends Data
{
    #[Required, Max(255)]
    public string $name;

    #[Required, Max(255)]
    public string $location;

    #[Nullable, Numeric]
    public ?float $latitude = null;

    #[Nullable, Numeric]
    public ?float $longitude = null;

    #[Required, Numeric]
    public float $total_area_hectares;

    #[Required, Max(255)]
    public string $culture_type;

    #[Required, Max(50)]
    public string $status = 'active';

    #[Nullable, Date]
    public ?string $expected_harvest_date = null;

    #[Nullable, Max(100)]
    public ?string $crop_stage = null;

    #[Nullable, Numeric]
    public ?int $crop_stage_progress = 0;

    #[Nullable, Date]
    public ?string $last_visit_date = null;

    #[Nullable]
    public ?string $notes = null;

    #[Required, Max(255)]
    public string $user_id;
}
