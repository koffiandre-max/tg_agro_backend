<?php

namespace App\Http\Controllers\Api\V1\Technician;

use App\Http\Controllers\Api\ApiController;
use App\Models\Technician;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProfileController extends ApiController
{
    /**
     * GET /api/v1/technician/profile
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $user = $request->user('api');
            $technician = Technician::query()->where('user_id', $user->id)->first();

            return $this->success([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                ],
                'technician' => $technician ? [
                    'id' => $technician->id,
                    'phone_secondary' => $technician->phone_secondary,
                    'location_base' => $technician->location_base,
                    'is_available' => $technician->is_available,
                    'max_concurrent_missions' => $technician->max_concurrent_missions,
                    'current_workload' => $technician->current_workload,
                    'notes' => $technician->notes,
                ] : null,
            ], 'Profil récupéré.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération du profil.', 500, $e);
        }
    }

    /**
     * PUT /api/v1/technician/profile
     * body: name, phone, avatar (image), phone_secondary, location_base
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $user = $request->user('api');

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:15',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'phone_secondary' => 'nullable|string|max:20',
                'location_base' => 'nullable|string|max:255',
            ]);

            $userData = [];
            if (isset($validated['name'])) {
                $userData['name'] = $validated['name'];
            }
            if (isset($validated['phone'])) {
                $userData['phone'] = $validated['phone'];
            }
            if ($request->hasFile('avatar')) {
                $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            if ($userData) {
                $user->update($userData);
            }

            $technician = Technician::query()->where('user_id', $user->id)->first();
            $techData = array_filter([
                'phone_secondary' => $validated['phone_secondary'] ?? null,
                'location_base' => $validated['location_base'] ?? null,
            ], fn ($v) => $v !== null);

            if ($technician) {
                $technician->update($techData);
            }

            Log::channel(self::LOG_CHANNEL)->info('API - Profil mis à jour', [
                'user_id' => $user->id,
            ]);

            $user->refresh();
            if ($technician) {
                $technician->refresh();
            }

            return $this->success([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                ],
                'technician' => $technician ? [
                    'id' => $technician->id,
                    'phone_secondary' => $technician->phone_secondary,
                    'location_base' => $technician->location_base,
                    'is_available' => $technician->is_available,
                    'max_concurrent_missions' => $technician->max_concurrent_missions,
                    'current_workload' => $technician->current_workload,
                ] : null,
            ], 'Profil mis à jour avec succès.');
        } catch (ValidationException $e) {
            return $this->error('Données invalides.', 422, null, $e->errors());
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la mise à jour du profil.', 500, $e);
        }
    }
}