<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Rediriger vers le dashboard approprié selon le rôle
        return match ($user->role) {
            'admin' => view('admin.dashboard'),
            'client' => view('portail.dashboard'),
            'technician' => view('technitian.dashboard'),
            default => view('admin.dashboard'),
        };
    }
}
