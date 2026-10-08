<?php

namespace App\Policies;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PhotoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('photos.view');
    }

    public function view(User $user, Photo $photo): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('photos.view');
    }

    public function create(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $user->hasPermission('photos.upload');
        }

        return false;
    }

    public function update(User $user, Photo $photo): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('photos.upload');
    }

    public function delete(User $user, Photo $photo): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('photos.delete');
    }
}
