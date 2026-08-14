<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MarketPriceApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Portail\MessageController;

// Public routes — no authentication required
Route::get('/api/market-prices/cote-divoire', [MarketPriceApiController::class, 'coteDIvoire'])
    ->name('api.market-prices.cote-divoire');


// Guest routes — only for unauthenticated users
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes — logout + dashboard + chat (role-agnostic)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard is role-agnostic — the controller redirects to the right dashboard
    require __DIR__ . '/dashboard.php';

    // Chat / messagerie — accessible to all authenticated users
    Route::get('/chat/messages', [MessageController::class, 'list'])->name('chat.messages.index');
    Route::post('/chat/messages', [MessageController::class, 'store'])->name('chat.messages.store');
    Route::post('/chat/messages/{message}/read', [MessageController::class, 'markRead'])->name('chat.messages.read');
});

// ─────────────────────────────────────────────
// Admin-only routes
// ─────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {
    require __DIR__ . '/admin.php';
    require __DIR__ . '/clients.php';
    require __DIR__ . '/technicians.php';
    require __DIR__ . '/farms.php';
    require __DIR__ . '/reports.php';
    require __DIR__ . '/photos.php';
    require __DIR__ . '/data.php';
    require __DIR__ . '/market-prices.php';
    require __DIR__ . '/subscriptions.php';
    require __DIR__ . '/users.php';
    require __DIR__ . '/roles.php';
    require __DIR__ . '/permissions.php';
});

// ─────────────────────────────────────────────
// Shared admin + technician routes (visit reports)
// ─────────────────────────────────────────────
Route::middleware(['auth', 'admin_or_technician'])->group(function () {
    require __DIR__ . '/rapports-visite.php';
});

// ─────────────────────────────────────────────
// Technician-only routes
// ─────────────────────────────────────────────
Route::middleware(['auth', 'technician'])->group(function () {
    require __DIR__ . '/technitian.php';
});

// ─────────────────────────────────────────────
// Client (portail) routes
// ─────────────────────────────────────────────
Route::middleware(['auth', 'client'])->group(function () {
    require __DIR__ . '/portail.php';
});
