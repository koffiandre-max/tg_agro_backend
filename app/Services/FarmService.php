<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Farm;
use Illuminate\Support\Collection;

class FarmService
{
    public function getFarmsAndClients(?int $technicianId = null): Collection
    {
        $query = Farm::with(['user', 'clients.user'])->orderBy('name');

        if ($technicianId) {
            $query->where('assigned_technician_id', $technicianId);
        }

        $farms = $query->get();

        $clientIds = $farms->flatMap(fn ($farm) => $farm->clients->pluck('id'))->unique();

        $clients = Client::whereIn('id', $clientIds)
            ->join('users', 'users.id', '=', 'clients.user_id')
            ->orderBy('clients.code')
            ->select('clients.*')
            ->with('user')
            ->get();

        if ($clients->isEmpty()) {
            $clients = Client::join('users', 'users.id', '=', 'clients.user_id')
                ->orderBy('clients.code')
                ->select('clients.*')
                ->with('user')
                ->get();
        }

        return collect(compact('farms', 'clients'));
    }
}
