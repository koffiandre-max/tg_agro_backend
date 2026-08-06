<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class TechnicianData extends Data
{
    #[Required]
    public string $name;

    #[Required, Email]
    public string $email;

    #[Nullable, Max(20)]
    public ?string $phone = null;

    #[Required, Min(8)]
    public string $password;

    #[Nullable, Max(20)]
    public ?string $phone_secondary = null;

    #[Required, Max(255)]
    public string $location_base;

    #[Required]
    public string $type_technicien;

    #[Nullable]
    public ?int $max_concurrent_missions = 5;

    public bool $is_available = true;

    #[Nullable]
    public ?string $notes = null;
}
