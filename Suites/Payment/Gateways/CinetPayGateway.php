<?php

namespace Modules\Payment\Gateways;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Modules\Payment\Models\Payment;
use RuntimeException;

/**
 * Passerelle CinetPay — mobile money (Orange, MTN, Moov, Wave) + carte,
 * via la page de paiement hébergée.
 *
 * API v2 : POST /payment (initialisation) puis /payment/check/paystatus.
 * Clés : CINETPAY_API_KEY, CINETPAY_SITE_ID, CINETPAY_CHANNELS.
 */
class CinetPayGateway implements PaymentGateway
{
    protected const CHECKOUT_URL = 'https://api-checkout.cinetpay.com/v2/payment';
    protected const STATUS_URL = 'https://api-checkout.cinetpay.com/v2/payment/check/paystatus';

    public function label(): string
    {
        return (string) config('payment.gateways.cinetpay.label', 'CinetPay');
    }

    public function initiate(Payment $payment): array
    {
        $apiKey = (string) config('payment.gateways.cinetpay.api_key');
        $siteId = (string) config('payment.gateways.cinetpay.site_id');

        if ($apiKey === '' || $siteId === '') {
            throw new RuntimeException(
                'CINETPAY_API_KEY / CINETPAY_SITE_ID manquants : renseignez-les dans le .env pour activer CinetPay.'
            );
        }

        $response = Http::acceptJson()->timeout(30)->post(self::CHECKOUT_URL, array_filter([
            'apikey' => $apiKey,
            'site_id' => $siteId,
            'transaction_id' => $payment->reference,
            'amount' => (int) round((float) $payment->amount),
            'currency' => 'XOF',
            'description' => $payment->description ?: 'Abonnement',
            'channels' => config('payment.gateways.cinetpay.channels', 'ALL'),
            'return_url' => URL::route('admin.payment.payments.callback', ['payment' => $payment->reference]),
            'notify_url' => URL::route('payment.webhook', ['provider' => 'cinetpay']),
            'customer_name' => $payment->payer_name,
            'customer_phone_number' => $payment->payer_phone,
            'metadata' => $payment->reference,
        ]));

        if ($response->json('code') !== '00') {
            throw new RuntimeException(
                'CinetPay : initialisation impossible ('.($response->json('message') ?? 'erreur inconnue').').'
            );
        }

        return [
            'redirect_url' => $response->json('data.payment_url'),
            'provider_reference' => $response->json('data.payment_token'),
            'instructions' => null,
        ];
    }

    public function verify(Payment $payment): array
    {
        $response = Http::acceptJson()->timeout(30)->post(self::STATUS_URL, [
            'apikey' => (string) config('payment.gateways.cinetpay.api_key'),
            'site_id' => (string) config('payment.gateways.cinetpay.site_id'),
            'transaction_id' => $payment->reference,
        ]);

        if ($response->failed()) {
            return ['status' => 'pending', 'paid_at' => null, 'provider_reference' => $payment->provider_reference, 'metadata' => null];
        }

        $status = strtoupper((string) data_get($response->json(), 'data.status'));

        $mapped = match ($status) {
            'ACCEPTED' => 'succeeded',
            'REFUSED', 'CANCELLED', 'EXPIRED' => 'failed',
            default => 'pending',
        };

        return [
            'status' => $mapped,
            'paid_at' => $mapped === 'succeeded' ? now() : null,
            'provider_reference' => $payment->provider_reference,
            'metadata' => ['provider_status' => $status],
        ];
    }

    public function autoCharge(Payment $payment): array
    {
        // CinetPay nécessite la validation du client sur sa page : le module
        // envoie un lien de renouvellement par email dans ce cas.
        return ['status' => 'pending', 'paid_at' => null, 'provider_reference' => null, 'metadata' => null];
    }

    public function supportsAutoCharge(): bool
    {
        return false;
    }
}
