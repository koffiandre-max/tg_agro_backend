<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Email;

class LoginData extends Data
{
    #[Required]
    #[Email]
    public string $email;

    #[Required]
    public string $password;
}
