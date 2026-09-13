<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Contrôleur de base des API mobiles.
 *
 * Normalise les réponses JSON ({ success, message, data }) et centralise
 * la journalisation des exceptions dans le canal `api` pour faciliter
 * le débogage.
 */
abstract class ApiController extends Controller
{
    public const LOG_CHANNEL = 'api';

    protected function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function created(mixed $data = null, string $message = 'Créé avec succès.'): JsonResponse
    {
        return $this->success($data, $message, 201);
    }

    protected function error(string $message, int $status = 400, ?Throwable $exception = null, array $errors = []): JsonResponse
    {
        if ($exception) {
            $this->logException($exception);
        }

        $body = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $body['errors'] = $errors;
        }

        if ($exception && app()->hasDebugModeEnabled()) {
            $body['debug'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        return response()->json($body, $status);
    }

    protected function notFound(string $message = 'Ressource non trouvée.'): JsonResponse
    {
        return $this->error($message, 404);
    }

    protected function unauthorized(string $message = 'Non autorisé.'): JsonResponse
    {
        return $this->error($message, 401);
    }

    protected function forbidden(string $message = 'Accès interdit.'): JsonResponse
    {
        return $this->error($message, 403);
    }

    /**
     * Journalise une exception avec le contexte maximal dans le canal `api`.
     */
    protected function logException(Throwable $e, string $context = ''): void
    {
        Log::channel(self::LOG_CHANNEL)->error('API - Exception' . ($context ? ' [' . $context . ']' : ''), [
            'message' => $e->getMessage(),
            'class' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'user_id' => auth('api')->id(),
        ]);
    }
}