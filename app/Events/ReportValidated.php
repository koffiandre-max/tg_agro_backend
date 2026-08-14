<?php

namespace App\Events;

use App\Models\Report;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement déclenché quand un administrateur valide un rapport.
 * Le client reçoit une notification par e-mail et in-app.
 */
class ReportValidated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Report $report,
        public User $validator,
    ) {}
}
