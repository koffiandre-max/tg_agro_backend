<?php

namespace App\Policies;

use App\Models\Farm;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FarmPolicy
{
    use HandlesAuthorization;

    /**
     * farm.user_id référence users.id (relation Farm::user() belongsTo User).
     * On accepte aussi le rattachement via la pivot client_farms quand le farm
     * est lié au profil client de l'utilisateur.
     */
    private function belongsToClient(User $user, Farm $farm): bool
    {
        if ((int) $farm->user_id === $user->id) {
            return true;
        }

        $client = $user->client;

        return $client !== null
            && $farm->clients()->where('clients.id', $client->id)->exists();
    }

    public function viewAny(User $user): bool
    {
        if ($user->role === 'admin' || $user->role === 'technician') {
            return true;
        }

        if ($user->role === 'client') {
            return $user->hasPermission('farms.view');
        }

        return false;
    }

    public function view(User $user, Farm $farm): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $farm->assigned_technician_id === optional($user->technician)->id;
        }

        if ($user->role === 'client') {
            return $user->hasPermission('farms.view') && $this->belongsToClient($user, $farm);
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'client') {
            return $user->hasPermission('farms.create');
        }

        return false;
    }

    public function update(User $user, Farm $farm): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $farm->assigned_technician_id === optional($user->technician)->id;
        }

        if ($user->role === 'client') {
            return $user->hasPermission('farms.edit') && $this->belongsToClient($user, $farm);
        }

        return false;
    }

    public function delete(User $user, Farm $farm): bool
    {
        return $user->role === 'admin';
    }

    public function pdf(User $user, Farm $farm): bool
    {
        return $this->view($user, $farm);
    }
}
