<?php

namespace App\Jobs;

use App\Services\SendmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job pour envoyer un e-mail de notification de manière asynchrone.
 *
 * Utilise SendmailService pour l'envoi réel.
 * Configure avec QUEUE_CONNECTION=database ou redis pour la production.
 */
class SendNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre de tentatives d'envoi en cas d'échec.
     */
    public int $tries = 3;

    /**
     * Délai entre les tentatives (en secondes).
     */
    public int $backoff = 10;

    /**
     * @param string      $to       Adresse e-mail du destinataire
     * @param string      $subject  Sujet de l'e-mail
     * @param string|null $view     Vue Blade pour le contenu HTML (optionnel)
     * @param string|null $content  Contenu texte brut (optionnel, utilisé si $view est null)
     * @param array       $data     Variables de la vue
     * @param string|null $from     Adresse expéditeur (optionnel)
     */
    public function __construct(
        protected string $to,
        protected string $subject,
        protected ?string $view = null,
        protected ?string $content = null,
        protected array $data = [],
        protected ?string $from = null,
    ) {}

    /**
     * Exécute l'envoi de l'e-mail.
     */
    public function handle(SendmailService $mailer): void
    {
        if ($this->content !== null) {
            $mailer->send($this->to, $this->subject, $this->content, $this->from);
        } elseif ($this->view !== null) {
            $mailer->sendView($this->to, $this->subject, $this->view, $this->data, $this->from);
        }
    }
}
