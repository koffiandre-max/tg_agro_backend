<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Technitian\TechnitianDashboardController;
use App\Models\Farm;
use App\Models\MarketPrice;
use App\Models\Message;
use App\Models\Photo;
use App\Models\Report;
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
            'technician' => app(TechnitianDashboardController::class)->index(),
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
        $reportsCount = 0;
        $photosCount = 0;
        $unreadMessages = 0;
        $reports = collect();
        $farmNames = [];
        $farmProgress = [];
        $cultureTypes = [];
        $harvestEstimates = [];
        $livestockBirths = [];
        $livestockTotals = [];

        if ($user->client) {
            $client = $user->client;
            $farms = Farm::where('user_id', $client->user_id)
                ->orderBy('name')
                ->get();

            $reportsCount = Report::where('client_id', $client->id)
                ->where('status', 'validated')
                ->count();

            $photosCount = Photo::where('client_id', $client->id)
                ->where('is_validated', true)
                ->count();

            $unreadMessages = Message::where('user_id', $client->user_id)
                ->where('is_read', false)
                ->count();

            $reports = Report::where('client_id', $client->id)
                ->where('status', 'validated')
                ->with(['farm', 'technician'])
                ->orderByDesc('validated_at')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            $farmNames = $farms->pluck('name')->toArray();
            $farmProgress = $farms->pluck('crop_stage_progress')->toArray();
            $cultureTypes = $farms->groupBy('culture_type')->map->count()->toArray();
            $farmStatuses = $farms->groupBy('status')->map->count()->toArray();

            $harvestEstimates = [];
            $livestockBirths = [];
            $livestockTotals = [];
        }

        $marketPrices = MarketPrice::orderBy('product_name')
            ->limit(6)
            ->get();

        return view('portail.dashboard', compact(
            'farms',
            'marketPrices',
            'reportsCount',
            'photosCount',
            'unreadMessages',
            'reports',
            'farmNames',
            'farmProgress',
            'cultureTypes',
            'farmStatuses',
            'harvestEstimates',
            'livestockBirths',
            'livestockTotals'
        ));
    }
}
