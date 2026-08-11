<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MarketPriceApiController;

Route::get('/api/market-prices/cote-divoire', [MarketPriceApiController::class, 'coteDIvoire'])
    ->name('api.market-prices.cote-divoire');

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    require __DIR__ . '/dashboard.php';
    require __DIR__ . '/admin.php';
    require __DIR__ . '/clients.php';
    require __DIR__ . '/technicians.php';
    require __DIR__ . '/farms.php';
    require __DIR__ . '/rapports-visite.php';
    require __DIR__ . '/reports.php';
    require __DIR__ . '/photos.php';
    require __DIR__ . '/data.php';
    require __DIR__ . '/market-prices.php';
    require __DIR__ . '/subscriptions.php';
    require __DIR__ . '/portail.php';
    require __DIR__ . '/technitian.php';
    require __DIR__ . '/users.php';
    require __DIR__ . '/roles.php';
    require __DIR__ . '/permissions.php';

    Route::get('/chat/messages', [App\Http\Controllers\Portail\MessageController::class, 'list'])->name('chat.messages.index');
    Route::post('/chat/messages', [App\Http\Controllers\Portail\MessageController::class, 'store'])->name('chat.messages.store');
    Route::post('/chat/messages/{message}/read', [App\Http\Controllers\Portail\MessageController::class, 'markRead'])->name('chat.messages.read');
});
