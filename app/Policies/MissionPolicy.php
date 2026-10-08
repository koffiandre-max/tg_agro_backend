<?php

namespace App\Policies;

use App\Models\Mission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $user->hasPermission('missions.view');
        }

        return false;
    }

    public function view(User $user, Mission $mission): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $mission->technician_id === optional($user->technician)->id
                || $user->hasPermission('missions.view');
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('missions.create');
    }

    public function update(User $user, Mission $mission): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $mission->technician_id === optional($user->technician)->id
                || $user->hasPermission('missions.edit');
        }

        return false;
    }

    public function delete(User $user, Mission $mission): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('missions.delete');
    }

    public function complete(User $user, Mission $mission): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $mission->technician_id === optional($user->technician)->id
                || $user->hasPermission('missions.complete');
        }

        return false;
    }
}
