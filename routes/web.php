<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    require __DIR__ . '/auth.php';
});

Route::middleware('auth')->group(function () {
    require __DIR__ . '/dashboard.php';
    require __DIR__ . '/admin.php';
    require __DIR__ . '/clients.php';
    require __DIR__ . '/technicians.php';
    require __DIR__ . '/farms.php';
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
});
