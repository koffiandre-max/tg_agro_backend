<?php

use Illuminate\Support\Facades\Route;
use Modules\ResetPassword\Http\Controllers\ResetPasswordController;

// Flux "mot de passe oublié" — l'utilisateur n'est PAS connecté,
// donc ni middleware "auth" ni scoping business_id.
Route::middleware(['web'])
    ->prefix('reset-password')
    ->name('reset_password.')
    ->group(function () {
        Route::get('/', [ResetPasswordController::class, 'showLinkRequestForm'])->name('request');
        Route::post('/', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('email');
        Route::post('/reset', [ResetPasswordController::class, 'reset'])->name('store');
        Route::get('/{token}', [ResetPasswordController::class, 'showResetForm'])->name('reset');
    });

