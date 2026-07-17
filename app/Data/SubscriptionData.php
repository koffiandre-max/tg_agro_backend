<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;

class SubscriptionData extends Data
{
    #[Required]
    public int $client_id;

    #[Required]
    public string $type;

    #[Required]
    public float $amount;

    public string $currency = 'EUR';

    #[Required]
    public string $start_date;

    #[Required]
    public string $end_date;

    public string $status = 'pending';
    public ?string $payment_method;
    public ?string $payment_reference;
    public bool $auto_renew = true;
}
