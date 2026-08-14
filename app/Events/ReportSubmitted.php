<?php

namespace App\Events;

use App\Models\Report;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement déclenché quand un technicien soumet un nouveau rapport.
 * Les administrateurs reçoivent une notification par e-mail et in-app.
 */
class ReportSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Report $report,
        public User $technician,
    ) {}
}
