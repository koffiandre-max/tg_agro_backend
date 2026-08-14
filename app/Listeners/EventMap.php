<?php

namespace App\Listeners;

use App\Events\ReportSubmitted;
use App\Events\ReportValidated;
use App\Events\ReportRejected;
use App\Events\RapportVisiteSubmitted;
use Illuminate\Events\Listener;

/**
 * Registre centralisée des mappages événements → listeners.
 *
 * NOTE : Dans Laravel 13, les mappages peuvent être déclarés dans
 * AppServiceProvider::boot() via Event::listen() ou Event::dispatch().
 * Ce fichier serve d'inventaire / documentation.
 */
class EventMap
{
    public const MAP = [
        ReportSubmitted::class  => [
            SendReportSubmittedNotification::class,
        ],
        ReportValidated::class  => [
            SendReportValidatedNotification::class,
        ],
        ReportRejected::class   => [
            SendReportRejectedNotification::class,
        ],
    ];
}
