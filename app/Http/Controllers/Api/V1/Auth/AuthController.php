<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Models\ApiToken;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends ApiController
{
    /**
     * POST /api/v1/auth/login
     * Authentifie un utilisateur et retourne un jeton Bearer pour le mobile.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'string'],
                'device_name' => ['nullable', 'string', 'max:255'],
            ]);

            $user = User::query()->where('email', $validated['email'])->first();

            if (! $user || ! Hash::check($validated['password'], $user->password)) {
                Log::channel(self::LOG_CHANNEL)->warning('API - Échec de connexion', [
                    'email' => $validated['email'],
                    'ip' => $request->ip(),
                ]);

                return $this->unauthorized('Email ou mot de passe incorrect.');
            }

            if ($user->is_verified === false) {
                return $this->error(
                    'Votre compte n\'a pas encore été vérifié. Cliquez sur le lien reçu par email.',
                    403
                );
            }

            if ($user->is_active === false) {
                return $this->error('Votre compte est désactivé. Contactez l\'administrateur.', 403);
            }

            [$plainToken, $hashedToken] = ApiToken::generate();

            ApiToken::create([
                'user_id' => $user->id,
                'token' => $hashedToken,
                'device_name' => $validated['device_name'] ?? 'mobile',
                'expires_at' => now()->addYear(),
            ]);

            Log::channel(self::LOG_CHANNEL)->info('API - Connexion réussie', [
                'user_id' => $user->id,
                'role' => $user->role,
                'device_name' => $validated['device_name'] ?? 'mobile',
                'ip' => $request->ip(),
            ]);

            return $this->success([
                'token' => $plainToken,
                'token_type' => 'Bearer',
                'expires_at' => now()->addYear()->toIso8601String(),
                'user' => $this->userPayload($user),
            ], 'Connexion réussie.');
        } catch (ValidationException $e) {
            return $this->error('Données invalides.', 422, null, $e->errors());
        } catch (Throwable $e) {
            return $this->error('Une erreur est survenue lors de la connexion.', 500, $e);
        }
    }

    /**
     * GET /api/v1/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user('api');

            if (! $user) {
                return $this->unauthorized();
            }

            return $this->success($this->userPayload($user), 'Profil récupéré.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la récupération du profil.', 500, $e);
        }
    }

    /**
     * POST /api/v1/auth/logout
     * Révoque le jeton courant.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $rawToken = $request->bearerToken();

            if ($rawToken) {
                ApiToken::query()
                    ->where('token', hash('sha256', $rawToken))
                    ->delete();
            }

            Log::channel(self::LOG_CHANNEL)->info('API - Déconnexion', [
                'user_id' => $request->user('api')?->id,
            ]);

            return $this->success(null, 'Déconnecté avec succès.');
        } catch (Throwable $e) {
            return $this->error('Erreur lors de la déconnexion.', 500, $e);
        }
    }

    /**
     * Structure utilisateur retournée au client mobile.
     */
    protected function userPayload(User $user): array
    {
        $technicianId = null;

        if ($user->role === 'technician') {
            $technicianId = Technician::query()->where('user_id', $user->id)->value('id');
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'role' => $user->role,
            'technician_id' => $technicianId,
            'is_active' => $user->is_active,
            'is_verified' => $user->is_verified,
        ];
    }
}