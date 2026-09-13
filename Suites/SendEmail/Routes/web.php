<?php

use Illuminate\Support\Facades\Route;
use Modules\SendEmail\Http\Controllers\SendEmailController;

/*
|--------------------------------------------------------------------------
| Module SendEmail — Routes
|--------------------------------------------------------------------------
|
| Envoi d'emails : composition, historique et relance des échecs.
|
*/

Route::middleware(['web', 'auth', 'feature:send_email'])
    ->prefix('admin/send_email')
    ->name('admin.send_email.')
    ->group(function () {
        Route::get('/', [SendEmailController::class, 'index'])->name('index');
        Route::post('/', [SendEmailController::class, 'store'])->name('store');
        Route::get('/emails/{emailLog}', [SendEmailController::class, 'show'])->name('show');
        Route::post('/emails/{emailLog}/resend', [SendEmailController::class, 'resend'])->name('resend');
    });
