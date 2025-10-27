<?php

use App\Http\Controllers\TransaksiOnlineController;
use App\Http\Controllers\ProductLocationSetupV2Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeliveryRecapController;
use App\Models\TransaksiOnline;
use App\Http\Controllers\PdfSplitController;

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
    Route::get('get_chat_history_online_transaction/{id}', [TransaksiOnlineController::class, 'getChatHistoryOnlineTransaction']);
    Route::post('send_chat_history_online_transaction', [TransaksiOnlineController::class, 'sendChatHistoryOnlineTransaction']);
    Route::post('transaksi_online_pick_items', [TransaksiOnlineController::class, 'pickItems']);
    Route::get('transaksi_online_get_items', [TransaksiOnlineController::class, 'getOnlineTransactionItems']);
    Route::post('transaksi_online_add_new_item', [TransaksiOnlineController::class, 'addNewItem']);
    Route::post('transaksi_online_delete_item', [TransaksiOnlineController::class, 'deleteItem']);
    Route::post('transaksi_online_edit_item', [TransaksiOnlineController::class, 'editItem']);

    // Rekap Resi
    /**
     * TODO:
     * 1. Transaksi Online Get Data Tables
     * 2. Rekap Resi Page insert
     * 3. Automization Select Shopee / Tiktok Platform
     * 4. Expor t per period
     */
    Route::get('delivery_recap', [DeliveryRecapController::class, 'index']);
    Route::get('delivery_recap_datatables', [DeliveryRecapController::class, 'getDatatables']);
    Route::get('add_delivery_recap', [DeliveryRecapController::class, 'add']);
    Route::post('/delivery-recaps/store', [DeliveryRecapController::class, 'store'])->name('delivery-recaps.store');
    Route::post('/signature-upload', [DeliveryRecapController::class, 'uploadSignature'])->name('signature.upload');


    Route::get('/pdf-import', [PdfSplitController::class, 'index'])->name('pdf.import');
    Route::post('/pdf-split', [PdfSplitController::class, 'split'])->name('pdf.split');
    Route::get('/split-resi/history', [PdfSplitController::class, 'getHistory'])->name('split.history.ajax');
});


