<?php

namespace App\Events;

use App\Models\DataEntry;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement déclenché quand un administrateur valide une saisie de données.
 * Le technicien et le client concernés reçoivent une notification.
 */
class DataEntryValidated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public DataEntry $dataEntry,
        public User $validator,
    ) {}
}
