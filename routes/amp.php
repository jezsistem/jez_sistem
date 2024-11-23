<?php

use App\Http\Controllers\TransaksiOnlineController;
use App\Http\Controllers\ProductLocationSetupV2Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeliveryRecapController;


use App\Http\Controllers\SatuanPosV2Controller;
use App\Http\Controllers\BahanBakuPosV2Controller;
use App\Http\Controllers\DataUserPosV2Controller;

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
    Route::get('transaksi_online_datatables', [TransaksiOnlineController::class, 'getDatatables']);
    Route::get('transaksi_online_datatables_detail', [TransaksiOnlineController::class, 'detailDatatables']);
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
    Route::get('delivery_rec    ap', [DeliveryRecapController::class, 'index']);
    Route::get('delivery_recap_datatables', [DeliveryRecapController::class, 'getDatatables']);
    Route::get('add_delivery_recap', [DeliveryRecapController::class, 'add']);
});


