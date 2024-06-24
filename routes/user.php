<?php

use App\Http\Controllers\InvestorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // UserController
    Route::get('load_user_store', [UserController::class, 'loadStore']);
    Route::get('data_user', [UserController::class, 'index']);
    Route::get('user_datatables', [UserController::class, 'getDatatables']);
    Route::post('u_save', [UserController::class, 'storeData']);
    Route::post('u_delete', [UserController::class, 'deleteData']);
    Route::post('check_exists_secret_code', [UserController::class, 'checkExistsSecretCode']);
    Route::post('autocomplete_menu', [UserController::class, 'autocompleteMenu']);
    Route::post('autocomplete_store', [UserController::class, 'autocompleteStore']);
    Route::post('load_user_menu', [UserController::class, 'loadUserMenu']);


    // UserManagementController
    Route::get('user_management', [UserManagementController::class, 'index']);
    Route::get('um_datatables', [UserManagementController::class, 'getDatatables']);
    Route::post('um_save', [UserManagementController::class, 'storeData']);
    Route::post(
        'um_delete',
        [UserManagementController::class, 'deleteData']
    );

    // Investor
    Route::get('investor', [InvestorController::class, 'index']);
    Route::get('i_datatables', [InvestorController::class, 'getDatatables']);
    Route::post('i_save', [InvestorController::class, 'storeData']);
    Route::post('i_delete',
        [InvestorController::class, 'deleteData']
    );
    Route::post('i_username', [InvestorController::class, 'checkUsername']);
});
