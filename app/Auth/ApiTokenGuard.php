<?php

namespace App\Auth;

use App\Models\ApiToken;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Garde d'authentification pour l'API mobile.
 *
 * Lit le jeton de la requête (en-tête `Authorization: Bearer <token>`
 * ou paramètre `api_token` pour les téléversements) puis résout l'utilisateur
 * depuis la table `api_tokens` (token stocké en sha256).
 */
class ApiTokenGuard implements Guard
{
    use GuardHelpers;

    public const HASH_ALGO = 'sha256';

    protected Request $request;

    protected ?ApiToken $tokenRecord = null;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    public function user(): ?Authenticatable
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $rawToken = $this->getTokenFromRequest();

        if ($rawToken === null || $rawToken === '') {
            return null;
        }

        $record = ApiToken::query()
            ->where('token', hash(self::HASH_ALGO, $rawToken))
            ->with('user')
            ->first();

        if (! $record || ! $record->user) {
            Log::channel('api')->warning('API - Jeton invalide ou introuvable', [
                'ip' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
                'device_name' => $this->request->input('device_name'),
            ]);

            return null;
        }

        if ($record->expires_at?->isPast()) {
            Log::channel('api')->warning('API - Jeton expiré', [
                'user_id' => $record->user_id,
                'expires_at' => $record->expires_at->toIso8601String(),
            ]);

            return null;
        }

        // Mise à jour périodique de "last_used_at" (au maximum une fois / minute).
        if ($record->last_used_at === null || $record->last_used_at->diffInSeconds(now()) > 60) {
            $record->forceFill(['last_used_at' => now()])->save();
        }

        $this->tokenRecord = $record;
        $this->user = $record->user;

        return $this->user;
    }

    public function setUser(Authenticatable $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Non utilisé pour ce type de garde (l'authentification se fait par jeton).
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    public function currentToken(): ?ApiToken
    {
        return $this->tokenRecord;
    }

    protected function getTokenFromRequest(): ?string
    {
        $header = $this->request->header('Authorization', '');

        if (preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
            return trim($matches[1]);
        }

        return $this->request->input('api_token');
    }
}