<?php

namespace App\Events;

use App\Models\RapportVisite;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement déclenché quand un technicien soumet un rapport de visite.
 * Les administrateurs reçoivent une notification.
 */
class RapportVisiteSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public RapportVisite $rapport,
        public User $technicien,
    ) {}
}
