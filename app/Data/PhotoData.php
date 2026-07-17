<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;

class PhotoData extends Data
{
    #[Required]
    public int $farm_id;

    #[Required]
    public int $client_id;

    public ?int $technician_id;

    #[Required]
    public string $photo_path;

    public ?string $thumbnail_path;
    public ?string $caption;
    public ?float $latitude;
    public ?float $longitude;
    public ?string $taken_at;
    public bool $is_visible_to_client = false;
}
