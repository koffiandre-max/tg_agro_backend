<?php

use App\Http\Controllers\Admin\DataValidationController;

Route::get('/data/validation', [DataValidationController::class, 'index'])->name('admin.data.validation');
Route::post('/data/{dataEntry}/validate', [DataValidationController::class, 'validate'])->name('admin.data.validate');
Route::post('/data/{dataEntry}/reject', [DataValidationController::class, 'reject'])->name('admin.data.reject');
