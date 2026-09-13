<?php

namespace App\Http\Controllers\Api\V1\Technician;

use App\Http\Controllers\Api\ApiController;
use App\Models\Client;
use App\Models\Farm;
use App\Models\Technician;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClientController extends ApiController
{
    /**
     * GET /api/v1/technician/clients
     * Clients assignés au technicien ou rattachés à ses exploitations.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user('api');
            $technician = Technician::query()->where('user_id', $user->id)->first();

            if (! $technician) {
                return $this->error('Aucun profil technicien associé à ce compte.', 404);
            }

            $farmIds = Farm::query()->where('assigned_technician_id', $technician->id)->pluck('id');

            $query = Client::query()->with('user:id,name,email,phone,avatar')
                ->where('assigned_technician_id', $technician->id)
                ->orWhereIn('user_id', function ($q) use ($farmIds) {
                    $q->select('user_id')->from('farms')->whereIn('id', $farmIds);
                })
                ->orWhereHas('assignedFarms', fn ($q) => $q->whereIn('farms.id', $farmIds))
                ->distinct()
                ->orderBy('code');

            $clients = $query->get()->map(fn (Client $c) => $this->format($c))->all();

            return $this->success($clients, 'Clients récupérés.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération des clients.', 500, $e);
        }
    }

    /**
     * GET /api/v1/technician/clients/{client}
     */
    public function show(Request $request, int $client): JsonResponse
    {
        try {
            $user = $request->user('api');
            $technician = Technician::query()->where('user_id', $user->id)->first();

            if (! $technician) {
                return $this->error('Aucun profil technicien associé à ce compte.', 404);
            }

            $farmIds = Farm::query()->where('assigned_technician_id', $technician->id)->pluck('id');

            $model = Client::query()->with(['user:id,name,email,phone,avatar', 'farms:id,name,location'])
                ->where('id', $client)
                ->where(function ($q) use ($technician, $farmIds) {
                    $q->where('assigned_technician_id', $technician->id)
                        ->orWhereIn('user_id', function ($s) use ($farmIds) {
                            $s->select('user_id')->from('farms')->whereIn('id', $farmIds);
                        })
                        ->orWhereHas('assignedFarms', fn ($s) => $s->whereIn('farms.id', $farmIds));
                })
                ->first();

            if (! $model) {
                return $this->notFound('Client non trouvé ou non autorisé.');
            }

            return $this->success([
                ...$this->format($model),
                'farms' => $model->farms->map(fn (Farm $f) => [
                    'id' => $f->id,
                    'name' => $f->name,
                    'location' => $f->location,
                    'status' => $f->status,
                ])->all(),
            ], 'Client récupéré.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération du client.', 500, $e);
        }
    }

    protected function format(Client $c): array
    {
        return [
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
        ];
    }
}