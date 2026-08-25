<?php

namespace App\Http\Controllers\Portail;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\MarketPrice;
use App\Models\Photo;
use App\Models\Report;
use App\Models\Message;
use App\Models\RapportVisite;
use App\Models\VisiteCulture;
use App\Models\VisiteElevage;
use Illuminate\Http\Request;

class PortailDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $client = $user?->client;

        $farms = collect();
        $reportsCount = 0;
        $photosCount = 0;
        $unreadMessages = 0;

        if ($client) {
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
        }

        $farmNames = $farms->pluck('name')->toArray();
        $farmProgress = $farms->pluck('crop_stage_progress')->toArray();

        $cultureTypes = $farms->groupBy('culture_type')->map->count()->toArray();
        $farmStatuses = $farms->groupBy('status')->map->count()->toArray();

        $harvestEstimates = [];
        $livestockBirths = [];
        $livestockTotals = [];

        if ($client) {
            $reports = RapportVisite::where('client_id', $client->id)
                ->with(['farm', 'visiteCultures', 'visiteElevage'])
                ->whereIn('type_activite', ['culture', 'elevage'])
                ->orderByDesc('date_visite')
                ->get();

            foreach ($reports as $report) {
                if ($report->type_activite === 'culture' && $report->farm) {
                    $culture = $report->visiteCultures->first();
                    if ($culture && $culture->estimation_recolte_kg > 0) {
                        $farmName = $report->farm->name;
                        if (!isset($harvestEstimates[$farmName])) {
                            $harvestEstimates[$farmName] = 0;
                        }
                        $harvestEstimates[$farmName] += $culture->estimation_recolte_kg;
                    }
                }

                if ($report->type_activite === 'elevage' && $report->farm) {
                    $elevage = $report->visiteElevage->first();
                    if ($elevage) {
                        $farmName = $report->farm->name;
                        if (!isset($livestockBirths[$farmName])) {
                            $livestockBirths[$farmName] = 0;
                        }
                        if (!isset($livestockTotals[$farmName])) {
                            $livestockTotals[$farmName] = 0;
                        }
                        $livestockBirths[$farmName] += $elevage->naissances_depuis_derniere_visite ?? 0;
                        $livestockTotals[$farmName] += $elevage->effectif_total ?? 0;
                    }
                }
            }
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

    public function farms()
    {
        $user = auth()->user();
        $client = $user?->client;


        $farms = collect();

        if ($client) {
            $farms = Farm::where('user_id', $client->user_id)
                ->orderBy('name')
                ->get();
        }

        return view('portail.farms', compact('farms'));
    }

    public function farmShow(Farm $farm)
    {
        $user = request()->user();
        $client = $user?->client;

        if (! $client) {
            return redirect()->route('admin.portail.index')
                ->with('error', 'Aucune fiche client trouvée pour votre compte.');
        }

        $farm->load([
            'photos' => fn($q) => $q->where('is_validated', true)->latest(),
            'assignedTechnician.user',
            'user'
        ]);

        return view('portail.farms.show', compact('farm'));
    }
}
