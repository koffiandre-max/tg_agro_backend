<?php

namespace App\Services;

use App\Models\Changelog;
use App\Models\Client;
use App\Models\DataEntry;
use App\Models\Farm;
use App\Models\Mission;
use App\Models\Photo;
use App\Models\Report;
use App\Models\SyncOperation;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Construit les données de synchronisation mobile : instantané complet
 * (bootstrap) et filtrage des entrées du changelog selon le rôle.
 * Un technicien ne voit que ses missions, fermes/clients assignés
 * et ses propres saisies.
 */
class MobileSyncService
{
    public function canAccessChangelogEntry(Changelog $entry, ?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        $technician = Technician::query()->where('user_id', $user->id)->first();

        if (! $technician) {
            return false;
        }

        $payload = $entry->payload ?? [];

        return match ($entry->entity_type) {
            'Mission' => $entry->operation === 'deleted'
                ? (int) ($payload['technician_id'] ?? 0) === $technician->id
                : Mission::query()->whereKey($entry->entity_id)->where('technician_id', $technician->id)->exists(),
            'Farm' => $entry->operation === 'deleted'
                ? (int) ($payload['assigned_technician_id'] ?? 0) === $technician->id
                : Farm::query()->whereKey($entry->entity_id)->where('assigned_technician_id', $technician->id)->exists(),
            'Client' => $entry->operation === 'deleted'
                ? (int) ($payload['assigned_technician_id'] ?? 0) === $technician->id
                : $this->assignedClientIds($user)->contains($entry->entity_id),
            'DataEntry' => $entry->operation === 'deleted'
                ? (int) ($payload['technician_id'] ?? 0) === $user->id
                : DataEntry::query()->whereKey($entry->entity_id)->where('technician_id', $user->id)->exists(),
            'Report' => $entry->operation === 'deleted'
                ? (int) ($payload['technician_id'] ?? 0) === $user->id
                : Report::query()->whereKey($entry->entity_id)->where('technician_id', $user->id)->exists(),
            'Photo' => $entry->operation === 'deleted'
                ? (int) ($payload['technician_id'] ?? 0) === $user->id
                : Photo::query()->whereKey($entry->entity_id)->where('technician_id', $user->id)->exists(),
            default => false,
        };
    }

    public function snapshotFor(User $user): array
    {
        return [
            'missions' => $this->missionsFor($user),
            'farms' => $this->farmsFor($user),
            'clients' => $this->clientsFor($user),
            'reports' => $this->reportsFor($user),
            'photos' => $this->photosFor($user),
            'data_entries' => $this->dataEntriesFor($user),
        ];
    }
    public function missionsFor(User $user): array
    {
        $query = Mission::query()->with('farm:id,name');

        if ($user->role !== 'admin') {
            $query->where('technician_id', $this->technicianId($user));
        }

        return $query->orderBy('scheduled_date')->get()
            ->map(fn(Mission $m) => [
                'id' => $m->id,
                'title' => $m->title,
                'description' => $m->description,
                'farm_id' => $m->farm_id,
                'farm_name' => $m->farm?->name,
                'technician_id' => $m->technician_id,
                'scheduled_date' => $m->scheduled_date?->toDateString(),
                'status' => $m->status,
                'notes' => $m->notes,
                'completed_at' => $m->completed_at?->toIso8601String(),
                'created_at' => $m->created_at?->toIso8601String(),
                'updated_at' => $m->updated_at?->toIso8601String(),
            ])->all();
    }

