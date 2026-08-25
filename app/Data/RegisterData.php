<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;

class RegisterData extends Data
{
    public ?string $name = '';

    public ?string $email = '';

    #[Required]
    #[Min(8)]
    public string $password;

    public ?string $password_confirmation = '';

    public ?string $phone = '';

    public ?string $country_of_residence = '';

    public ?string $country_of_origin = '';

    public ?string $city_of_residence = '';
}
