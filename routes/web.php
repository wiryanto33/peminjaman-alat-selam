<?php

use App\Http\Controllers\PeminjamanInvoiceController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['auth'])->group(function () {
    Route::get('/peminjaman-alats/{peminjaman}/invoice', PeminjamanInvoiceController::class)
        ->name('peminjaman-alats.invoice');
});
