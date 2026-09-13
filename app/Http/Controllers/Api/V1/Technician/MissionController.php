<?php

namespace App\Http\Controllers\Api\V1\Technician;

use App\Enums\MissionStatus;
use App\Http\Controllers\Api\ApiController;
use App\Models\Mission;
use App\Models\Technician;
use App\Services\MobileSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class MissionController extends ApiController
{
    public function __construct(protected MobileSyncService $sync) {}
    /**
     * GET /api/v1/technician/missions?status=...&date=...
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user('api');
            $technicianId = Technician::query()->where('user_id', $user->id)->value('id');

            $query = Mission::query()->with('farm:id,name,location')->where('technician_id', $technicianId);

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('date')) {
                $query->whereDate('scheduled_date', $request->input('date'));
            }

            $missions = $query->orderBy('scheduled_date')->get()->map(fn (Mission $m) => $this->format($m))->all();

            return $this->success($missions, 'Missions récupérées.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération des missions.', 500, $e);
        }
    }

    /**
     * GET /api/v1/technician/missions/{mission}
     */
    public function show(Request $request, int $mission): JsonResponse
    {
        try {
            $missionModel = $this->findForUser($request->user('api'), $mission);

            return $this->success($this->format($missionModel), 'Mission récupérée.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            return $this->error($e->getMessage() ?: 'Erreur.', $e->getStatusCode());
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération de la mission.', 500, $e);
        }
    }

    /**
     * PATCH /api/v1/technician/missions/{mission}/status  { status }
     */
    public function updateStatus(Request $request, int $mission): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => ['required', 'in:' . implode(',', array_column(MissionStatus::cases(), 'value'))],
                'client_uuid' => ['nullable', 'string', 'max:64'],
            ]);

            $user = $request->user('api');

            // Rennvoi idempotent (mode hors-ligne) ?
            if (! empty($validated['client_uuid'])) {
                $existingOp = $this->sync->resolveClientUuid($user, $validated['client_uuid']);

                if ($existingOp && $existingOp->status === 'applied' && $existingOp->entity_id === $mission) {
                    $mapped = $this->findForUser($user, (int) $existingOp->entity_id);

                    return $this->success($this->format($mapped), 'Statut déjà synchronisé.', 200);
                }
            }

            $missionModel = $this->findForUser($user, $mission);

            $missionModel->update(['status' => $validated['status']]);
            $this->syncCompletion($missionModel);

            if (! empty($validated['client_uuid'])) {
                $this->sync->recordClientOperation($user, $validated['client_uuid'], 'Mission', $missionModel->id, 'updated');
            }

            Log::channel(self::LOG_CHANNEL)->info('API - Statut de mission mis à jour', [
                'user_id' => $user->id,
                'mission_id' => $missionModel->id,
                'status' => $validated['status'],
            ]);

            return $this->success($this->format($missionModel), 'Le statut de la mission a été mis à jour.');
        } catch (ValidationException $e) {
            return $this->error('Données invalides.', 422, null, $e->errors());
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            return $this->error($e->getMessage() ?: 'Erreur.', $e->getStatusCode());
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la mise à jour de la mission.', 500, $e);
        }
    }

    protected function findForUser($user, int $missionId): Mission
    {
        $technicianId = Technician::query()->where('user_id', $user->id)->value('id');

        $mission = Mission::query()->with(['farm:id,name,location', 'technician.user:id,name'])
            ->where('id', $missionId)
            ->where('technician_id', $technicianId)
            ->first();

        if (! $mission) {
            abort(404, 'Mission non trouvée ou non autorisée.');
        }

        return $mission;
    }

    protected function format(Mission $m): array
    {
        $statusLabel = null;

        try {
            $statusLabel = MissionStatus::from($m->status)->label();
        } catch (\ValueError) {
            $statusLabel = $m->status;
        }

        return [
            'id' => $m->id,
            'title' => $m->title,
            'description' => $m->description,
            'farm_id' => $m->farm_id,
            'farm_name' => $m->farm?->name,
            'farm_location' => $m->farm?->location,
            'technician_id' => $m->technician_id,
            'scheduled_date' => $m->scheduled_date?->toDateString(),
            'status' => $m->status,
            'status_label' => $statusLabel,
            'notes' => $m->notes,
            'completed_at' => $m->completed_at?->toIso8601String(),
            'created_at' => $m->created_at?->toIso8601String(),
            'updated_at' => $m->updated_at?->toIso8601String(),
        ];
    }

    protected function syncCompletion(Mission $mission): void
    {
        if ($mission->status === 'completed' && ! $mission->completed_at) {
            $mission->update(['completed_at' => now()]);
        } elseif ($mission->status !== 'completed' && $mission->completed_at) {
            $mission->update(['completed_at' => null]);
        }
    }
}