    public function farmsFor(User $user): array
    {
        $query = Farm::query()->with('clients:id,code');

        if ($user->role !== 'admin') {
            $query->where('assigned_technician_id', $this->technicianId($user));
        }

        return $query->orderBy('name')->get()
            ->map(fn(Farm $f) => [
                'id' => $f->id,
                'name' => $f->name,
                'owner_user_id' => $f->user_id,
                'location' => $f->location,
                'latitude' => $f->latitude,
                'longitude' => $f->longitude,
                'type' => $f->type,
                'culture_type' => $f->culture_type,
                'status' => $f->status,
                'total_area_hectares' => $f->total_area_hectares,
                'crop_stage' => $f->crop_stage,
                'crop_stage_progress' => $f->crop_stage_progress,
                'expected_harvest_date' => $f->expected_harvest_date?->toDateString(),
                'assigned_technician_id' => $f->assigned_technician_id,
                'notes' => $f->notes,
                'client_ids' => $f->clients->pluck('id')->all(),
                'updated_at' => $f->updated_at?->toIso8601String(),
            ])->all();
    }

    public function clientsFor(User $user): array
    {
        $query = Client::query()->with('user:id,name,email,phone,avatar');

        if ($user->role !== 'admin') {
            $technician = $this->technician($user);
            $farmIds = Farm::query()->where('assigned_technician_id', $technician->id)->pluck('id');

            $query->where('assigned_technician_id', $technician->id)
                ->orWhereIn('user_id', function ($q) use ($farmIds) {
                    $q->select('user_id')->from('farms')->whereIn('id', $farmIds);
                })
                ->orWhereHas('assignedFarms', fn($q) => $q->whereIn('farms.id', $farmIds));
        }

        return $query->distinct()->orderBy('code')->get()
            ->map(fn(Client $c) => [
                'id' => $c->id,
                'code' => $c->code,
                'user_id' => $c->user_id,
                'name' => $c->user?->name,
                'email' => $c->user?->email,
                'phone' => $c->user?->phone,
                'avatar' => $c->user?->avatar,
                'assigned_technician_id' => $c->assigned_technician_id,
                'subscription_type' => $c->subscription_type,
                'subscription_expires_at' => $c->subscription_expires_at?->toDateString(),
                'updated_at' => $c->updated_at?->toIso8601String(),
            ])->all();
    }
    public function reportsFor(User $user): array
    {
        $query = Report::query()->with('farm:id,name');

        if ($user->role !== 'admin') {
            $query->where('technician_id', $user->id);
        }

        return $query->orderByDesc('id')->get()
            ->map(fn(Report $r) => [
                'id' => $r->id,
                'title' => $r->title,
                'type' => $r->type,
                'farm_id' => $r->farm_id,
                'farm_name' => $r->farm?->name,
                'client_id' => $r->client_id,
                'notes' => $r->notes,
                'status' => $r->status,
                'is_validated' => $r->is_validated,
                'rejection_reason' => $r->rejection_reason,
                'file_url' => $r->file_path ? Storage::disk('public')->url($r->file_path) : null,
                'file_original_name' => $r->file_original_name,
                'file_size' => $r->file_size,
                'created_at' => $r->created_at?->toIso8601String(),
                'updated_at' => $r->updated_at?->toIso8601String(),
            ])->all();
    }

    public function photosFor(User $user): array
    {
        $query = Photo::query()->with('farm:id,name');

        if ($user->role !== 'admin') {
            $query->where('technician_id', $user->id);
        }

        return $query->orderByDesc('id')->get()
            ->map(fn(Photo $p) => [
                'id' => $p->id,
                'farm_id' => $p->farm_id,
                'farm_name' => $p->farm?->name,
                'client_id' => $p->client_id,
                'photo_url' => Storage::disk('public')->url($p->photo_path),
                'caption' => $p->caption,
                'latitude' => $p->latitude,
                'longitude' => $p->longitude,
                'taken_at' => $p->taken_at?->toIso8601String(),
                'is_validated' => $p->is_validated,
                'created_at' => $p->created_at?->toIso8601String(),
            ])->all();
    }

    public function dataEntriesFor(User $user): array
    {
        $query = DataEntry::query()->with('farm:id,name', 'client:id,code');

        if ($user->role !== 'admin') {
            $query->where('technician_id', $user->id);
        }

        return $query->orderByDesc('id')->get()
            ->map(fn(DataEntry $d) => [
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
                'seen_by_client' => $d->seen_by_client,
                'created_at' => $d->created_at?->toIso8601String(),
                'updated_at' => $d->updated_at?->toIso8601String(),
            ])->all();
    }

// ─── Outbox : synchronisation des écritures hors-ligne ──────────

