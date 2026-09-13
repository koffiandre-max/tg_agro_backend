<?php

namespace App\Http\Controllers\Api\V1\Technician;

use App\Http\Controllers\Api\ApiController;
use App\Models\Client;
use App\Models\DataEntry;
use App\Models\Farm;
use App\Models\Mission;
use App\Models\Photo;
use App\Models\Report;
use App\Models\Technician;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class DashboardController extends ApiController
{
    /**
     * GET /api/v1/technician/dashboard
     * Statistiques et indicateurs de l'espace technicien.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user('api');

            $technician = Technician::query()->where('user_id', $user->id)->first();

            if (! $technician) {
                return $this->error('Aucun profil technicien associé à ce compte. Contactez l\'administrateur.', 404);
            }

            $missions = Mission::query()->where('technician_id', $technician->id);

            $totalMissions = (clone $missions)->count();
            $upcomingMissions = (clone $missions)->where('status', 'pending')
                ->where('scheduled_date', '>=', today())->count();
            $inProgressMissions = (clone $missions)->where('status', 'in_progress')->count();
            $completedMissions = (clone $missions)->where('status', 'completed')->count();
            $overdueMissions = (clone $missions)->where('status', 'pending')
                ->where('scheduled_date', '<', today())->count();

            $recentMissions = (clone $missions)->with('farm:id,name')
                ->orderByDesc('scheduled_date')->take(5)->get()
                ->map(fn (Mission $m) => [
                    'id' => $m->id,
                    'title' => $m->title,
                    'status' => $m->status,
                    'scheduled_date' => $m->scheduled_date?->toDateString(),
                    'farm_name' => $m->farm?->name,
                ])->all();

            $farmIds = Farm::query()->where('assigned_technician_id', $technician->id)->pluck('id');

            $clientsCount = Client::query()
                ->where('assigned_technician_id', $technician->id)
                ->orWhereIn('user_id', function ($q) use ($farmIds) {
                    $q->select('user_id')->from('farms')->whereIn('id', $farmIds);
                })
                ->orWhereHas('assignedFarms', fn ($q) => $q->whereIn('farms.id', $farmIds))
                ->distinct()
                ->count();

            $pendingValidations = [
                'reports' => Report::query()->where('technician_id', $user->id)->where('status', 'pending')->count(),
                'photos' => Photo::query()->where('technician_id', $user->id)->where('is_validated', false)->count(),
                'data_entries' => DataEntry::query()->where('technician_id', $user->id)->where('status', 'pending')->count(),
            ];

            Log::channel(self::LOG_CHANNEL)->info('API - Tableau de bord consulté', [
                'user_id' => $user->id,
                'technician_id' => $technician->id,
            ]);

            return $this->success([
                'missions' => [
                    'total' => $totalMissions,
                    'upcoming' => $upcomingMissions,
                    'in_progress' => $inProgressMissions,
                    'completed' => $completedMissions,
                    'overdue' => $overdueMissions,
                    'recent' => $recentMissions,
                ],
                'farms_count' => $farmIds->count(),
                'clients_count' => $clientsCount,
                'pending_validations' => $pendingValidations,
            ], 'Tableau de bord récupéré.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors du chargement du tableau de bord.', 500, $e);
        }
    }
}