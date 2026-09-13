<?php

namespace Modules\Payment\Gateways;

use Modules\Payment\Models\Payment;

/**
 * Contrat d'une passerelle de paiement (carte bancaire / mobile money).
 *
 * Chaque passerelle est isolée dans sa classe : pour en ajouter une nouvelle,
 * implémentez cette interface et référencez le driver dans
 * config("payment.gateways") — sans toucher au reste du module.
 */
interface PaymentGateway
{
    /**
     * Libellé lisible de la passerelle (affiché à l'utilisateur).
     */
    public function label(): string;

    /**
     * Initialise une session de paiement chez la passerelle.
     *
     * @param Payment $payment Paiement "pending" à encaisser.
     * @return array{redirect_url: ?string, provider_reference: ?string, instructions: ?string}
     *         redirect_url : page de paiement chez la passerelle (ou module).
     */
    public function initiate(Payment $payment): array;

    /**
     * Vérifie l'état réel du paiement auprès de la passerelle
     * (source de vérité : utilisée par le callback et le webhook).
     *
     * @return array{status: string, paid_at: ?\Illuminate\Support\Carbon, provider_reference: ?string, metadata: ?array}
     *         status : succeeded|failed|pending
     */
    public function verify(Payment $payment): array;

    /**
     * Tente un débit hors session (abonnement auto-renewable).
     * Même format de retour que verify().
     */
    public function autoCharge(Payment $payment): array;

    /**
     * La passerelle sait-elle débiter automatiquement (token stocké) ?
     * Sinon, le module envoie un lien de renouvellement par email.
     */
    public function supportsAutoCharge(): bool;
}
