<?php

namespace App\Enums;

enum SubscriptionType: string
{
    case BASIC = 'basic';
    case STANDARD = 'standard';
    case PREMIUM = 'premium';

    public function label(): string
    {
        return match ($this) {
            self::BASIC => 'Basic',
            self::STANDARD => 'Standard',
            self::PREMIUM => 'Premium',
        };
    }
}
