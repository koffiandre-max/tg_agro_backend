<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;

class DataEntryData extends Data
{
    #[Required]
    public int $farm_id;

    #[Required]
    public int $client_id;

    #[Required]
    public int $technician_id;

    public ?string $crop_stage;
    public ?int $crop_stage_progress;
    public ?string $estimated_harvest_date;
    public ?string $inputs_used;
    public ?string $observations;
    public ?string $weather_conditions;
    public string $status = 'pending';
}
