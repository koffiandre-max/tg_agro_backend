<?php

namespace App\Listeners;

use App\Events\ReportRejected;
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
class SendReportRejectedNotification
{
    use InteractsWithQueue;

    public function handle(ReportRejected $event): void
    {
        $report = $event->report;
        $validator = $event->validator;
        $clientUser = $report->client?->user;

        if ($clientUser) {
            $reason = $event->reason ? " — Motif : {$event->reason}" : '';
            Notification::create([
                'utilisateur_id'   => $clientUser->id,
                'notifiable_type'  => Report::class,
                'notifiable_id'    => $report->id,
                'type_notification'=> 'report_rejected',
                'message'          => "Le rapport « {$report->title} » a été rejeté par {$validator->name}{$reason}",
                'lien'             => route('admin.portail.reports'),
                'lue'              => false,
                'date_creation'    => now(),
            ]);
        }
    }
}
