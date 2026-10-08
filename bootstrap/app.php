<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
                $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'technician' => \App\Http\Middleware\TechnicianMiddleware::class,
            'client' => \App\Http\Middleware\ClientMiddleware::class,
            'admin_or_technician' => \App\Http\Middleware\AdminOrTechnicianMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'feature' => \App\Http\Middleware\CheckFeatureMiddleware::class,
            'verified' => \App\Http\Middleware\EnsureUserIsVerified::class,
            // API mobile
            'api.log' => \App\Http\Middleware\ApiLogRequests::class,
            'api.technician' => \App\Http\Middleware\ApiTechnicianMiddleware::class,
        ]);

        // Webhooks des passerelles de paiement (module Payment) : pas de CSRF.
        $middleware->validateCsrfTokens(except: [
            'payment/webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*'),
        );

        // API : jeton absent/invalide/révqué → 401 (doit précéder le catch-all Throwable)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => 'Non authentifié. Veuillez vous reconnecter.'], 401);
            }
            return null; // web : comportement par défaut (redirection vers login)
        });

        $exceptions->report(function (Throwable $e) {
            $path = storage_path('logs/exception-report.log');
            file_put_contents(
                $path,
                '[' . now()->toDateTimeString() . '] ' . get_class($e) . ' | ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString() . "\n\n",
                FILE_APPEND
            );
        });

        // Gérer ModelNotFoundException pour les routes {user} - rediriger vers 404
        $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Ressource non trouvée.'], 404);
            }
            return redirect()->back()->with('error', 'La ressource demandée est introuvable.');
        });

        // Page 404 personnalisée
        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Page non trouvée.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // Page 403 personnalisée
        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Accès interdit.'], 403);
            }
            return response()->view('errors.403', [], 403);
        });

        // Page 500 personnalisée
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                if (app()->hasDebugModeEnabled()) {
                    return response()->json([
                        'error' => 'Erreur serveur interne.',
                        'debug' => $e->getMessage(),
                        'file' => $e->getFile() . ':' . $e->getLine(),
                    ], 500);
                }
                return response()->json(['error' => 'Erreur serveur interne.'], 500);
            }
            // Only render custom view for 500 errors in production
            if (app()->isProduction()) {
                return response()->view('errors.500', [], 500);
            }
            // In development, let Laravel show the detailed error page
            return null;
        });

        // Page 419 personnalisée (CSRF token mismatch)
        $exceptions->render(function (Illuminate\Session\TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Session expirée. Veuillez réessayer.'], 419);
            }
            return response()->view('errors.419', [], 419);
        });
    })->create();
