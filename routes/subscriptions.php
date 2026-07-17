<?php

use App\Http\Controllers\Admin\SubscriptionController;

Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
Route::get('/subscriptions/{subscription}/edit', [SubscriptionController::class, 'edit'])->name('admin.subscriptions.edit');
Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('admin.subscriptions.update');
