<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MarketPriceApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Portail\MessageController;

// Public routes — no authentication required
Route::get('/api/market-prices/cote-divoire', [MarketPriceApiController::class, 'coteDIvoire'])
    ->name('api.market-prices.cote-divoire');

Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes — only for unauthenticated users
Route::middleware(['guest', 'throttle:100,60'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Reset password — "mot de passe oublié"
    Route::get('/mot-de-passe-oublie', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/mot-de-passe-oublie/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/mot-de-passe-oublie/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.store');

    // Email verification
    Route::get('/verify-email/{token}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/resend-verification-email', [VerificationController::class, 'resend'])->name('verification.resend');
});

// Authenticated routes — logout + dashboard + chat (role-agnostic)
Route::middleware(['auth', 'verified', 'throttle:100,60'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/session/refresh', [AuthController::class, 'refresh'])->name('session.refresh');

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
Route::middleware(['auth', 'admin', 'throttle:100,60'])->group(function () {
    require __DIR__ . '/admin.php';

    require __DIR__ . '/technicians.php';
    require __DIR__ . '/reports.php';
    require __DIR__ . '/photos.php';
    require __DIR__ . '/data.php';
    require __DIR__ . '/market-prices.php';
    require __DIR__ . '/subscriptions.php';
    require __DIR__ . '/users.php';
    require __DIR__ . '/roles.php';
    require __DIR__ . '/permissions.php';
});

Route::middleware(['auth', 'throttle:100,60'])->group(function () {
    require __DIR__ . '/farms.php';
});

// ─────────────────────────────────────────────
// Shared admin + technician routes (visit reports, gallery)
// ─────────────────────────────────────────────
Route::middleware(['auth', 'admin_or_technician', 'throttle:100,60'])->group(function () {
    require __DIR__ . '/rapports-visite.php';
    require __DIR__ . '/gallery.php';
    require __DIR__ . '/clients.php';
});

// ─────────────────────────────────────────────
// Technician-only routes
// ─────────────────────────────────────────────
Route::middleware(['auth', 'throttle:100,60'])->group(function () {
    require __DIR__ . '/technitian.php';
});

// ─────────────────────────────────────────────
// Client (portail) routes
// ─────────────────────────────────────────────
Route::middleware(['auth', 'client', 'throttle:100,60'])->group(function () {
    require __DIR__ . '/portail.php';
});
