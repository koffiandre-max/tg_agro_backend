<?php

namespace Modules\Payment\Console;

use Illuminate\Console\Command;
use Modules\Payment\Services\SubscriptionService;

/**
 * Renouvelle les abonnements arrivés à échéance et expire les retardataires.
 *
 * À planifier quotidiennement (scheduler de l'app hôte) :
 *   $schedule->command('payment:process-auto-renewals')->daily();
 */
class ProcessAutoRenewals extends Command
{
    protected $signature = 'payment:process-auto-renewals';

    protected $description = 'Renouvelle les abonnements automatiques arrivés à échéance et expire les abonnements terminés';

    public function handle(SubscriptionService $service): int
    {
        $stats = $service->processAutoRenewals();

        $this->info("Renouvellements auto débités : {$stats['renewed']}");
        $this->info("Liens de renouvellement envoyés : {$stats['links_sent']}");
        $this->info("Renouvellements en échec : {$stats['failed']}");

        $expired = $service->expireOverdue();
        $this->info("Abonnements expirés : {$expired}");

        return self::SUCCESS;
    }
}
