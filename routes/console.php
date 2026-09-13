<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('payment:send-reminders', function () {
    $subscriptions = \App\Models\Subscription::query()
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
            \Illuminate\Support\Facades\Mail::to($subscription->user->email)->send(new \App\Mail\PaymentReminderMail($subscription));

            $subscription->update([
                'next_payment_reminder_at' => now(),
            ]);

            $sent++;
        }
    }

    $this->info("Sent {$sent} payment reminder(s).");
})->purpose('Send payment reminders for upcoming subscription payments');
