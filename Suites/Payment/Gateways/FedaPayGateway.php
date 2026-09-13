<?php

namespace Modules\Payment\Gateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Modules\Payment\Models\Payment;
use RuntimeException;

/**
 * Passerelle FedaPay (Togo) — carte Visa/Mastercard + mobile money
 * (MTN MoMo, Moov Flooz, Wave, ...) via la page de paiement hébergée.
 *
 * API v1 : création de transaction + token de paiement, puis vérification
 * du statut. Clés : FEDAPAY_SECRET_KEY, FEDAPAY_ENVIRONMENT (sandbox|live).
 */
class FedaPayGateway implements PaymentGateway
{
    public function label(): string
    {
        return (string) config('payment.gateways.fedapay.label', 'FedaPay');
    }

    protected function baseUrl(): string
    {
        $host = config('payment.gateways.fedapay.environment') === 'live'
            ? 'api.fedapay.com'
            : 'sandbox-api.fedapay.com';

        return "https://{$host}/v1";
    }

    protected function client(): PendingRequest
    {
        return Http::acceptJson()
            ->withToken((string) config('payment.gateways.fedapay.secret_key'))
            ->timeout(30);
    }

    public function initiate(Payment $payment): array
    {
        $secret = (string) config('payment.gateways.fedapay.secret_key');

        if ($secret === '') {
            throw new RuntimeException(
                'FEDAPAY_SECRET_KEY manquante : renseignez-la dans le .env pour activer la passerelle FedaPay.'
            );
        }

        $email = data_get($payment->metadata, 'payer_email') ?: 'client@example.com';
        $nameParts = $this->splitName($payment->payer_name ?: 'Client');

        $response = $this->client()->post($this->baseUrl().'/transactions', [
            'description' => $payment->description ?: 'Abonnement',
            'amount' => (int) round((float) $payment->amount),
            'currency' => ['iso' => $this->currencyIso($payment->currency)],
            'callback_url' => URL::route('admin.payment.payments.callback', ['payment' => $payment->reference]),
            'merchant_reference' => $payment->reference,
            'customer' => array_filter([
                'firstname' => $nameParts[0],
                'lastname' => $nameParts[1],
                'email' => $email,
                'phone_number' => $payment->payer_phone
                    ? ['number' => $payment->payer_phone, 'country' => 'TG']
                    : null,
            ]),
        ]);

        if ($response->failed()) {
            throw new RuntimeException('FedaPay : création de transaction impossible ('.$response->status().').');
        }

        $transactionId = data_get($response->json(), 'v1.transaction.id');
        if (! $transactionId) {
            throw new RuntimeException('FedaPay : transaction créée mais identifiant absent.');
        }

        $tokenResponse = $this->client()->post($this->baseUrl()."/transactions/{$transactionId}/token");
        if ($tokenResponse->failed()) {
            throw new RuntimeException('FedaPay : génération du lien de paiement impossible.');
        }

        return [
            'redirect_url' => data_get($tokenResponse->json(), 'v1.url'),
            'provider_reference' => (string) $transactionId,
            'instructions' => null,
        ];
    }

    public function verify(Payment $payment): array
    {
        $transactionId = $payment->provider_reference;
        if (! $transactionId) {
            return ['status' => 'pending', 'paid_at' => null, 'provider_reference' => null, 'metadata' => null];
        }

        $response = $this->client()->get($this->baseUrl()."/transactions/{$transactionId}");
        if ($response->failed()) {
            return ['status' => 'pending', 'paid_at' => null, 'provider_reference' => $transactionId, 'metadata' => null];
        }

        $status = (string) data_get($response->json(), 'v1.transaction.status');

        $mapped = match ($status) {
            'approved', 'transferred' => 'succeeded',
            'declined', 'canceled' => 'failed',
            default => 'pending',
        };

        return [
            'status' => $mapped,
            'paid_at' => $mapped === 'succeeded' ? now() : null,
            'provider_reference' => $transactionId,
            'metadata' => ['provider_status' => $status],
        ];
    }

    public function autoCharge(Payment $payment): array
    {
        // FedaPay nécessite une validation du client sur sa page de paiement :
        // le module enverra alors un lien de renouvellement par email.
        return ['status' => 'pending', 'paid_at' => null, 'provider_reference' => null, 'metadata' => null];
    }

    public function supportsAutoCharge(): bool
    {
        return false;
    }

    protected function currencyIso(string $currency): string
    {
        // FedaPay accepte XOF (FCFA) ; devise par défaut si inconnue.
        return in_array(strtoupper($currency), ['XOF', 'XAF'], true) ? strtoupper($currency) : 'XOF';
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name)) ?: ['Client'];
        $firstname = array_shift($parts) ?: 'Client';

        return [$firstname, implode(' ', $parts) ?: '-'];
    }
}
