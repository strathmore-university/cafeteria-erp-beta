<?php

use App\Http\Controllers\Downloads\DownloadPurchaseOrder;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::get('/dashboard', fn () => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get(
        '/download/{purchaseOrder}/purchase-order',
        DownloadPurchaseOrder::class
    )->name('download.purchase-order');
});

require __DIR__ . '/auth.php';
