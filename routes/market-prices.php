<?php

use App\Http\Controllers\Admin\MarketPriceController;

Route::get('/market-prices', [MarketPriceController::class, 'index'])->name('admin.market-prices.index');
Route::get('/market-prices/create', [MarketPriceController::class, 'create'])->name('admin.market-prices.create');
Route::post('/market-prices', [MarketPriceController::class, 'store'])->name('admin.market-prices.store');
Route::get('/market-prices/{marketPrice}/edit', [MarketPriceController::class, 'edit'])->name('admin.market-prices.edit');
Route::put('/market-prices/{marketPrice}', [MarketPriceController::class, 'update'])->name('admin.market-prices.update');
Route::delete('/market-prices/{marketPrice}', [MarketPriceController::class, 'destroy'])->name('admin.market-prices.destroy');
