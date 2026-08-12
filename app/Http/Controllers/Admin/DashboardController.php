<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Farm;
use App\Models\MarketPrice;
use App\Models\Mission;
use App\Models\RapportVisite;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Rediriger vers le dashboard approprié selon le rôle
        return match ($user->role) {
            'admin' => $this->adminDashboard(),
            'client' => $this->clientDashboard($user),
            'technician' => view('technitian.dashboard'),
            default => $this->adminDashboard(),
        };
    }

    private function adminDashboard()
    {
        $now = now();
        $startThis = $now->copy()->startOfMonth();
        $startLast = $now->copy()->subMonth()->startOfMonth();
        $endLast = $now->copy()->subMonth()->endOfMonth();

        $kpis = [
            [
                'label' => 'Clients',
                'count' => Client::count(),
                'model' => Client::class,
                'color' => 'indigo',
                'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            ],
            [
                'label' => 'Techniciens',
                'count' => Technician::count(),
                'model' => Technician::class,
                'color' => 'emerald',
                'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            ],
            [
                'label' => 'Exploitations',
                'count' => Farm::count(),
                'model' => Farm::class,
                'color' => 'purple',
                'icon' => 'M9 20l-5.188-1.066A2.25 2.25 0 012.25 17.5v-11.5a2.25 2.25 0 012.25-2.25h15a2.25 2.25 0 012.25 2.25v11.5a2.25 2.25 0 01-2.25 2.25L9 18.75v-8.25z',
            ],
            [
                'label' => 'Rapports en attente',
                'count' => RapportVisite::where('statut', \App\Enums\StatutRapport::EN_ATTENTE_VALIDATION->value)->count(),
                'model' => RapportVisite::class,
                'color' => 'amber',
                'icon' => 'M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z',
                'suffix' => 'par rapport à hier',
            ],
        ];

        foreach ($kpis as &$kpi) {
            $thisMonth = $kpi['model']::whereBetween('created_at', [$startThis, $now])->count();
            $lastMonth = $kpi['model']::whereBetween('created_at', [$startLast, $endLast])->count();
            $kpi['trend'] = $lastMonth === 0 ? ($thisMonth > 0 ? 100 : 0)
                : round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
        }
        unset($kpi);

        return view('admin.dashboard', compact('kpis'));
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
