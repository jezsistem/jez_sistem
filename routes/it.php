<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\MenuAccessTemplateController;
use App\Http\Controllers\MenuAccessTemplateDetailController;
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
    
    Route::prefix('menu-access-templates')->group(function () {
        Route::get('/', [MenuAccessTemplateController::class, 'index'])->name('menu.access.templates.index');
        Route::get('/create', [MenuAccessTemplateController::class, 'create'])->name('menu.access.templates.create');
        Route::post('/store', [MenuAccessTemplateController::class, 'store'])->name('menu.access.templates.store');
        Route::get('/edit/{id}', [MenuAccessTemplateController::class, 'edit'])->name('menu.access.templates.edit');
        Route::put('/update/{id}', [MenuAccessTemplateController::class, 'update'])->name('menu.access.templates.update');
        Route::delete('/delete/{id}', [MenuAccessTemplateController::class, 'destroy'])->name('menu.access.templates.destroy');
        Route::get('/datatable', [MenuAccessTemplateController::class, 'datatable'])->name('menu.access.templates.datatable');
        Route::get('/get-menu-accesses', [MenuAccessTemplateController::class, 'getMenuAccesses'])->name('menu.access.templates.get_menu_accesses');
    });

    Route::get('/ai-chat', [AIController::class, 'index']);
    Route::post('/ai-process', [AIController::class, 'chat']);
});
