<?php

namespace App\Http\Controllers\Api\V1\Technician;

use App\Events\ReportSubmitted;
use App\Http\Controllers\Api\ApiController;
use App\Models\Farm;
use App\Models\Report;
use App\Models\Technician;
use App\Services\MobileSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class ReportController extends ApiController
{
    public function __construct(protected MobileSyncService $sync) {}
    /**
     * GET /api/v1/technician/reports?status=&farm_id=
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user('api');

            $query = Report::query()->with('farm:id,name')->where('technician_id', $user->id);

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('farm_id')) {
                $query->where('farm_id', $request->input('farm_id'));
            }

            $reports = $query->orderByDesc('id')->get()->map(fn (Report $r) => $this->format($r))->all();

            return $this->success($reports, 'Rapports récupérés.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération des rapports.', 500, $e);
        }
    }

    /**
     * POST /api/v1/technician/reports  (multipart/form-data)
     * body: farm_id, client_id, title, type, file (pdf), notes, client_uuid (optionnel)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|integer|exists:farms,id',
                'client_id' => 'required|integer|exists:clients,id',
                'title' => 'required|string|max:255',
                'type' => 'required|string|in:monthly,soil_analysis,harvest,other,inspection,diagnostic,suivi',
                'file' => 'required|file|mimes:pdf|max:10240',
                'notes' => 'nullable|string|max:1000',
                'client_uuid' => 'nullable|string|max:64',
            ]);

            $user = $request->user('api');

            $this->assertFarmAssigned($user, $validated['farm_id']);

            // Rennvoi idempotent d'un rapport déjà envoyé (mode hors-ligne) ?
            if (! empty($validated['client_uuid'])) {
                $existingOp = $this->sync->resolveClientUuid($user, $validated['client_uuid']);

                if ($existingOp && $existingOp->status === 'applied' && $existingOp->entity_id) {
                    $report = Report::query()->find($existingOp->entity_id);

                    if ($report) {
                        return $this->success($this->format($report), 'Rapport déjà synchronisé.', 200);
                    }
                }
            }

            $path = $request->file('file')->store('reports', 'public');

            $report = Report::create([
                'title' => $validated['title'],
                'type' => $validated['type'],
                'farm_id' => $validated['farm_id'],
                'client_id' => $validated['client_id'],
                'technician_id' => $user->id,
                'file_path' => $path,
                'file_original_name' => $request->file('file')->getClientOriginalName(),
                'file_size' => $request->file('file')->getSize(),
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending',
                'is_validated' => false,
            ]);

            if (! empty($validated['client_uuid'])) {
                $this->sync->recordClientOperation($user, $validated['client_uuid'], 'Report', $report->id, 'created');
            }

            ReportSubmitted::dispatch($report, $user);

            Log::channel(self::LOG_CHANNEL)->info('API - Rapport soumis', [
                'user_id' => $user->id,
                'report_id' => $report->id,
                'type' => $report->type,
            ]);

            return $this->created($this->format($report), 'Rapport soumis et en attente de validation.');
        } catch (ValidationException $e) {
            return $this->error('Données invalides.', 422, null, $e->errors());
        } catch (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            return $this->error($e->getMessage() ?: 'Erreur.', $e->getStatusCode());
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la soumission du rapport.', 500, $e);
        }
    }

    /**
     * GET /api/v1/technician/reports/{report}/download
     */
    public function download(Request $request, int $report): JsonResponse
    {
        try {
            $user = $request->user('api');

            $model = Report::query()->where('id', $report)->where('technician_id', $user->id)->first();

            if (! $model) {
                return $this->notFound('Rapport non trouvé ou non autorisé.');
            }

            if (! $model->file_path || ! Storage::disk('public')->exists($model->file_path)) {
                return $this->error('Le fichier du rapport est introuvable.', 404);
            }

            return $this->success([
                'download_url' => Storage::disk('public')->url($model->file_path),
                'file_original_name' => $model->file_original_name,
            ], 'Lien de téléchargement généré.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la génération du lien de téléchargement.', 500, $e);
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

    protected function format(Report $r): array
    {
        return [
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
            'validated_at' => $r->validated_at?->toIso8601String(),
            'created_at' => $r->created_at?->toIso8601String(),
            'updated_at' => $r->updated_at?->toIso8601String(),
        ];
    }
}