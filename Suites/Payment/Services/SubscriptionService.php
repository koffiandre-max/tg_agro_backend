<?php

namespace Modules\Payment\Services;

use App\Core\ModuleSDK;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Payment\Mail\RenewalLinkMail;
use Modules\Payment\Models\Payment;
use Modules\Payment\Models\Plan;
use Modules\Payment\Models\Subscription;

/**
 * Logique métier du système d'abonnement :
 * souscription, encaissement (carte / mobile money), renouvellement
 * automatique, annulation et expiration.
 */
class SubscriptionService
{
    public function __construct(protected GatewayManager $gateways)
    {
    }

    /* ------------------------------------------------------------------
     |  Lecture
     * ----------------------------------------------------------------- */

    /**
     * Abonnement courant du tenant (actif, en attente ou en retard).
     */
    public function currentSubscription(int $businessId): ?Subscription
    {
        return Subscription::query()
            ->where('business_id', $businessId)
            ->whereIn('status', [
                Subscription::STATUS_ACTIVE,
                Subscription::STATUS_PENDING,
                Subscription::STATUS_PAST_DUE,
            ])
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Paiement en attente d'un tenant (checkout non finalisé).
     */
    public function pendingPayment(int $businessId): ?Payment
    {
        return Payment::query()
            ->where('business_id', $businessId)
            ->where('status', Payment::STATUS_PENDING)
            ->orderByDesc('id')
            ->first();
    }

    /* ------------------------------------------------------------------
     |  Souscription & encaissement
     * ----------------------------------------------------------------- */

    /**
     * Abonne le tenant à une offre. Offre gratuite → activation immédiate ;
     * offre payante → statut "pending" en attendant le paiement.
     *
     * @param array{method?: string, auto_renew?: bool, payer_name?: string|null, phone?: string|null} $data
     */
    public function subscribe(int $businessId, int $userId, Plan $plan, array $data = []): Subscription
    {
        return DB::transaction(function () use ($businessId, $userId, $plan, $data) {
            // Un seul abonnement courant par tenant : les précédents sont clôturés.
            Subscription::query()
                ->where('business_id', $businessId)
                ->whereIn('status', [
                    Subscription::STATUS_PENDING,
                    Subscription::STATUS_ACTIVE,
                    Subscription::STATUS_PAST_DUE,
                ])
                ->update([
                    'status' => Subscription::STATUS_CANCELED,
                    'canceled_at' => now(),
                ]);

            $method = $data['method'] ?? null;

            $subscription = Subscription::create([
                'business_id' => $businessId,
                'user_id' => $userId,
                'plan_id' => $plan->id,
                'status' => Subscription::STATUS_PENDING,
                'period_months' => (int) $plan->period_months,
                'amount' => $plan->price,
                'currency' => $plan->currency,
                'auto_renew' => (bool) ($data['auto_renew'] ?? false),
                'payment_method' => $method,
                'payment_details' => array_filter([
                    'phone' => $method === Payment::METHOD_MOBILE_MONEY ? ($data['phone'] ?? null) : null,
                    'payer_name' => $data['payer_name'] ?? null,
                ]),
            ]);

            if ($plan->isFree()) {
                $this->extend($subscription, null);
            }

            return $subscription;
        });
    }

    /**
     * Crée la transaction "pending" correspondant à un abonnement.
     */
    public function createPayment(Subscription $subscription, bool $isRenewal = false, ?string $method = null): Payment
    {
        return Payment::create([
            'reference' => ModuleSDK::generateReference('PAY', $subscription->business_id),
            'business_id' => $subscription->business_id,
            'user_id' => $subscription->user_id,
            'subscription_id' => $subscription->id,
            'plan_id' => $subscription->plan_id,
            'amount' => $subscription->amount,
            'currency' => $subscription->currency,
            'description' => trim('Abonnement '.($subscription->plan?->name ?: '').($isRenewal ? ' — renouvellement' : '')) ?: 'Abonnement',
            'method' => $method ?: $subscription->payment_method,
            'provider' => (string) config('payment.default_gateway', 'simulate'),
            'status' => Payment::STATUS_PENDING,
            'payer_name' => data_get($subscription->payment_details, 'payer_name'),
            'payer_phone' => data_get($subscription->payment_details, 'phone'),
            'is_renewal' => $isRenewal,
            'metadata' => [
                'payer_email' => ModuleSDK::user()?->email,
            ],
        ]);
    }

    /**
     * Initialise la session de paiement chez la passerelle.
     *
     * @return array{0: Payment, 1: ?string, 2: ?string} [payment, redirect_url, instructions]
     */
    public function initiateCheckout(Payment $payment): array
    {
        $gateway = $this->gateways->driver($payment->provider);
        $result = $gateway->initiate($payment);

        $payment->forceFill([
            'provider_reference' => $result['provider_reference'] ?? $payment->provider_reference,
            'metadata' => array_merge($payment->metadata ?? [], [
                'instructions' => $result['instructions'] ?? null,
                'checkout_url' => $result['redirect_url'] ?? null,
            ]),
        ])->save();

        return [$payment, $result['redirect_url'] ?? null, $result['instructions'] ?? null];
    }

    /**
     * Vérifie le paiement auprès de la passerelle et applique le résultat.
     */
    public function verifyPayment(Payment $payment): Payment
    {
        if ($payment->isSucceeded()) {
            return $payment;
        }

        $gateway = $this->gateways->driver($payment->provider);
        $result = $gateway->verify($payment);

        if (($result['status'] ?? null) === 'succeeded') {
            return $this->markPaid($payment, $result);
        }

        if (($result['status'] ?? null) === 'failed' && $payment->isPending()) {
            $payment->forceFill([
                'status' => Payment::STATUS_FAILED,
                'metadata' => array_merge($payment->metadata ?? [], $result['metadata'] ?? []),
            ])->save();
        }

        return $payment->refresh();
    }

    /**
     * Marque un paiement comme payé et active / étend l'abonnement.
     *
     * @param array{paid_at?: \Illuminate\Support\Carbon|null, provider_reference?: ?string, metadata?: ?array} $result
     */
    public function markPaid(Payment $payment, array $result = []): Payment
    {
        return DB::transaction(function () use ($payment, $result) {
            $payment->forceFill([
                'status' => Payment::STATUS_SUCCEEDED,
                'paid_at' => $result['paid_at'] ?? now(),
                'provider_reference' => $result['provider_reference'] ?? $payment->provider_reference,
                'metadata' => array_merge($payment->metadata ?? [], $result['metadata'] ?? []),
            ])->save();

            $subscription = $payment->subscription()->first();

            if ($subscription) {
                $this->extend($subscription, $payment);
            }

            return $payment;
        });
    }

    /* ------------------------------------------------------------------
     |  Activation / extension de période
     * ----------------------------------------------------------------- */

    /**
     * Active / prolonge l'abonnement d'une période complète.
     */
    protected function extend(Subscription $subscription, ?Payment $payment): void
    {
        $months = max(1, (int) $subscription->period_months);

        // Si l'abonnement est encore valide, la nouvelle période s'empile.
        $base = ($subscription->ends_at && $subscription->ends_at->isFuture())
            ? $subscription->ends_at
            : now();

        $newEnds = $base->copy()->addMonths($months);

        $subscription->forceFill([
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => $subscription->starts_at ?: now()->toDateString(),
            'ends_at' => $newEnds->toDateString(),
            'next_payment_at' => $newEnds->toDateString(),
            'amount' => $payment?->amount ?: $subscription->amount,
            'currency' => $payment?->currency ?: $subscription->currency,
            'payment_method' => $payment?->method ?: $subscription->payment_method,
            'payment_details' => array_merge($subscription->payment_details ?? [], array_filter([
                'provider' => $payment?->provider,
                'last_reference' => $payment?->reference,
            ])),
            'canceled_at' => null,
        ])->save();
    }

    /* ------------------------------------------------------------------
     |  Gestion de l'abonnement
     * ----------------------------------------------------------------- */

    public function toggleAutoRenew(Subscription $subscription, bool $enabled): Subscription
    {
        $subscription->forceFill([
            'auto_renew' => $enabled,
            'canceled_at' => $enabled ? null : $subscription->canceled_at,
        ])->save();

        return $subscription;
    }

    /**
     * Annule le renouvellement : l'abonnement reste actif jusqu'à ends_at.
     */
    public function cancel(Subscription $subscription): Subscription
    {
        $subscription->forceFill([
            'auto_renew' => false,
            'canceled_at' => now(),
        ])->save();

        return $subscription;
    }

    /**
     * Réactive le renouvellement automatique avant la fin de période.
     */
    public function resume(Subscription $subscription): Subscription
    {
        $subscription->forceFill([
            'auto_renew' => true,
            'canceled_at' => null,
        ])->save();

        return $subscription;
    }

    /* ------------------------------------------------------------------
     |  Renouvellement automatique & expiration
     * ----------------------------------------------------------------- */

    /**
     * Renouvelle tous les abonnements arrivés à échéance (auto_renew).
     *
     * @return array{renewed: int, links_sent: int, failed: int}
     */
    public function processAutoRenewals(): array
    {
        $stats = ['renewed' => 0, 'links_sent' => 0, 'failed' => 0];

        if (! config('payment.auto_renew.enabled', true)) {
            return $stats;
        }

        $due = Subscription::query()
            ->with('plan')
            ->where('auto_renew', true)
            ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_PAST_DUE])
            ->whereNotNull('next_payment_at')
            ->whereDate('next_payment_at', '<=', now()->toDateString())
            ->where('amount', '>', 0)
            ->get();

