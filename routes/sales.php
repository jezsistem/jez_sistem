<?php

use App\Http\Controllers\PointOfSaleController;
use App\Http\Controllers\InvoiceEditorController;
use App\Http\Controllers\NamesetDataController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\ProductDiscountController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PosSummaryController;
use App\Http\Controllers\CrossOrderController;
use App\Http\Controllers\InvoiceTrackingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TransaksiOnlineController;
use App\Http\Controllers\ProductLocationSetupV2Controller;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // POS 
    Route::get('point_of_sale', [PointOfSaleController::class, 'index'])->name('point_of_sale');
    Route::get('reload_refund', [PointOfSaleController::class, 'reloadRefund']);
    Route::get('reload_refund_offline', [PointOfSaleController::class, 'reloadRefundOffline']);
    Route::get('refund_retur_datatables', [PointOfSaleController::class, 'refundReturDatatables']);
    Route::post('refund_exchange_list', [PointOfSaleController::class, 'refundExchangeList']);
    Route::post('check_barcode', [PointOfSaleController::class, 'checkBarcode']);
    Route::post('check_barcode_by_waiting', [PointOfSaleController::class, 'checkBarcodeWaiting']);
    Route::post('reload_item_total', [PointOfSaleController::class, 'reloadItemTotal']);
    Route::post('update_ongkir', [PointOfSaleController::class, 'updateOngkir']);
    Route::post('save_transaction', [PointOfSaleController::class, 'saveTransaction']);
    Route::post('save_transaction_offline', [PointOfSaleController::class, 'saveTransactionOffline']);
    Route::post('save_transaction_detail', [PointOfSaleController::class, 'saveTransactionDetail']);
    Route::post('save_transaction_detail_offline', [PointOfSaleController::class, 'saveTransactionDetailOffline']);
    Route::post('autocomplete', [PointOfSaleController::class, 'fetch']);
    Route::post('autocomplete_by_waiting', [PointOfSaleController::class, 'fetchWaiting']);
    Route::post('autocomplete_invoice', [PointOfSaleController::class, 'fetchInvoice']);
    Route::post('autocomplete_invoice_offline', [PointOfSaleController::class, 'fetchInvoiceOffline']);
    Route::post('change_waiting_status', [PointOfSaleController::class, 'changeWaitingStatus']);
    Route::post('check_waiting_for_checkout', [PointOfSaleController::class, 'checkWaitingForCheckout']);
    Route::post('check_complaint', [PointOfSaleController::class, 'checkComplaint']);
    Route::post('check_offline_complaint', [PointOfSaleController::class, 'checkOfflineComplaint']);
    Route::post('autocomplete_refund_invoice', [PointOfSaleController::class, 'fetchRefundInvoice']);
    Route::post('add_custom_amount', [PointOfSaleController::class, 'addCustomAmount']);

    // InvoiceEditorController
    Route::get('invoice_editor', [InvoiceEditorController::class, 'index']);
    Route::get('ie_permission_datatables', [InvoiceEditorController::class, 'getPermissionDatatables']);
    Route::get('ie_permission_invoice_datatables', [InvoiceEditorController::class, 'getInvoiceDatatables']);
    Route::get('ie_permission_detail_datatables', [InvoiceEditorController::class, 'getDetailDatatables']);
    Route::get('ie_permission_tracking_datatables', [InvoiceEditorController::class, 'getTrackingDatatables']);
    Route::get('ie_permission_history_datatables', [InvoiceEditorController::class, 'getHistoryDatatables']);
    Route::post('ie_permission_save', [InvoiceEditorController::class, 'storePermissionData']);
    Route::post('ie_permission_delete', [InvoiceEditorController::class, 'deletePermissionData']);
    Route::post('ie_permission_invoice', [InvoiceEditorController::class, 'checkInvoice']);
    Route::post('ie_permission_check_active_edit', [InvoiceEditorController::class, 'checkActiveEdit']);
    Route::post('ie_permission_done_edit', [InvoiceEditorController::class, 'doneEdit']);
    Route::post('ie_permission_do_edit', [InvoiceEditorController::class, 'doEdit']);
    Route::post('ie_permission_cancel_item', [InvoiceEditorController::class, 'cancelItem']);
    Route::post('ie_permission_cancel_invoice', [InvoiceEditorController::class, 'cancelInvoice']);

    // Nameset Data
    Route::get('data_nameset', [NamesetDataController::class, 'index'])->name('nameset_data');
    Route::get('nameset_datatables', [NamesetDataController::class, 'getDatatables']);
    Route::post('update_data', [NamesetDataController::class, 'updateData']);

    // Target
    Route::get('target', [TargetController::class, 'index'])->name('target');
    Route::get('target_datatables', [TargetController::class, 'getDatatables']);
    Route::get('target_detail_datatables', [TargetController::class, 'getDetailDatatables']);
    Route::post('delete_sub_target', [TargetController::class, 'deleteSubTarget']);
    Route::post('tr_save', [TargetController::class, 'storeData']);
    Route::post('tr_delete', [TargetController::class, 'deleteData']);
    Route::post('check_exists_target', [TargetController::class, 'checkExistsTarget']);
    Route::post('sv_target_detail', [TargetController::class, 'saveTargetDetail']);
    Route::post('check_str', [TargetController::class, 'checkStr']);
    Route::post('edit_target', [TargetController::class, 'editTarget']);
    Route::post('sv_target_detail_import', [TargetController::class, 'saveTargetDetailImport']);

    // Product Discount
    Route::get('setup_diskon', [ProductDiscountController::class, 'index'])->name('product_discount');
    Route::get('product_discount_datatables', [ProductDiscountController::class, 'getDatatables']);
    Route::get('product_discount_detail_datatables', [ProductDiscountController::class, 'getDetailDatatables']);
    Route::get('product_discount_article_datatables', [ProductDiscountController::class, 'getArticleDatatables']);
    Route::post('pd_save', [ProductDiscountController::class, 'storeData']);
    Route::post('pd_delete', [ProductDiscountController::class, 'deleteData']);
    Route::post('add_item_to_discount', [ProductDiscountController::class, 'addItemToDiscount']);
    Route::post('delete_item_discount', [ProductDiscountController::class, 'deleteItemDiscount']);
    Route::post('discount_import', [ProductDiscountController::class, 'importData']);

    // Voucher
    Route::get('voucher', [VoucherController::class, 'index']);
    Route::get('voucher_datatables', [VoucherController::class, 'getDatatables']);
    Route::post('voucher_save', [VoucherController::class, 'storeData']);
    Route::post('voucher_delete', [VoucherController::class, 'deleteData']);
    Route::post('check_exists_voucher', [VoucherController::class, 'checkExistsVoucher']);

    // Pos Summary
    Route::get('pos_summary', [PosSummaryController::class, 'index'])->name('pos_summary');
    Route::get('pos_summary_online_datatables', [PosSummaryController::class, 'onlineDatatables']);
    Route::get('pos_summary_offline_datatables', [PosSummaryController::class, 'offlineDatatables']);
    Route::get('sales_detail_datatables', [PosSummaryController::class, 'salesDetailDatatables']);
    Route::get('sales_item_detail_datatables', [PosSummaryController::class, 'salesItemDetailDatatables']);
    Route::post('target_chart', [PosSummaryController::class, 'targetChart']);
    Route::post('cross_chart', [PosSummaryController::class, 'crossChart']);

    // Cross Order
    Route::get('cross_order', [CrossOrderController::class, 'index'])->name('cross');
    Route::get('cross_order_datatables', [CrossOrderController::class, 'getDatatables']);
    Route::get('confirmation_datatables', [CrossOrderController::class, 'confirmationDatatables']);
    Route::get('detail_datatables', [CrossOrderController::class, 'detailDatatables']);
    Route::get('take_confirmation_datatables', [CrossOrderController::class, 'takeConfirmationDatatables']);
    Route::get('cross_invoice/{invoice}', [CrossOrderController::class, 'printInvoice']);
    Route::get('check_cross_order', [CrossOrderController::class, 'checkCrossOrder']);
    Route::post('sv_cross_order', [CrossOrderController::class, 'saveData']);
    Route::post('sv_cross_status', [CrossOrderController::class, 'saveStatus']);
    Route::post('sv_cross_note', [CrossOrderController::class, 'saveNote']);
    Route::post('print_cross_invoice', [CrossOrderController::class, 'checkPrint']);
    Route::post('reload_cross_order_invoice', [CrossOrderController::class, 'reloadCrossOrderInvoice']);
    Route::post('get_cross_item_status', [CrossOrderController::class, 'getCrossItem']);

    // Invoice Tracking
    Route::get('invoice_tracking', [InvoiceTrackingController::class, 'index'])->name('invoice_tracking');
    Route::get('invoice_tracking_datatables', [InvoiceTrackingController::class, 'getDatatables']);
    Route::post('shipping_number_save', [InvoiceTrackingController::class, 'updateData']);
    Route::post('waybill_tracking', [InvoiceTrackingController::class, 'waybillTracking']);
    Route::post('all_waybill_tracking', [InvoiceTrackingController::class, 'allWaybillTracking']);
    Route::post('invoice_dp_repayment', [InvoiceTrackingController::class, 'invoiceDpRepayment']);

    // Invoice 
    Route::post('search_invoice', [InvoiceController::class, 'searchInvoice']);

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
    Route::post('transaksi_online_detail', [TransaksiOnlineController::class, 'detail']);
    Route::post('transaksi_online_import', [TransaksiOnlineController::class, 'importData']);
    Route::post('stock_location_import', [ProductLocationSetupV2Controller::class, 'importData']);
    Route::post('transaksi_online_delete', [TransaksiOnlineController::class, 'delete']);
});
