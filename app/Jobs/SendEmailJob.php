<?php

namespace App\Jobs;

use App\Models\EmailAttempt;
use App\Services\SendmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimited;
use Throwable;

class SendEmailJob implements ShouldQueue
{
    use Queueable;

    public $tries = 5;
    public $backoff = [60, 300, 900, 1800, 3600];

    public function __construct(
        public EmailAttempt $attempt
    ) {}

    public function middleware(): array
    {
        return [
            new RateLimited('emails'),
        ];
    }

    public function handle(SendmailService $mailer): void
    {
        $this->attempt->increment('attempts');
        $this->attempt->update([
            'last_attempt_at' => now(),
            'status' => 'pending',
        ]);

        try {
            $mailer->sendView(
                $this->attempt->recipient_email,
                $this->attempt->subject,
                $this->attempt->template,
                $this->attempt->template_data ?? []
            );

            $this->attempt->update([
                'status' => 'sent',
                'sent_at' => now(),
                'last_error' => null,
            ]);
        } catch (Throwable $e) {
            $this->attempt->update(['last_error' => $e->getMessage()]);

            if ($this->attempt->fresh()->attempts >= $this->attempt->max_attempts) {
                $this->attempt->update(['status' => 'abandoned']);
            } else {
                $this->attempt->update(['status' => 'failed']);
                throw $e;
            }
        }
    }
}
