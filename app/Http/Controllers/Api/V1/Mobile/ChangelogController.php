<?php

namespace App\Http\Controllers\Api\V1\Mobile;

use App\Http\Controllers\Api\ApiController;
use App\Models\Changelog;
use App\Services\ChangelogService;
use App\Services\MobileSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * Architecture "changelog" pour l'application mobile.
 *
 * - GET  /api/v1/mobile/bootstrap      : instantané complet (1er lancement).
 * - GET  /api/v1/mobile/changelog      : changements incrémentaux (curseur).
 * - POST /api/v1/mobile/changelog/ack  : acquittement du curseur local.
 */
class ChangelogController extends ApiController
{
    protected const FETCH_LIMIT = 1000;
    protected const DEFAULT_LIMIT = 100;
    protected const MAX_LIMIT = 500;

    protected const ENTITY_TYPES = ['Mission', 'Farm', 'Client', 'Report', 'Photo', 'DataEntry'];

    public function __construct(
        protected ChangelogService $service,
        protected MobileSyncService $sync,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $cursor = max(0, $request->integer('cursor', 0));
            $entityType = $request->input('entity_type') ?: null;
            $limit = max(1, min($request->integer('limit', self::DEFAULT_LIMIT), self::MAX_LIMIT));

            if ($entityType && ! in_array($entityType, self::ENTITY_TYPES, true)) {
                return $this->error('Type d\'entité inconnu. Valeurs acceptées : ' . implode(', ', self::ENTITY_TYPES) . '.', 422);
            }

            $user = $request->user('api');

            $filtered = $this->service
                ->changesAfter($cursor, $entityType, self::FETCH_LIMIT)
                ->filter(fn (Changelog $entry) => $this->sync->canAccessChangelogEntry($entry, $user))
                ->take($limit)
                ->values();

            $nextCursor = $filtered->last()?->id ?? $cursor;
            $hasMore = $filtered->count() === $limit;

            return $this->success(
                $filtered->map(fn (Changelog $entry) => $this->format($entry))->all(),
                'Changements récupérés.',
                200
            )->withHeaders(['X-Next-Cursor' => $nextCursor]);
        } catch (ValidationException $e) {
            return $this->error('Données invalides.', 422, null, $e->errors());
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération des changements.', 500, $e);
        }
    }

    public function bootstrap(Request $request): JsonResponse
    {
        try {
            $user = $request->user('api');

            if (! $user) {
                return $this->unauthorized();
            }

            return $this->success([
                'current_cursor' => $this->service->latestCursor(),
                'server_time' => now()->toIso8601String(),
                'version' => 'v1',
                'data' => $this->sync->snapshotFor($user),
            ], 'Données initiales récupérées.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la synchronisation initiale.', 500, $e);
        }
    }

    public function acknowledge(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'cursor' => ['required', 'integer', 'min:0'],
            ]);

            Log::channel(self::LOG_CHANNEL)->info('API - Changements acquittés', [
                'user_id' => $request->user('api')?->id,
                'cursor' => $validated['cursor'],
            ]);

            return $this->success([
                'acknowledged_cursor' => (int) $validated['cursor'],
                'latest_cursor' => $this->service->latestCursor(),
            ], 'Acquittement enregistré.');
        } catch (ValidationException $e) {
            return $this->error('Données invalides.', 422, null, $e->errors());
        } catch (Throwable $e) {
            return $this->error('Erreur lors de l\'acquittement.', 500, $e);
        }
    }

    protected function format(Changelog $entry): array
    {
        return [
            'cursor' => $entry->id,
            'entity_type' => $entry->entity_type,
            'entity_id' => $entry->entity_id,
            'operation' => $entry->operation,
            'payload' => $entry->payload,
            'created_at' => $entry->created_at?->toIso8601String(),
        ];
    }
}