<?php

use App\Http\Controllers\Admin\PermissionController;

Route::get('/permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
Route::get('/permissions/create', [PermissionController::class, 'create'])->name('admin.permissions.create');
Route::post('/permissions', [PermissionController::class, 'store'])->name('admin.permissions.store');
Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('admin.permissions.show');
Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('admin.permissions.edit');
Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('admin.permissions.update');
Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('admin.permissions.destroy');
