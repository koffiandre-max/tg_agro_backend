<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('reports.view');
    }

    public function view(User $user, Report $report): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $report->technician_id === $user->id
                || $user->hasPermission('reports.view');
        }

        if ($user->role === 'client') {
            return $report->client_id === optional($user->client)->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $user->hasPermission('reports.create');
        }

        return false;
    }

    public function update(User $user, Report $report): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'technician') {
            return $report->technician_id === $user->id;
        }

        return false;
    }

    public function delete(User $user, Report $report): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('reports.delete');
    }

    public function validate(User $user, Report $report): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->hasPermission('reports.validate');
    }
}