    /**
     * Retrouve une opération du mobile par son UUID local (idempotence).
     */
    public function resolveClientUuid(User $user, string $clientUuid): ?SyncOperation
    {
        return SyncOperation::query()
            ->where('user_id', $user->id)
            ->where('client_uuid', $clientUuid)
            ->first();
    }

    /**
     * Enregistre une opération du mobile (ou sa ré-application).
     */
    public function recordClientOperation(
        User $user,
        string $clientUuid,
        string $entityType,
        ?int $entityId,
        string $operation,
        ?string $deviceId = null,
        string $status = 'applied',
        ?string $errorMessage = null,
    ): SyncOperation {
        return SyncOperation::updateOrCreate(
            ['user_id' => $user->id, 'client_uuid' => $clientUuid],
            [
                'device_id' => $deviceId,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'operation' => $operation,
                'status' => $status,
                'error_message' => $errorMessage,
                'applied_at' => now(),
            ]
        );
    }

    /**
     * Traite un lot d'opérations hors-ligne (POST /api/v1/mobile/sync).
     *
     * Chaque opération est traitée indépendamment (best-effort) :
     *  - `applied` : appliquée avec succès (entity_id serveur fourni),
     *  - `idempotent` : déjà appliquée (renvoi du client), renvoie l'ID serveur,
     *  - `skipped` : ignorée (conflit LWW, la version serveur est plus récente),
     *  - `error` : rejetée (validation, autorisation, fichier requis…).
     *
     * @return array{results: array, applied_count: int, failed_count: int}
     */
    public function processOutbox(array $operations, User $user, ?string $deviceId = null): array
    {
        $results = [];
        $applied = 0;
        $failed = 0;

        foreach ($operations as $i => $op) {
            $clientUuid = (string) ($op['client_uuid'] ?? $op['uuid'] ?? ('generated-' . $i . '-' . uniqid()));
            $entityType = (string) ($op['entity_type'] ?? '');
            $operation = (string) ($op['operation'] ?? '');
            $entityId = isset($op['entity_id']) ? (int) $op['entity_id'] : null;

            // 1. Dé-duplication : l'opération est déjà appliquée.
            $existing = $this->resolveClientUuid($user, $clientUuid);

            if ($existing && $existing->status === 'applied') {
                $results[] = [
                    'client_uuid' => $clientUuid,
                    'status' => 'idempotent',
                    'entity_type' => $entityType ?: $existing->entity_type,
                    'entity_id' => $existing->entity_id,
                ];
                continue;
            }

            // 2. Application de l'opération.
            $outcome = $this->applyOperation($op, $user, $clientUuid, $deviceId);

            if ($outcome['status'] === 'applied') {
                $applied++;
            } elseif ($outcome['status'] !== 'skipped') {
                $failed++;
            }

            $results[] = $outcome;
        }

        return [
            'results' => $results,
            'applied_count' => $applied,
            'failed_count' => $failed,
        ];
    }

    /**
     * Applique une opération hors-ligne et l'enregistre dans l'outbox.
     */
    protected function applyOperation(array $op, User $user, string $clientUuid, ?string $deviceId): array
    {
        $entityType = (string) ($op['entity_type'] ?? '');
        $operation = (string) ($op['operation'] ?? '');
        $payload = $op['payload'] ?? [];
        $entityId = isset($op['entity_id']) ? (int) $op['entity_id'] : null;
        $clientUpdatedAt = isset($op['client_updated_at']) ? \Carbon\Carbon::parse($op['client_updated_at']) : now();

        $base = [
            'client_uuid' => $clientUuid,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
        ];

        try {
            return match ($entityType) {
                'DataEntry' => $this->applyDataEntry($operation, $payload, $user, $clientUuid, $deviceId, $clientUpdatedAt, $entityId),
                'Mission' => $this->applyMission($operation, $payload, $user, $clientUuid, $deviceId, $entityId),
                'Report', 'Photo' => $this->applyBinary($entityType, $operation, $user, $clientUuid, $deviceId, $entityId),
                default => $this->failOutcome($base, 'Type d\'entité non pris en charge par l\'outbox : ' . $entityType . '.'),
            };
        } catch (\Throwable $e) {
            return $this->failOutcome($base, $e->getMessage());
        }
    }
    // ─── Helpers ─────────────────────────────────────────────────

