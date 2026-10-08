<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $user->hasPermission('clients.view');
        }

        return false;
    }

    public function view(User $user, Client $client): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $user->hasPermission('clients.view');
        }

        if ($user->role === 'client') {
            return $client->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('clients.create');
    }

    public function update(User $user, Client $client): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('clients.edit');
    }

    public function delete(User $user, Client $client): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('clients.delete');
    }
}
