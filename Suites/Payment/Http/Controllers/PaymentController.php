<?php

namespace Modules\Payment\Http\Controllers;

use App\Core\ModuleSDK;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\Payment\Http\Requests\SubscribeRequest;
use Modules\Payment\Models\Payment;
use Modules\Payment\Models\Plan;
use Modules\Payment\Services\GatewayManager;
use Modules\Payment\Services\SubscriptionService;

/**
 * Système d'abonnement : offres, paiement (carte / mobile money),
 * renouvellement automatique, annulation et historique.
 */
class PaymentController extends BaseController
{
    public function __construct(
        protected SubscriptionService $subscriptions,
        protected GatewayManager $gateways,
    ) {
    }

    /**
     * Tableau de bord : abonnement courant, offres, historique.
     */
    public function index(Request $request)
    {
        $businessId = $this->getBusinessId();

        return view('payment::payment.dashboard', [
            'subscription' => $this->subscriptions->currentSubscription($businessId),
            'pendingPayment' => $this->subscriptions->pendingPayment($businessId),
            'plans' => Plan::query()->active()->get(),
            'payments' => Payment::query()
                ->where('business_id', $businessId)
                ->with('plan')
                ->orderByDesc('id')
                ->limit(20)
                ->get(),
            'gateways' => $this->gateways->available(),
            'currentGateway' => (string) config('payment.default_gateway', 'simulate'),
            'autoRenewEnabled' => (bool) config('payment.auto_renew.enabled', true),
        ]);
    }

    /**
     * Souscrit à une offre et lance l'encaissement si elle est payante.
     */
    public function subscribe(SubscribeRequest $request, Plan $plan)
    {
        $businessId = $this->getBusinessId();
        $user = ModuleSDK::user();

        if (! $plan->is_active) {
            return back()->withErrors(['plan' => "Cette offre n'est plus disponible."]);
        }

        $data = $request->validated();
        $data['auto_renew'] = $request->boolean('auto_renew');

        $subscription = $this->subscriptions->subscribe($businessId, $user?->id, $plan, $data);

        // Offre gratuite : activation immédiate, rien à encaisser.
        if ($plan->isFree()) {
            return redirect()
                ->route('admin.payment.index')
                ->with('success', 'Offre gratuite activée. Votre abonnement est actif.');
        }

        // Offre payante : création de la transaction puis redirection vers la passerelle.
        $payment = $this->subscriptions->createPayment($subscription);

        try {
            [, $url, $instructions] = $this->subscriptions->initiateCheckout($payment);
        } catch (\Throwable $e) {
            return back()->withErrors(['gateway' => $e->getMessage()]);
        }

        if ($instructions) {
            session()->flash('payment_instructions', $instructions);
        }

        return $url ? redirect()->away($url) : back();
    }

    /**
     * Page de statut d'un paiement : re-vérifie l'état auprès de la passerelle.
     */
    public function paymentStatus(Request $request, Payment $payment)
    {
        $this->authorizePayment($payment);
        $this->subscriptions->verifyPayment($payment);

        return view('payment::payment.status', ['payment' => $payment->refresh()]);
    }

    /**
     * Retour navigateur depuis la page de paiement de la passerelle.
     */
    public function callback(Request $request, Payment $payment)
    {
        $this->authorizePayment($payment);
        $this->subscriptions->verifyPayment($payment);
        $payment->refresh();

        if ($payment->isSucceeded()) {
            return redirect()
                ->route('admin.payment.index')
                ->with('success', 'Paiement confirmé : votre abonnement est actif. Merci !');
        }

        return redirect()
            ->route('admin.payment.payments.status', $payment)
            ->with('notice', $payment->isPending()
                ? 'Paiement pas encore confirmé. Réessayez dans quelques instants.'
                : null);
    }

    /**
     * Webhook serveur-à-serveur de la passerelle (sans CSRF).
     */
    public function webhook(Request $request, string $provider)
    {
        $payload = $request->json()->all();

        $reference = (string) ($request->input('transaction_id')
            ?: $request->input('reference')
            ?: data_get($payload, 'data.transaction_id')
            ?: '');

        $payment = $reference !== ''
            ? Payment::query()->where('reference', $reference)->first()
            : null;

        if (! $payment) {
            return response()->json(['message' => 'Référence inconnue.'], 404);
        }

        $this->subscriptions->verifyPayment($payment);

        return response()->json(['message' => 'OK']);
    }

    /**
     * Active / désactive le renouvellement automatique.
     */
    public function toggleAutoRenew(Request $request)
    {
        $subscription = $this->subscriptions->currentSubscription($this->getBusinessId());

        if (! $subscription) {
            return back()->withErrors(['subscription' => 'Aucun abonnement à configurer.']);
        }

        $enabled = $request->boolean('auto_renew');

        if ($enabled && ! config('payment.auto_renew.enabled', true)) {
            return back()->withErrors(['auto_renew' => "Le renouvellement automatique n'est pas disponible."]);
        }

        $this->subscriptions->toggleAutoRenew($subscription, $enabled);

        return back()->with(
            'success',
            $enabled
                ? 'Renouvellement automatique activé : votre abonnement se prolongera à l\'échéance.'
                : 'Renouvellement automatique désactivé.'
        );
    }

    /**
     * Annule le renouvellement (l'abonnement reste actif jusqu'à l'échéance).
     */
    public function cancel()
    {
        $subscription = $this->subscriptions->currentSubscription($this->getBusinessId());

        if (! $subscription) {
            return back()->withErrors(['subscription' => 'Aucun abonnement à annuler.']);
        }

        $this->subscriptions->cancel($subscription);

        return back()->with('success', 'Votre abonnement ne sera pas renouvelé. Il reste actif jusqu\'à l\'échéance.');
    }

    /**
     * Réactive le renouvellement automatique.
     */
    public function resume()
    {
        $subscription = $this->subscriptions->currentSubscription($this->getBusinessId());

        if (! $subscription) {
            return back()->withErrors(['subscription' => 'Aucun abonnement à réactiver.']);
        }

        $this->subscriptions->resume($subscription);

        return back()->with('success', 'Renouvellement automatique réactivé.');
    }

    /* ------------------------------------------------------------------
     |  Simulation (développement local — passerelle "simulate")
     * ----------------------------------------------------------------- */

    public function simulateShow(Payment $payment)
    {
        abort_unless(config('payment.default_gateway') === 'simulate', 404);
        $this->authorizePayment($payment);

        return view('payment::payment.simulate', ['payment' => $payment]);
    }

    public function simulateSuccess(Payment $payment)
    {
        abort_unless(config('payment.default_gateway') === 'simulate', 404);
        $this->authorizePayment($payment);

        $this->subscriptions->markPaid($payment, [
            'metadata' => ['simulated' => true, 'card_brand' => 'visa', 'card_last4' => '4242'],
        ]);

        return redirect()
            ->route('admin.payment.index')
            ->with('success', 'Paiement simulé confirmé : votre abonnement est actif.');
    }

    public function simulateFailure(Payment $payment)
    {
        abort_unless(config('payment.default_gateway') === 'simulate', 404);
        $this->authorizePayment($payment);

        $payment->forceFill(['status' => Payment::STATUS_FAILED])->save();

        return redirect()
            ->route('admin.payment.payments.status', $payment)
            ->with('notice', 'Paiement refusé (simulation). Vous pouvez réessayer.');
    }

    /* ------------------------------------------------------------------
     |  Helpers
     * ----------------------------------------------------------------- */

    protected function authorizePayment(Payment $payment): void
    {
        abort_unless((int) $payment->business_id === (int) $this->getBusinessId(), 403);
    }
}

