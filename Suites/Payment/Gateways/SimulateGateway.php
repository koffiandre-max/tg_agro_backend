<?php

namespace Modules\Payment\Gateways;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Modules\Payment\Models\Payment;

/**
 * Passerelle de simulation — pour le développement local (Laragon, etc.)
 * et les tests automatisés. Aucune clé requise.
 *
 * L'initiation redirige vers une page signée du module où l'on peut
 * "payer" (succès) ou "échouer" volontairement. Le débit automatique
 * (abonnement auto) réussit selon le taux d'échec configuré.
 */
class SimulateGateway implements PaymentGateway
{
    public function label(): string
    {
        return (string) config('payment.gateways.simulate.label', 'Simulation (tests)');
    }

    public function initiate(Payment $payment): array
    {
        $payment->forceFill([
            'provider' => 'simulate',
            'provider_reference' => $payment->provider_reference ?: 'SIM-'.strtoupper(Str::random(10)),
        ])->save();

        return [
            'redirect_url' => URL::temporarySignedRoute(
                'admin.payment.simulate.show',
                now()->addHours(2),
                ['payment' => $payment->reference],
            ),
            'provider_reference' => $payment->provider_reference,
            'instructions' => null,
        ];
    }

    public function verify(Payment $payment): array
    {
        // En simulation, l'état en base fait foi (mis à jour par la page de simulation).
        return [
            'status' => $payment->status,
            'paid_at' => $payment->paid_at,
            'provider_reference' => $payment->provider_reference,
            'metadata' => null,
        ];
    }

    public function autoCharge(Payment $payment): array
    {
        $failureRate = (int) config('payment.gateways.simulate.failure_rate', 0);
        $status = ($failureRate > 0 && random_int(1, 100) <= $failureRate)
            ? 'failed'
            : 'succeeded';

        return [
            'status' => $status,
            'paid_at' => $status === 'succeeded' ? now() : null,
            'provider_reference' => 'SIM-'.strtoupper(Str::random(10)),
            'metadata' => null,
        ];
    }

    public function supportsAutoCharge(): bool
    {
        return true;
    }
}
