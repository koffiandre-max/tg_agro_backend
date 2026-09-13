<?php

namespace Modules\SendEmail\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\SendEmail\Models\EmailLog;

/**
 * Envoi d'emails avec journalisation en base.
 *
 * L'expédition utilise la configuration mail standard de Laravel de l'app
 * hôte (MAIL_MAILER, MAIL_HOST, ...) — aucune dépendance au module.
 */
class EmailSender
{
    /**
     * Compose et envoie un email, puis journalise le résultat.
     *
     * @param array{to_name?: ?string, subject: string, body: string, is_html?: bool} $data
     */
    public function send(int $businessId, ?int $userId, string $toEmail, array $data): EmailLog
    {
        $log = EmailLog::create([
            'business_id' => $businessId,
            'user_id' => $userId,
            'to_email' => $toEmail,
            'to_name' => $data['to_name'] ?? null,
            'subject' => $this->prefix($data['subject']),
            'body' => $data['body'],
            'is_html' => (bool) ($data['is_html'] ?? true),
            'status' => EmailLog::STATUS_PENDING,
        ]);

        return $this->deliver($log);
    }

    /**
     * Tente à nouveau l'envoi d'un email journalisé.
     */
    public function resend(EmailLog $log): EmailLog
    {
        $log->forceFill([
            'status' => EmailLog::STATUS_PENDING,
            'error' => null,
            'sent_at' => null,
        ])->save();

        return $this->deliver($log);
    }

    /**
     * Effectue l'envoi réel via le mailer Laravel et met à jour le journal.
     */
    protected function deliver(EmailLog $log): EmailLog
    {
        $subject = $log->subject;
        $body = $log->body;

        try {
            Mail::send([], [], function ($message) use ($log, $subject, $body) {
                $fromAddress = config('send_email.from.address');
                $fromName = config('send_email.from.name');

                if ($fromAddress) {
                    $message->from($fromAddress, $fromName ?: null);
                }

                $message->to($log->to_email, $log->to_name ?: null)->subject($subject);

                if ($log->is_html) {
                    $message->setBody($body, 'text/html');
                } else {
                    $message->setBody($body, 'text/plain');
                }
            });

            $log->forceFill([
                'status' => EmailLog::STATUS_SENT,
                'sent_at' => now(),
                'error' => null,
            ])->save();
        } catch (\Throwable $e) {
            $log->forceFill([
                'status' => EmailLog::STATUS_FAILED,
                'error' => mb_substr($e->getMessage(), 0, 4000),
            ])->save();

            // Journalisé mais jamais bloquant pour l'interface.
            Log::warning('[SendEmail] Envoi impossible vers '.$log->to_email.' : '.$e->getMessage());
        }

        return $log;
    }

    /**
     * Ajoute le préfixe configuré au sujet (s'il est défini).
     */
    protected function prefix(string $subject): string
    {
        $prefix = trim((string) config('send_email.subject_prefix', ''));

        return $prefix === '' ? $subject : '['.$prefix.'] '.$subject;
    }

    /**
     * Statistiques d'envoi pour le tableau de bord.
     *
     * @return array{sent: int, failed: int, total: int}
     */
    public function stats(int $businessId): array
    {
        $rows = EmailLog::query()
            ->forBusiness($businessId)
            ->selectRaw("status, count(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'sent' => (int) ($rows[EmailLog::STATUS_SENT] ?? 0),
            'failed' => (int) ($rows[EmailLog::STATUS_FAILED] ?? 0),
            'total' => (int) $rows->sum(),
        ];
    }
}
