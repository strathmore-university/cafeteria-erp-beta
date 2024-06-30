<?php

use App\Events\Anthony;
use App\Http\Controllers\Downloads\DownloadCrn;
use App\Http\Controllers\Downloads\DownloadGRN;
use App\Http\Controllers\Downloads\DownloadPurchaseOrder;
use App\Http\Controllers\Retail\CreateRetailSession;
use App\Http\Middleware\EnsureHasRetailSession;
use App\Livewire\PosInterface;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::get('/dashboard', fn () => view('dashboard'))->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test', function (): void {
    Anthony::dispatch('Tendwa');
});

Route::middleware('auth')->group(function (): void {
    Route::get(
        '/download/{purchaseOrder}/purchase-order',
        DownloadPurchaseOrder::class
    )->name('download.purchase-order');

    Route::get(
        '/download/{grn}/grn',
        DownloadGRN::class
    )->name('download.grn');

    Route::get(
        '/download/{crn}/crn',
        DownloadCrn::class
    )->name('download.crn');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/retail/session/create', [CreateRetailSession::class, 'create'])
        ->name('retail.session.create');

    Route::post('/retail/session/create', CreateRetailSession::class)
        ->name('retail.session.create.post');

    Route::get('/pos', PosInterface::class)
        ->middleware(EnsureHasRetailSession::class)
        ->name('pos');
});

require __DIR__ . '/auth.php';
