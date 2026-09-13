<?php

/*
|--------------------------------------------------------------------------
| Module Payment — Configuration
|--------------------------------------------------------------------------
|
| Configuration propre au module Payment. Elle peut être surchargée par
| l'application hôte : publiez ce fichier (ou recopiez-le dans config/)
| puis ajustez les valeurs, SANS modifier le code du module.
|
| Passerelle par défaut : PAYMENT_GATEWAY
|   - "simulate" : passerelle de test locale (aucune clé requise).
|   - "fedapay"  : FedaPay — carte Visa/Mastercard + mobile money (Togo).
|   - "cinetpay" : CinetPay — mobile money + carte.
|
*/

return [

    'default_gateway' => env('PAYMENT_GATEWAY', 'simulate'),

    'currency' => env('PAYMENT_CURRENCY', 'XOF'),

    'gateways' => [

        'simulate' => [
            'driver' => 'simulate',
            'label' => 'Simulation (tests locaux)',
            // Taux d'échec simulé (0-100) pour tester les renouvellements en échec.
            'failure_rate' => (int) env('PAYMENT_SIMULATE_FAILURE_RATE', 0),
        ],

        'fedapay' => [
            'driver' => 'fedapay',
            'label' => 'FedaPay — Carte Visa/Mastercard & Mobile Money',
            'secret_key' => env('FEDAPAY_SECRET_KEY'),
            'environment' => env('FEDAPAY_ENVIRONMENT', 'sandbox'), // sandbox|live
        ],

        'cinetpay' => [
            'driver' => 'cinetpay',
            'label' => 'CinetPay — Mobile Money & Carte',
            'api_key' => env('CINETPAY_API_KEY'),
            'site_id' => env('CINETPAY_SITE_ID'),
            'channels' => env('CINETPAY_CHANNELS', 'ALL'), // ALL|MOBILE_MONEY|CREDIT_CARD
        ],
    ],

    'auto_renew' => [
        'enabled' => (bool) env('PAYMENT_AUTO_RENEW', true),
        // Jours de grâce avant expiration après un échec de renouvellement.
        'grace_days' => (int) env('PAYMENT_GRACE_DAYS', 7),
    ],

];
