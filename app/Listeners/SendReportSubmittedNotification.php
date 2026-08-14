<?php

namespace App\Listeners;

use App\Events\ReportSubmitted;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Crée une notification in-app pour chaque administrateur.
 *
 * NOTE : L'envoi d'e-mail est géré directement par le contrôleur appelant
 * (SendmailService), afin de conserver le comportement synchrone existant.
 * Lorsque le système de queue sera pleinement opérationnel en production,
 * les e-mails pourront être déplacés dans un Job ShouldQueue appelé depuis
 * ce listener.
 */
class SendReportSubmittedNotification
{
    use InteractsWithQueue;

    public function handle(ReportSubmitted $event): void
    {
        $report = $event->report;
        $technician = $event->technician;

        $admins = User::where('role', 'admin')
            ->where('is_active', true)
            ->get();

        foreach ($admins as $admin) {
            Notification::create([
                'utilisateur_id'   => $admin->id,
                'notifiable_type'  => Report::class,
                'notifiable_id'    => $report->id,
                'type_notification'=> 'report_submitted',
                'message'          => "Nouveau rapport à valider : « {$report->title} » (par {$technician->name})",
                'lien'             => route('admin.reports.show', $report),
                'lue'              => false,
                'date_creation'    => now(),
            ]);
        }
    }
}

