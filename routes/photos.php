<?php

use App\Http\Controllers\Admin\PhotoValidationController;

Route::get('/photos/validation', [PhotoValidationController::class, 'index'])->name('admin.photos.validation');
Route::post('/photos/{photo}/approve', [PhotoValidationController::class, 'approve'])->name('admin.photos.approve');
Route::post('/photos/{photo}/reject', [PhotoValidationController::class, 'reject'])->name('admin.photos.reject');
