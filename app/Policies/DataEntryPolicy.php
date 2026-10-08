<?php

namespace App\Policies;

use App\Models\DataEntry;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DataEntryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $user->hasPermission('data.view');
        }

        return false;
    }

    public function view(User $user, DataEntry $dataEntry): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $dataEntry->technician_id === $user->id
                || $user->hasPermission('data.view');
        }

        if ($user->role === 'client') {
            return $dataEntry->client_id === optional($user->client)->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $user->hasPermission('data.create');
        }

        return false;
    }

    public function update(User $user, DataEntry $dataEntry): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $dataEntry->technician_id === $user->id
                || $user->hasPermission('data.edit');
        }

        return false;
    }

    public function delete(User $user, DataEntry $dataEntry): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('data.delete');
    }
}
