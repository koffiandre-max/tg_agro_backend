<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Mobile\ChangelogController;
use App\Http\Controllers\Api\V1\Mobile\SyncController;
use App\Http\Controllers\Api\V1\Technician\ClientController;
use App\Http\Controllers\Api\V1\Technician\DashboardController;
use App\Http\Controllers\Api\V1\Technician\DataEntryController;
use App\Http\Controllers\Api\V1\Technician\FarmController;
use App\Http\Controllers\Api\V1\Technician\MissionController;
use App\Http\Controllers\Api\V1\Technician\PhotoController;
use App\Http\Controllers\Api\V1\Technician\ProfileController;
use App\Http\Controllers\Api\V1\Technician\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Mobile — v1 (TG Agro)
|--------------------------------------------------------------------------
| Authentification : POST /api/v1/auth/login → jeton Bearer.
| Changelog : synchronisation incrémentale pour l'application mobile.
| Espace technicien : protégé par le middleware api.technician.
*/

Route::prefix('v1')->middleware('api.log')->group(function () {

    // ── Authentification publique ──────────────────────────────
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');

    // ── Routes authentifiées par jeton API ─────────────────────
    Route::middleware('auth:api')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Architecture changelog / synchronisation mobile
        Route::prefix('/mobile')->group(function () {
            Route::get('/bootstrap', [ChangelogController::class, 'bootstrap']);
            Route::get('/changelog', [ChangelogController::class, 'index']);
            Route::post('/changelog/ack', [ChangelogController::class, 'acknowledge']);

            // Outbox : remontée des opérations réalisées hors-ligne
            Route::post('/sync', [SyncController::class, 'store']);
        });

        // ── Espace technicien mobile ───────────────────────────
        Route::middleware('api.technician')->prefix('/technician')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index']);

            Route::get('/missions', [MissionController::class, 'index']);
            Route::get('/missions/{mission}', [MissionController::class, 'show']);
            Route::patch('/missions/{mission}/status', [MissionController::class, 'updateStatus']);

            Route::get('/farms', [FarmController::class, 'index']);
            Route::get('/farms/{farm}', [FarmController::class, 'show']);

            Route::get('/clients', [ClientController::class, 'index']);
            Route::get('/clients/{client}', [ClientController::class, 'show']);

            Route::get('/data', [DataEntryController::class, 'index']);
            Route::post('/data', [DataEntryController::class, 'store']);
            Route::get('/data/{dataEntry}', [DataEntryController::class, 'show']);
            Route::put('/data/{dataEntry}', [DataEntryController::class, 'update']);
            Route::delete('/data/{dataEntry}', [DataEntryController::class, 'destroy']);

            Route::get('/reports', [ReportController::class, 'index']);
            Route::post('/reports', [ReportController::class, 'store']);
            Route::get('/reports/{report}/download', [ReportController::class, 'download']);

            Route::get('/photos', [PhotoController::class, 'index']);
            Route::post('/photos', [PhotoController::class, 'store']);

            Route::get('/profile', [ProfileController::class, 'show']);
            Route::put('/profile', [ProfileController::class, 'update']);
        });
    });
});