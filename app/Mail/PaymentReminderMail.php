<?php

namespace App\Mail;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Subscription $subscription)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rappel de paiement - ' . ($this->subscription->user->name ?? 'Client'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-reminder',
            with: ['subscription' => $this->subscription],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
