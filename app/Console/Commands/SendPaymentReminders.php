<?php

namespace App\Console\Commands;

use App\Mail\PaymentReminderMail;
use App\Models\Subscription;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('app:send-payment-reminders')]
#[Description('Send payment reminders for upcoming subscription payments')]
class SendPaymentReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subscriptions = Subscription::query()
            ->whereNotNull('next_payment_date')
            ->where('next_payment_date', '<=', now()->addDays(7))
            ->where('next_payment_date', '>=', now())
            ->where(function ($query) {
                $query->whereNull('next_payment_reminder_at')
                    ->orWhere('next_payment_reminder_at', '<', now()->subDays(7));
            })
            ->with('user')
            ->get();

        $sent = 0;

        foreach ($subscriptions as $subscription) {
            if ($subscription->user && $subscription->user->email) {
                Mail::to($subscription->user->email)->send(new PaymentReminderMail($subscription));

                $subscription->update([
                    'next_payment_reminder_at' => now(),
                ]);

                $sent++;
            }
        }

        $this->info("Sent {$sent} payment reminder(s).");
    }
}