        foreach ($due as $subscription) {
            $payment = $this->createPayment($subscription, isRenewal: true);
            $gateway = $this->gateways->driver($payment->provider);

            if ($gateway->supportsAutoCharge()) {
                $result = $gateway->autoCharge($payment);

                if (($result['status'] ?? null) === 'succeeded') {
                    $this->markPaid($payment, $result);
                    $stats['renewed']++;
                } else {
                    $payment->forceFill(['status' => Payment::STATUS_FAILED])->save();
                    $subscription->forceFill(['status' => Subscription::STATUS_PAST_DUE])->save();
                    $stats['failed']++;
                }
            } else {
                // Passerelle à checkout : lien de renouvellement envoyé par email.
                [, $url] = $this->initiateCheckout($payment);
                $this->sendRenewalLink($subscription, $payment, $url);
                $stats['links_sent']++;
            }
        }

        return $stats;
    }

    /**
     * Expire les abonnements terminés (sans auto-renew) ou impayés
     * au-delà de la période de grâce.
     */
    public function expireOverdue(): int
    {
        $grace = max(0, (int) config('payment.auto_renew.grace_days', 7));
        $today = now()->toDateString();
        $graceLimit = now()->subDays($grace)->toDateString();

        // Période terminée, sans renouvellement automatique.
        $expiredA = Subscription::query()
            ->where('auto_renew', false)
            ->whereIn('status', [Subscription::STATUS_ACTIVE, Subscription::STATUS_PENDING])
            ->whereDate('ends_at', '<', $today)
            ->update(['status' => Subscription::STATUS_EXPIRED]);

        // Auto-renew en échec depuis plus que la période de grâce.
        $expiredB = Subscription::query()
            ->where('status', Subscription::STATUS_PAST_DUE)
            ->whereDate('next_payment_at', '<', $graceLimit)
            ->update(['status' => Subscription::STATUS_EXPIRED]);

        return $expiredA + $expiredB;
    }

    /**
     * Envoie le lien de renouvellement au titulaire de l'abonnement.
     */
    protected function sendRenewalLink(Subscription $subscription, Payment $payment, ?string $url): void
    {
        if (! $url) {
            return;
        }

        $email = $subscription->user_id
            ? DB::table('users')->where('id', $subscription->user_id)->value('email')
            : null;

        if (! $email) {
            return;
        }

        try {
            Mail::to($email)->send(new RenewalLinkMail(
                planName: $subscription->plan?->name ?: 'Abonnement',
                amount: $payment->formattedAmount(),
                link: $url,
                graceDays: (int) config('payment.auto_renew.grace_days', 7),
            ));
        } catch (\Throwable $e) {
            // L'envoi du mail ne doit jamais bloquer le renouvellement.
            Log::warning('[Payment] Envoi du lien de renouvellement impossible : '.$e->getMessage());
        }
    }
}
