<?php

use App\Http\Controllers\Admin\ClientController;

Route::get('/clients', [ClientController::class, 'index'])->name('admin.clients.index');
Route::get('/clients/create', [ClientController::class, 'create'])->name('admin.clients.create');
Route::post('/clients', [ClientController::class, 'store'])->name('admin.clients.store');
Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('admin.clients.edit');
Route::get('/clients/{client}/farms', [ClientController::class, 'farms'])->name('admin.clients.farms');
Route::get('/clients/{client}', [ClientController::class, 'show'])->name('admin.clients.show');
Route::put('/clients/{client}', [ClientController::class, 'update'])->name('admin.clients.update');
Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('admin.clients.destroy');
