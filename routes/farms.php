<?php

use App\Http\Controllers\Admin\FarmController;
use Illuminate\Support\Facades\Route;

Route::get('/farms', [FarmController::class, 'index'])->name('admin.farms.index');
Route::get('/farms/create', [FarmController::class, 'create'])->name('admin.farms.create');
Route::post('/farms', [FarmController::class, 'store'])->name('admin.farms.store');

// Farms Livewire Datatable (must be before /farms/{farm} to avoid route conflict)
Route::get('/farms/datatable', function () {
    return view('admin.farms.datatable');
})->name('admin.farms.datatable');

Route::get('/farms/culture', function () {
    return view('admin.farms.datatable', ['type' => 'culture']);
})->name('admin.farms.culture');

Route::get('/farms/elevage', function () {
    return view('admin.farms.datatable', ['type' => 'elevage']);
})->name('admin.farms.elevage');

Route::get('/farms/{farm}', [FarmController::class, 'show'])->name('admin.farms.show');
Route::get('/farms/{farm}/pdf', [FarmController::class, 'pdf'])->name('admin.farms.pdf');
Route::get('/farms/{farm}/edit', [FarmController::class, 'edit'])->name('admin.farms.edit');
Route::put('/farms/{farm}', [FarmController::class, 'update'])->name('admin.farms.update');
Route::delete('/farms/{farm}', [FarmController::class, 'destroy'])->name('admin.farms.destroy');