    public function technicianId(User $user): ?int
    {
        return $user->technician?->id
            ?? Technician::query()->where('user_id', $user->id)->value('id');
    }

    public function technician(User $user): Technician
    {
        return $user->technician
            ?? Technician::query()->where('user_id', $user->id)->firstOrFail();
    }

    public function assignedClientIds(User $user): Collection
    {
        $technician = $this->technician($user);
        $farmIds = Farm::query()->where('assigned_technician_id', $technician->id)->pluck('id');

        return Client::query()
            ->where('assigned_technician_id', $technician->id)
            ->orWhereIn('user_id', function ($q) use ($farmIds) {
                $q->select('user_id')->from('farms')->whereIn('id', $farmIds);
            })
            ->orWhereHas('assignedFarms', fn($q) => $q->whereIn('farms.id', $farmIds))
            ->distinct()
            ->pluck('clients.id');
    }

    // ─── Application des opérations outbox ───────────────────────

    protected function applyDataEntry(
        string $operation,
        array $payload,
        User $user,
        string $clientUuid,
        ?string $deviceId,
        \Carbon\CarbonInterface $clientUpdatedAt,
        ?int $entityId,
    ): array {
        $base = ['client_uuid' => $clientUuid, 'entity_type' => 'DataEntry', 'entity_id' => $entityId];

        if ($operation === 'deleted') {
            if (! $entityId) {
                return $this->failOutcome($base, 'entity_id est obligatoire pour supprimer une saisie.');
            }

            $entry = DataEntry::query()->whereKey($entityId)->where('technician_id', $user->id)->first();

            if ($entry) {
                $id = $entry->id;
                $entry->delete();
                $this->recordClientOperation($user, $clientUuid, 'DataEntry', $id, 'deleted', $deviceId);

                return [...$base, 'status' => 'applied', 'entity_id' => $id];
            }

            return $this->failOutcome($base, 'Saisie introuvable ou non autorisée.');
        }

        // champs texte (création / mise à jour)
        $onServer = null;

        if ($entityId) {
            $onServer = DataEntry::query()->whereKey($entityId)->where('technician_id', $user->id)->first();
        }

        if ($operation === 'created' && $onServer) {
            return $this->idempotentOutcome($base, $onServer->id);
        }

        if ($operation !== 'created' && ! $entityId) {
            return $this->failOutcome($base, 'entity_id est obligatoire pour mettre à jour une saisie.');
        }

        $data = array_filter([
            'farm_id' => $payload['farm_id'] ?? null,
            'client_id' => $payload['client_id'] ?? null,
            'crop_stage' => $payload['crop_stage'] ?? null,
            'crop_stage_progress' => $payload['crop_stage_progress'] ?? null,
            'estimated_harvest_date' => $payload['estimated_harvest_date'] ?? null,
            'inputs_used' => $payload['inputs_used'] ?? null,
            'observations' => $payload['observations'] ?? null,
            'weather_conditions' => $payload['weather_conditions'] ?? null,
            'client_updated_at' => $clientUpdatedAt,
        ], fn($v) => $v !== null);

        if (! isset($data['farm_id'], $data['client_id']) && $operation === 'created') {
            return $this->failOutcome($base, 'farm_id et client_id sont obligatoires pour créer une saisie.');
        }

        if (isset($data['farm_id'])) {
            $this->assertFarmAssigned($user, (int) $data['farm_id']);
        }

        if ($operation === 'updated') {
            if (! $onServer) {
                return $this->failOutcome($base, 'Saisie introuvable ou non autorisée.');
            }

            // Résolution de conflit Last-Write-Wins : la version serveur
            // (client_updated_at le plus récent) remporte.
            if ($onServer->client_updated_at !== null && $onServer->client_updated_at->gt($clientUpdatedAt)) {
                $this->recordClientOperation($user, $clientUuid, 'DataEntry', $onServer->id, 'updated', $deviceId, 'skipped');

                return [...$base, 'status' => 'skipped', 'entity_id' => $onServer->id];
            }

            $onServer->update($data);
        } else {
            // created
            $onServer = DataEntry::create([
                ...$data,
                'technician_id' => $user->id,
                'status' => $payload['status'] ?? 'pending',
            ]);
        }

        $this->recordClientOperation($user, $clientUuid, 'DataEntry', $onServer->id, $operation, $deviceId);

        return [...$base, 'status' => 'applied', 'entity_id' => $onServer->id];
    }
    protected function applyMission(
        string $operation,
        array $payload,
        User $user,
        string $clientUuid,
        ?string $deviceId,
        ?int $entityId,
    ): array {
        $base = ['client_uuid' => $clientUuid, 'entity_type' => 'Mission', 'entity_id' => $entityId];

        if ($operation !== 'updated' || ! $entityId) {
            return $this->failOutcome($base, 'Seule la mise à jour du statut d\'une mission est prise en charge.');
        }

        $status = $payload['status'] ?? null;

        if (! in_array($status, ['pending', 'in_progress', 'completed', 'cancelled'], true)) {
            return $this->failOutcome($base, 'Statut de mission invalide.');
        }

        $mission = Mission::query()->whereKey($entityId)
            ->where('technician_id', $this->technicianId($user))
            ->first();

        if (! $mission) {
            return $this->failOutcome($base, 'Mission introuvable ou non autorisée.');
        }

        $mission->update(['status' => $status]);

        if ($status === 'completed' && ! $mission->completed_at) {
            $mission->update(['completed_at' => now()]);
        } elseif ($status !== 'completed' && $mission->completed_at) {
            $mission->update(['completed_at' => null]);
        }

        $this->recordClientOperation($user, $clientUuid, 'Mission', $mission->id, 'updated', $deviceId);

        return [...$base, 'status' => 'applied', 'entity_id' => $mission->id];
    }

