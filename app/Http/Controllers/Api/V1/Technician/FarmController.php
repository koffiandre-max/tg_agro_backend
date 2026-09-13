<?php

namespace App\Http\Controllers\Api\V1\Technician;

use App\Http\Controllers\Api\ApiController;
use App\Models\Farm;
use App\Models\Technician;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class FarmController extends ApiController
{
    /**
     * GET /api/v1/technician/farms
     * Exploitations assignées au technicien connecté.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user('api');
            $technicianId = Technician::query()->where('user_id', $user->id)->value('id');

            $query = Farm::query()->with('clients:id,code')->where('assigned_technician_id', $technicianId);

            if ($request->filled('type')) {
                $query->where('type', $request->input('type'));
            }

            $farms = $query->orderBy('name')->get()->map(fn (Farm $f) => $this->format($f))->all();

            return $this->success($farms, 'Exploitations récupérées.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération des exploitations.', 500, $e);
        }
    }

    /**
     * GET /api/v1/technician/farms/{farm}
     */
    public function show(Request $request, int $farm): JsonResponse
    {
        try {
            $user = $request->user('api');
            $technicianId = Technician::query()->where('user_id', $user->id)->value('id');

            $model = Farm::query()->with(['clients.user:id,name,email,phone', 'user:id,name,phone'])
                ->where('id', $farm)
                ->where('assigned_technician_id', $technicianId)
                ->first();

            if (! $model) {
                return $this->notFound('Exploitation non trouvée ou non autorisée.');
            }

            return $this->success($this->format($model, true), 'Exploitation récupérée.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération de l\'exploitation.', 500, $e);
        }
    }

    protected function format(Farm $f, bool $detailed = false): array
    {
        $data = [
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
            'last_visit_date' => $f->last_visit_date?->toDateString(),
            'assigned_technician_id' => $f->assigned_technician_id,
            'notes' => $f->notes,
            'client_codes' => $f->clients->pluck('code')->all(),
            'updated_at' => $f->updated_at?->toIso8601String(),
        ];

        if ($detailed) {
            $data['clients'] = $f->clients->map(fn ($c) => [
                'id' => $c->id,
                'code' => $c->code,
                'name' => $c->user?->name,
                'email' => $c->user?->email,
                'phone' => $c->user?->phone,
            ])->all();
            $data['owner'] = [
                'name' => $f->user?->name,
                'phone' => $f->user?->phone,
            ];
        }

        return $data;
    }
}