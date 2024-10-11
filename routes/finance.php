<?php

use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AccountClassificationController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\StockTypeController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\DebtListController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Account Type
    Route::get('jenis_akun', [AccountTypeController::class, 'index'])->name('account_type');
    Route::get('account_type_datatables', [AccountTypeController::class, 'getDatatables']);
    Route::post('at_save', [AccountTypeController::class, 'storeData']);
    Route::post('at_delete', [AccountTypeController::class, 'deleteData']);
    Route::post('check_exists_account_type', [AccountTypeController::class, 'checkExistsAccountType']);

    // Account Klasifikasi
    Route::get('klasifikasi_akun', [AccountClassificationController::class, 'index'])->name('account_classification');
    Route::get('account_classification_datatables', [AccountClassificationController::class, 'getDatatables']);
    Route::post(
        'ac_save',
        [AccountClassificationController::class, 'storeData']
    );
    Route::post('ac_delete', [AccountClassificationController::class, 'deleteData']);
    Route::post('check_exists_account_classification', [AccountClassificationController::class, 'checkExistsAccountClassification']);

    // Account
    Route::get('data_akun', [AccountController::class, 'index'])->name('account');
    Route::get('account_datatables', [AccountController::class, 'getDatatables']);
    Route::post(
        'a_save',
        [AccountController::class, 'storeData']
    );
    Route::post('a_delete', [AccountController::class, 'deleteData']);
    Route::post('check_exists_account', [AccountController::class, 'checkExistsAccount']);
    Route::post('check_exists_account_code', [AccountController::class, 'checkExistsAccountCode']);

    // Tax
    Route::get('data_pajak', [TaxController::class, 'index'])->name('tax');
    Route::get('tax_datatables', [TaxController::class, 'getDatatables']);
    Route::post(
        'tx_save',
        [TaxController::class, 'storeData']
    );
    Route::post('tx_delete', [TaxController::class, 'deleteData']);
    Route::post('tx_import', [TaxController::class, 'importData']);
    Route::post('check_exists_tax', [TaxController::class, 'checkExistsTax']);

    // Stock Type
    Route::get('tipe_stok', [StockTypeController::class, 'index'])->name('stock_type');
    Route::get('stock_type_datatables', [StockTypeController::class, 'getDatatables']);
    Route::post('stkt_save', [StockTypeController::class, 'storeData']);
    Route::post('stkt_delete', [StockTypeController::class, 'deleteData']);
    Route::post('stkt_import', [StockTypeController::class, 'importData']);
    Route::post('check_exists_stock_type', [StockTypeController::class, 'checkExistsStockType']);

    // Payment Method
    Route::get('metode_pembayaran', [PaymentMethodController::class, 'index'])->name('payment_method');
    Route::get('payment_method_datatables', [PaymentMethodController::class, 'getDatatables']);
    Route::post(
        'pm_save',
        [PaymentMethodController::class, 'storeData']
    );
    Route::post('pm_delete', [PaymentMethodController::class, 'deleteData']);
    Route::post('pm_import', [PaymentMethodController::class, 'importData']);
    Route::post('check_exists_pm', [PaymentMethodController::class, 'checkExistsPm']);

    // Product Main Color
    Route::get('kurir_pengiriman', [CourierController::class, 'index'])->name('courier');
    Route::get('courier_datatables', [CourierController::class, 'getDatatables']);
    Route::post(
        'cr_save',
        [CourierController::class, 'storeData']
    );
    Route::post('cr_delete', [CourierController::class, 'deleteData']);

    // Debt List
    Route::get('daftar_hutang', [DebtListController::class, 'index'])->name('debt_list');
    Route::get('debt_list_datatables', [DebtListController::class, 'getDatatables']);
    Route::get('payment_datatables', [DebtListController::class, 'paymentDatatables']);
    Route::post('dl_save',
        [DebtListController::class, 'storeData']
    );
    Route::post('dl_delete', [DebtListController::class, 'deleteData']);
    Route::post('dlp_save', [DebtListController::class, 'storeDataPayment']);
    Route::post('dlp_delete', [DebtListController::class, 'deleteDataPayment']);
    Route::post('debt_import', [DebtListController::class, 'importData']);
});
