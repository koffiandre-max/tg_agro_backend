<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\MarketPrice;
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
            'client' => $this->clientDashboard($user),
            'technician' => view('technitian.dashboard'),
            default => view('admin.dashboard'),
        };
    }

    private function clientDashboard($user)
    {
        $farms = collect();

        if ($user->client) {
            $farms = Farm::where('user_id', $user->client->user_id)
                ->orderBy('name')
                ->get();
        }

        $marketPrices = MarketPrice::orderBy('product_name')
            ->limit(6)
            ->get();

        return view('portail.dashboard', compact('farms', 'marketPrices'));
    }
}
