<?php

namespace App\Http\Controllers\Api\V1\Technician;

use App\Http\Controllers\Api\ApiController;
use App\Models\Farm;
use App\Models\Photo;
use App\Models\Technician;
use App\Services\MobileSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class PhotoController extends ApiController
{
    public function __construct(protected MobileSyncService $sync) {}
    /**
     * GET /api/v1/technician/photos?farm_id=&client_id=
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user('api');

            $query = Photo::query()->with('farm:id,name')->where('technician_id', $user->id);

            if ($request->filled('client_id')) {
                $query->where('client_id', $request->input('client_id'));
            }

            if ($request->filled('farm_id')) {
                $query->where('farm_id', $request->input('farm_id'));
            }

            $photos = $query->orderByDesc('id')->get()->map(fn (Photo $p) => $this->format($p))->all();

            return $this->success($photos, 'Photos récupérées.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération des photos.', 500, $e);
        }
    }

    /**
     * POST /api/v1/technician/photos  (multipart/form-data)
     * body: client_id, farm_id, photos[] (images), latitude, longitude, caption, taken_at, client_uuid (optionnel)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'client_id' => 'required|integer|exists:clients,id',
                'farm_id' => 'required|integer|exists:farms,id',
                'photos' => 'required|array|min:1|max:20',
                'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'caption' => 'nullable|string|max:500',
                'taken_at' => 'nullable|date',
                'client_uuid' => 'nullable|string|max:64',
            ]);

            $user = $request->user('api');

            $this->assertFarmAssigned($user, $validated['farm_id']);

            // Rennvoi idempotent d'un lot de photos déjà envoyé (mode hors-ligne) ?
            if (! empty($validated['client_uuid'])) {
                $existingOp = $this->sync->resolveClientUuid($user, $validated['client_uuid']);

                if ($existingOp && $existingOp->status === 'applied' && $existingOp->entity_id) {
                    $photo = Photo::query()->find($existingOp->entity_id);

                    if ($photo) {
                        return $this->success([$this->format($photo)], 'Photos déjà synchronisées.', 200);
                    }
                }
            }

            $takenAt = $validated['taken_at'] ?? now();
            $created = [];
            $firstRecord = null;

            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('gallery', 'public');

                $record = Photo::create([
                    'farm_id' => $validated['farm_id'],
                    'client_id' => $validated['client_id'],
                    'technician_id' => $user->id,
                    'photo_path' => $path,
                    'thumbnail_path' => null,
                    'caption' => $validated['caption'] ?? null,
                    'latitude' => $validated['latitude'] ?? null,
                    'longitude' => $validated['longitude'] ?? null,
                    'taken_at' => $takenAt,
                    'is_visible_to_client' => false,
                    'is_validated' => false,
                    'file_size' => $photo->getSize(),
                ]);

                $firstRecord ??= $record;
                $created[] = $this->format($record);
            }

            if (! empty($validated['client_uuid']) && $firstRecord) {
                $this->sync->recordClientOperation($user, $validated['client_uuid'], 'Photo', $firstRecord->id, 'created');
            }

            Log::channel(self::LOG_CHANNEL)->info('API - Photos uploadées', [
                'user_id' => $user->id,
                'count' => count($created),
                'farm_id' => $validated['farm_id'],
            ]);

            return $this->created($created, count($created) . ' photo(s) ajoutée(s) avec succès.');
        } catch (ValidationException $e) {
            return $this->error('Données invalides.', 422, null, $e->errors());
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            return $this->error($e->getMessage() ?: 'Erreur.', $e->getStatusCode());
        } catch (Throwable $e) {
            return $this->error('Erreur lors de l\'envoi des photos.', 500, $e);
        }
    }

    protected function assertFarmAssigned($user, int $farmId): void
    {
        $technicianId = $user->technician?->id ?? Technician::query()->where('user_id', $user->id)->value('id');

        $owned = Farm::query()->where('id', $farmId)->where('assigned_technician_id', $technicianId)->exists();

        if (! $owned) {
            abort(403, 'Cette exploitation ne vous est pas assignée.');
        }
    }

    protected function format(Photo $p): array
    {
        return [
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
            'validated_at' => $p->validated_at?->toIso8601String(),
            'file_size' => $p->file_size,
            'created_at' => $p->created_at?->toIso8601String(),
        ];
    }
}