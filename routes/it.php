<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ModalLockController;

Route::middleware(['auth'])->group(function () {
    // Modal Lock
    Route::prefix('modal_lock')->group(function () {
        Route::get('/', [ModalLockController::class, 'index']);
        Route::post('/save', [ModalLockController::class, 'storeData']);
        Route::get('/datatables', [ModalLockController::class, 'getDatatables']);
        Route::post('/delete', [ModalLockController::class, 'deleteData']);
    });
    
});
