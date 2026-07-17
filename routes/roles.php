<?php

use App\Http\Controllers\Admin\RoleController;

Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
Route::get('/roles/create', [RoleController::class, 'create'])->name('admin.roles.create');
Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
Route::get('/roles/{role}', [RoleController::class, 'show'])->name('admin.roles.show');
Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
Route::put('/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
