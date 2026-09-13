<?php

namespace App\Http\Controllers\Technitian;

use App\Http\Controllers\Controller;
use App\Models\DataEntry;
use App\Models\Mission;
use App\Models\Photo;
use App\Models\Report;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechnitianDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $technician = Technician::where('user_id', $user->id)->first();
        $technicianId = $technician?->id;

        // Récupérer toutes les missions du technicien (null safe pour éviter les erreurs si profil manquant).
        $missionsQuery = $technicianId ? Mission::with(['farm.user', 'farm.clients.user'])->where('technician_id', $technicianId) : Mission::query()->whereRaw('1 = 0');

        // Missions du jour (scheduled_date = aujourd'hui)
        $missionsToday = (clone $missionsQuery)
            ->whereDate('scheduled_date', now()->toDateString())
            ->orderBy('scheduled_date')
            ->get();

        // Missions en attente / en cours (non terminées, non annulées), triées par date
        $activeMissions = (clone $missionsQuery)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('scheduled_date')
            ->get();

        // Prochaines missions (aujourd'hui inclus, non terminées), les 5 plus proches
        $upcomingMissions = (clone $missionsQuery)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereDate('scheduled_date', '>=', now()->toDateString())
            ->orderBy('scheduled_date')
            ->limit(5)
            ->get();

        // Statistiques
        $stats = [
            'missions_today' => $missionsToday->count(),
            'missions_in_progress' => $missionsQuery->clone()
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
            'missions_completed' => $missionsQuery->clone()
                ->where('status', 'completed')
                ->count(),
            'reports' => Report::where('technician_id', $user->id)->count(),
            'photos' => Photo::where('technician_id', $user->id)->count(),
            'photos_pending' => Photo::where('technician_id', $user->id)
                ->where('is_validated', false)
                ->count(),
            'data_entries' => DataEntry::where('technician_id', $user->id)->count(),
        ];

        return view('technitian.dashboard', [
            'user' => $user,
            'technician' => $technician,
            'missionsToday' => $missionsToday,
            'activeMissions' => $activeMissions,
            'upcomingMissions' => $upcomingMissions,
            'stats' => $stats,
        ]);
    }
}