    /**
     * Les fichiers (rapports PDF, photos) ne passent pas par l'outbox :
     * ils sont téléversés via leurs endpoints multipart dédiés une fois
     * connecté (avec client_uuid pour éviter les doublons).
     */
    protected function applyBinary(
        string $entityType,
        string $operation,
        User $user,
        string $clientUuid,
        ?string $deviceId,
        ?int $entityId,
    ): array {
        $base = ['client_uuid' => $clientUuid, 'entity_type' => $entityType, 'entity_id' => $entityId];

        return $this->failOutcome(
            $base,
            'Fichier requis : utilisez POST /api/v1/technician/' . strtolower($entityType)
                . ' en multipart lorsque le réseau est rétabli (client_uuid obligatoire).'
        );
    }
    protected function idempotentOutcome(array $base, int $entityId): array
    {
        return ['status' => 'idempotent', 'entity_id' => $entityId, ...$base];
    }

    protected function failOutcome(array $base, string $message): array
    {
        return ['status' => 'error', 'error' => $message, ...$base];
    }

    public function assertFarmAssigned(User $user, int $farmId): void
    {
        $technicianId = $this->technicianId($user);

        $owned = Farm::query()->where('id', $farmId)
            ->where('assigned_technician_id', $technicianId)
            ->exists();

        if (! $owned) {
            throw new \RuntimeException('Cette exploitation ne vous est pas assignée.');
        }
    }
}
