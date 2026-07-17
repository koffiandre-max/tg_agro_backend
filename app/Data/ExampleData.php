<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;

class ExampleData extends Data
{
    #[Required]
    public string $name;

    #[Required]
    public string $email;

    public ?string $phone;
}
