<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Module Payment — Routes
|--------------------------------------------------------------------------
|
| Espace admin : abonnement, offres, paiements (carte / mobile money),
| renouvellement automatique. Les routes nécessitent un utilisateur
| connecté et la feature "payment".
|
*/

Route::middleware(['web', 'auth', 'feature:payment'])
    ->prefix('admin/payment')
    ->name('admin.payment.')
    ->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::post('/subscribe/{plan}', [PaymentController::class, 'subscribe'])->name('subscribe');
        Route::post('/auto-renew', [PaymentController::class, 'toggleAutoRenew'])->name('auto_renew');
        Route::post('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
        Route::post('/resume', [PaymentController::class, 'resume'])->name('resume');

        // Suivi & retour passerelle (callback navigateur).
        Route::get('/payments/{payment}/status', [PaymentController::class, 'paymentStatus'])
            ->name('payments.status');
        Route::get('/payments/{payment}/callback', [PaymentController::class, 'callback'])
            ->name('payments.callback');

        // Passerelle de simulation (dev local) — URLs signées.
        Route::get('/simulate/{payment}', [PaymentController::class, 'simulateShow'])
            ->name('simulate.show')->middleware('signed');
        Route::post('/simulate/{payment}/success', [PaymentController::class, 'simulateSuccess'])
            ->name('simulate.success')->middleware('signed');
        Route::post('/simulate/{payment}/failure', [PaymentController::class, 'simulateFailure'])
            ->name('simulate.failure')->middleware('signed');
    });

/*
 * Webhook serveur-à-serveur des passerelles : ni auth ni CSRF
 * (exclu dans bootstrap/app.php). Identifie la transaction par référence.
 */
Route::post('/payment/webhook/{provider}', [PaymentController::class, 'webhook'])
    ->middleware('web')
    ->name('payment.webhook');

