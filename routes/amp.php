<?php

use App\Http\Controllers\TransaksiOnlineController;
use App\Http\Controllers\ProductLocationSetupV2Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeliveryRecapController;


Route::middleware(['auth'])->group(function () {

    // Transaksi Online
    /**
     * TODO:
     * 1. Transaksi Online Get Data Tables
     * 2. Transaksi Online Import
     * 3. Automization Select Shopee / Tiktok Platform
     * 4. Export per period
     */
    Route::get('transaksi_online', [TransaksiOnlineController::class, 'index']);
    Route::get('transaksi_online_v2', [TransaksiOnlineController::class, 'indexUpdated'])->name('transaksi_online_v2');
    Route::get('transaksi_online_datatables', [TransaksiOnlineController::class, 'getDatatables']);
    Route::get('transaksi_online_datatables_simple', [TransaksiOnlineController::class, 'getDatatablesForSimple']);
    Route::get('transaksi_online_datatables_detail', [TransaksiOnlineController::class, 'detailDatatables']);
    Route::get('transaksi_online_datatables_detail_simple', [TransaksiOnlineController::class, 'detailDatatablesForSimple']);
    Route::post('transaksi_online_detail', [TransaksiOnlineController::class, 'detail']);
    Route::post('transaksi_online_import', [TransaksiOnlineController::class, 'importData']);
    Route::post('stock_location_import', [ProductLocationSetupV2Controller::class, 'importData']);
    Route::post('transaksi_online_delete', [TransaksiOnlineController::class, 'delete']);
    Route::post('print_online_invoice', [TransaksiOnlineController::class, 'cetak_invoice']);
    Route::get('print_online_nota/{orderNum}', [TransaksiOnlineController::class, 'cetak_nota'])->name('print_online_nota');

    // Rekap Resi
    /**
     * TODO:
     * 1. Transaksi Online Get Data Tables
     * 2. Rekap Resi Page insert
     * 3. Automization Select Shopee / Tiktok Platform
     * 4. Export per period
     */
    Route::get('delivery_recap', [DeliveryRecapController::class, 'index']);
    Route::get('delivery_recap_datatables', [DeliveryRecapController::class, 'getDatatables']);
    Route::get('add_delivery_recap', [DeliveryRecapController::class, 'add']);
});


