<?php

namespace App\Http\Controllers\Api\V1\Technician;

use App\Http\Controllers\Api\ApiController;
use App\Models\DataEntry;
use App\Models\Farm;
use App\Models\Technician;
use App\Services\MobileSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class DataEntryController extends ApiController
{
    public function __construct(protected MobileSyncService $sync) {}
    /**
     * GET /api/v1/technician/data?farm_id=&client_id=
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user('api');

            $query = DataEntry::query()
                ->with('farm:id,name', 'client:id,code')
                ->where('technician_id', $user->id);

            if ($request->filled('farm_id')) {
                $query->where('farm_id', $request->input('farm_id'));
            }

            if ($request->filled('client_id')) {
                $query->where('client_id', $request->input('client_id'));
            }

            $entries = $query->orderByDesc('id')->get()->map(fn(DataEntry $d) => $this->format($d))->all();

            return $this->success($entries, 'Saisies récupérées.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération des saisies.', 500, $e);
        }
    }

    /**
     * GET /api/v1/technician/data/{dataEntry}
     */
    public function show(Request $request, int $dataEntry): JsonResponse
    {
        try {
            $entry = $this->findForUser($request->user('api'), $dataEntry);
            $entry->load('farm:id,name', 'client:id,code');

            return $this->success($this->format($entry), 'Saisie récupérée.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            return $this->error($e->getMessage() ?: 'Erreur.', $e->getStatusCode());
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération de la saisie.', 500, $e);
        }
    }

    /**
     * POST /api/v1/technician/data
     * Optionnel : client_uuid (ID local du mobile) pour les renvois idempotents.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|integer|exists:farms,id',
                'client_id' => 'required|integer|exists:clients,id',
                'crop_stage' => 'nullable|string|max:255',
                'crop_stage_progress' => 'nullable|integer|min:0|max:100',
                'estimated_harvest_date' => 'nullable|date',
                'inputs_used' => 'nullable|string|max:500',
                'observations' => 'nullable|string|max:1000',
                'weather_conditions' => 'nullable|string|max:255',
                'client_uuid' => 'nullable|string|max:64',
                'client_updated_at' => 'nullable|date',
            ]);

            $user = $request->user('api');

            $this->assertFarmAssigned($user, $validated['farm_id']);

            // Rennvoi d'une opération déjà appliquée (mode hors-ligne) ?
            if (! empty($validated['client_uuid'])) {
                $existingOp = $this->sync->resolveClientUuid($user, $validated['client_uuid']);

                if ($existingOp && $existingOp->status === 'applied' && $existingOp->entity_id) {
                    $entry = DataEntry::query()->find($existingOp->entity_id);

                    if ($entry) {
                        return $this->success($this->format($entry), 'Saisie déjà synchronisée.', 200);
                    }
                }
            }

            $entry = DataEntry::create([
                'farm_id' => $validated['farm_id'],
                'client_id' => $validated['client_id'],
                'technician_id' => $user->id,
                'crop_stage' => $validated['crop_stage'] ?? null,
                'crop_stage_progress' => $validated['crop_stage_progress'] ?? 0,
                'estimated_harvest_date' => $validated['estimated_harvest_date'] ?? null,
                'inputs_used' => $validated['inputs_used'] ?? null,
                'observations' => $validated['observations'] ?? null,
                'weather_conditions' => $validated['weather_conditions'] ?? null,
                'status' => 'pending',
                'client_updated_at' => $validated['client_updated_at'] ?? null,
            ]);

            if (! empty($validated['client_uuid'])) {
                $this->sync->recordClientOperation($user, $validated['client_uuid'], 'DataEntry', $entry->id, 'created');
            }

            Log::channel(self::LOG_CHANNEL)->info('API - Saisie créée', [
                'user_id' => $user->id,
                'data_entry_id' => $entry->id,
                'farm_id' => $entry->farm_id,
            ]);

            return $this->created($this->format($entry), 'Saisie créée et en attente de validation.');
        } catch (ValidationException $e) {
            return $this->error('Données invalides.', 422, null, $e->errors());
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            return $this->error($e->getMessage() ?: 'Erreur.', $e->getStatusCode());
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la création de la saisie.', 500, $e);
        }
    }
    public function update(Request $request, int $dataEntry): JsonResponse
    {
        try {
            $user = $request->user('api');
            $entry = $this->findForUser($user, $dataEntry);

            $validated = $request->validate([
                'farm_id' => 'sometimes|integer|exists:farms,id',
                'client_id' => 'sometimes|integer|exists:clients,id',
                'crop_stage' => 'nullable|string|max:255',
                'crop_stage_progress' => 'nullable|integer|min:0|max:100',
                'estimated_harvest_date' => 'nullable|date',
                'inputs_used' => 'nullable|string|max:500',
                'observations' => 'nullable|string|max:1000',
                'weather_conditions' => 'nullable|string|max:255',
            ]);

            if (isset($validated['farm_id'])) {
                $this->assertFarmAssigned($user, $validated['farm_id']);
            }

            $entry->update(collect($validated)->filter(fn($v) => $v !== null)->all());

            Log::channel(self::LOG_CHANNEL)->info('API - Saisie mise à jour', [
                'user_id' => $user->id,
                'data_entry_id' => $entry->id,
            ]);

            return $this->success($this->format($entry), 'Saisie mise à jour.');
        } catch (ValidationException $e) {
            return $this->error('Données invalides.', 422, null, $e->errors());
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            return $this->error($e->getMessage() ?: 'Erreur.', $e->getStatusCode());
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la mise à jour de la saisie.', 500, $e);
        }
    }

    /**
     * DELETE /api/v1/technician/data/{dataEntry}
     */
    public function destroy(Request $request, int $dataEntry): JsonResponse
    {
        try {
            $user = $request->user('api');
            $entry = $this->findForUser($user, $dataEntry);
            $entry->delete();

            Log::channel(self::LOG_CHANNEL)->info('API - Saisie supprimée', [
                'user_id' => $user->id,
                'data_entry_id' => $entry->id,
            ]);

            return $this->success(null, 'Saisie supprimée.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            return $this->error($e->getMessage() ?: 'Erreur.', $e->getStatusCode());
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la suppression de la saisie.', 500, $e);
        }
    }

    protected function findForUser($user, int $id): DataEntry
    {
        $entry = DataEntry::query()->where('id', $id)->where('technician_id', $user->id)->first();

        if (! $entry) {
            abort(404, 'Saisie non trouvée ou non autorisée.');
        }

        return $entry;
    }

    protected function assertFarmAssigned($user, int $farmId): void
    {
        $technicianId = $user->technician?->id
            ?? Technician::query()->where('user_id', $user->id)->value('id');

        $owned = Farm::query()->where('id', $farmId)->where('assigned_technician_id', $technicianId)->exists();

        if (! $owned) {
            abort(403, 'Cette exploitation ne vous est pas assignée.');
        }
    }

    protected function format(DataEntry $d): array
    {
        return [
            'id' => $d->id,
            'farm_id' => $d->farm_id,
            'farm_name' => $d->farm?->name,
            'client_id' => $d->client_id,
            'client_code' => $d->client?->code,
            'crop_stage' => $d->crop_stage,
            'crop_stage_progress' => $d->crop_stage_progress,
            'estimated_harvest_date' => $d->estimated_harvest_date?->toDateString(),
            'inputs_used' => $d->inputs_used,
            'observations' => $d->observations,
            'weather_conditions' => $d->weather_conditions,
            'status' => $d->status,
            'rejection_reason' => $d->rejection_reason,
            'seen_by_client' => $d->seen_by_client,
            'created_at' => $d->created_at?->toIso8601String(),
            'updated_at' => $d->updated_at?->toIso8601String(),
        ];
    }
}
