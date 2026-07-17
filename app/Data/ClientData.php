<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;

class ClientData extends Data
{
    #[Required]
    public string $name;

    #[Required]
    public string $email;

    #[Required]
    public string $phone;

    public ?string $country_of_residence;
    public ?string $country_of_origin;
    public ?string $city_of_residence;
    public ?string $id_document_type;
    public ?string $id_document_number;
    public string $subscription_type = 'basic';
}
