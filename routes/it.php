<?php

use App\Http\Controllers\ModalLockConfigController;
use App\Http\Controllers\ModalLockAllowedModelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ModalLockController;
use App\Http\Controllers\UserActivityController;

Route::middleware(['auth'])->group(function () {
    // Modal Lock
    Route::prefix('modal_lock')->group(function () {
        Route::get('/', [ModalLockController::class, 'index']);
        Route::post('/save', [ModalLockController::class, 'storeData']);
        Route::get('/datatables', [ModalLockController::class, 'getDatatables']);
        Route::post('/delete', [ModalLockController::class, 'deleteData']);

        Route::prefix('config')->group(function () {
            Route::get('/', [ModalLockConfigController::class, 'getConfigs']);
            Route::post('/save', [ModalLockConfigController::class, 'storeConfig']);
            Route::get('/edit/{id}', [ModalLockConfigController::class, 'getConfigForEdit']);
            Route::put('/update/{id}', [ModalLockConfigController::class, 'updateConfig']);
            Route::delete('/delete/{id}', [ModalLockConfigController::class, 'deleteConfig']);
            Route::get('/datatables', [ModalLockConfigController::class, 'getConfigDatatables']);
        });

        Route::prefix('allowed_models')->group(function () {
            Route::get('/', [ModalLockAllowedModelController::class, 'getAllowedModels']);
            Route::post('/save', [ModalLockAllowedModelController::class, 'storeAllowedModel']);
            Route::get('/edit/{id}', [ModalLockAllowedModelController::class, 'getAllowedModelForEdit']);
            Route::put('/update/{id}', [ModalLockAllowedModelController::class, 'updateAllowedModel']);
            Route::delete('/delete/{id}', [ModalLockAllowedModelController::class, 'deleteAllowedModel']);
            Route::get('/datatables', [ModalLockAllowedModelController::class, 'getAllowedModelsDatatables']);
        });
    });

    Route::prefix('user_activity_log')->group(function () {
        Route::get('/', [UserActivityController::class, 'index']);
    });
    
});
