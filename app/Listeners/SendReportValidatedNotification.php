<?php

namespace App\Listeners;

use App\Events\ReportValidated;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Crée une notification in-app pour le client propriétaire du rapport.
 *
 * NOTE : L'envoi d'e-mail est géré directement par le contrôleur appelant
 * (ReportValidationController), afin de conserver le comportement synchrone.
 */
class SendReportValidatedNotification
{
    use InteractsWithQueue;

    public function handle(ReportValidated $event): void
    {
        $report = $event->report;
        $validator = $event->validator;
        $clientUser = $report->client?->user;

        if ($clientUser) {
            Notification::create([
                'utilisateur_id'   => $clientUser->id,
                'notifiable_type'  => Report::class,
                'notifiable_id'    => $report->id,
                'type_notification'=> 'report_validated',
                'message'          => "Le rapport « {$report->title} » a été validé par {$validator->name}",
                'lien'             => route('admin.portail.reports'),
                'lue'              => false,
                'date_creation'    => now(),
            ]);
        }
    }
}
