<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\MarketPrice;
use App\Support\DashboardFeatures;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirige vers le dashboard approprié selon le rôle
        return match ($user->role) {
            'admin' => $this->adminDashboard(),
            'client' => $this->clientDashboard($user),
            'technician' => view('technitian.dashboard'),
            default => $this->adminDashboard(),
        };
    }

    private function adminDashboard()
    {
        // Statistiques activées + ordre définis depuis /settings (position en base)
        $features = DashboardFeatures::withStats();

        $colorNames = ['indigo', 'emerald', 'purple', 'amber', 'sky', 'rose', 'teal', 'orange', 'lime', 'cyan', 'violet', 'yellow'];

        $kpis = [];
        $index = 0;
        foreach ($features as $feature) {
            if (! $feature['enabled']) {
                continue;
            }

            $kpis[] = [
                'key'     => $feature['key'],
                'label'   => $feature['label'],
                'icon'    => $feature['icon'],
                'count'   => $feature['count'],
                'trend'   => $feature['trend'],
                'color'   => $colorNames[$index % count($colorNames)],
            ];

            $index++;
        }

        $monthLabels = DashboardFeatures::monthLabels();
        $chartColors = DashboardFeatures::palette(max(count($kpis), 1));

        return view('admin.dashboard', compact('kpis', 'monthLabels', 'chartColors'));
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
