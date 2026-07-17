<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;

class MissionData extends Data
{
    #[Required]
    public string $title;

    #[Required]
    public int $technician_id;

    #[Required]
    public int $farm_id;

    public ?string $description;
    public ?string $scheduled_date;
    public string $status = 'pending';
}
