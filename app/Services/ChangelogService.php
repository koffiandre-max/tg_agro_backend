<?php

namespace App\Services;

use App\Models\Changelog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Service central de l'architecture "changelog".
 *
 * Enregistre chaque changement d'entité, fournit la liste des changements
 * postérieurs à un curseur donné et détermine le dernier curseur disponible.
 */
class ChangelogService
{
    public const OPERATION_CREATED = 'created';
    public const OPERATION_UPDATED = 'updated';
    public const OPERATION_DELETED = 'deleted';
    public const OPERATION_RESTORED = 'restored';

    /**
     * Enregistre un changement dans le journal.
     */
    public function record(
        string $entityType,
        int $entityId,
        string $operation,
        ?array $payload = null,
        ?int $userId = null
    ): Changelog {
        $entry = Changelog::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'operation' => $operation,
            'payload' => $payload,
            'user_id' => $userId ?? auth('api')->id() ?? auth()->id(),
            'created_at' => now(),
        ]);

        Log::channel('api')->info('Changelog - Changement enregistré', [
            'id' => $entry->id,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'operation' => $operation,
            'user_id' => $entry->user_id,
        ]);

        return $entry;
    }

    /**
     * Retourne les changements strictement postérieurs au curseur fourni,
     * optionnellement filtrés par type d'entité, ordonnés par identifiant.
     */
    public function changesAfter(int $cursor = 0, ?string $entityType = null, int $limit = 100): Collection
    {
        $query = Changelog::query()
            ->where('id', '>', $cursor)
            ->orderBy('id');

        if ($entityType) {
            $query->where('entity_type', $entityType);
        }

        return $query->take($limit)->get();
    }

    /**
     * Dernier curseur (max(id)) disponible pour la synchronisation.
     */
    public function latestCursor(): int
    {
        return (int) Changelog::query()->max('id');
    }
}