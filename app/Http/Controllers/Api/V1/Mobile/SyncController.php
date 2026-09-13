<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\ApiController;
use App\Services\ChangelogService;
use App\Services\MobileSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Point d'entrée de la synchronisation hors-ligne (bidirectionnelle).
 *
 * POST /api/v1/mobile/sync
 * {
 *   "device_id": "iphone-7f3a",
 *   "operations": [
 *     {
 *       "client_uuid": "uuid-local-généré",
 *       "entity_type": "DataEntry",
 *       "operation": "created",
 *       "entity_id": null,
 *       "payload": { "farm_id": 1, "client_id": 2, "observations": "…" },
 *       "client_updated_at": "2026-09-08T09:00:00Z"
 *     },
 *     { "entity_type": "Mission", "operation": "updated", "entity_id": 3,
 *       "payload": { "status": "completed" } }
 *   ]
 * }
 *
 * Le mobile envoie ici sa file locale d'opérations effectuées hors-ligne ;
 * le serveur les applique de façon idempotente (client_uuid) et répond
 * avec le mapping uuid local → id serveur, puis le client recharge le
 * changelog (entrées serveur > à son curseur local).
 */
class SyncController extends ApiController
{
    public function __construct(
        protected MobileSyncService $sync,
        protected ChangelogService $changelog,
    ) {}

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'device_id' => ['nullable', 'string', 'max:100'],
                'operations' => ['required', 'array', 'max:500'],
                'operations.*.client_uuid' => ['required_without:operations.*.uuid', 'string', 'max:64'],
                'operations.*.uuid' => ['required_without:operations.*.client_uuid', 'string', 'max:64'],
                'operations.*.entity_type' => ['required', 'string', 'max:100'],
                'operations.*.operation' => ['required', 'in:created,updated,deleted'],
                'operations.*.entity_id' => ['nullable', 'integer'],
                'operations.*.payload' => ['array'],
                'operations.*.client_updated_at' => ['nullable', 'date'],
            ]);

            $user = $request->user('api');

            if (! $user) {
                return $this->unauthorized();
            }

            $result = $this->sync->processOutbox(
                $validated['operations'],
                $user,
                $validated['device_id'] ?? null
            );

            Log::channel(self::LOG_CHANNEL)->info('API - Synchronisation outbox', [
                'user_id' => $user->id,
                'device_id' => $validated['device_id'] ?? null,
                'operations' => count($validated['operations']),
                'applied' => $result['applied_count'],
                'failed' => $result['failed_count'],
            ]);

            return $this->success([
                'results' => $result['results'],
                'applied_count' => $result['applied_count'],
                'failed_count' => $result['failed_count'],
                'latest_cursor' => $this->changelog->latestCursor(),
                'message' => $result['failed_count'] > 0
                    ? 'Certaines opérations ont été rejetées, consultez results.'
                    : 'Synchronisation réussie.',
            ], 'Opérations hors-ligne synchronisées.');
        } catch (ValidationException $e) {
            return $this->error('Données invalides.', 422, null, $e->errors());
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la synchronisation.', 500, $e);
        }
    }
}
