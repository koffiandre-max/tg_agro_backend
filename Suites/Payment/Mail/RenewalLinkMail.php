<?php

namespace Modules\Payment\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email de renouvellement automatique : contient le lien de paiement
 * de la nouvelle période (passerelles à checkout client).
 */
class RenewalLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $planName,
        public string $amount,
        public string $link,
        public int $graceDays,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Renouvellement de votre abonnement — paiement requis',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'payment::payment.emails.renewal-link',
        );
    }
}
