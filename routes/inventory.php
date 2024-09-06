<?php

use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\B1g1Controller;
use App\Http\Controllers\ExceptionLocationController;
use App\Http\Controllers\InstockApprovalController;
use App\Http\Controllers\InstockListController;
use App\Http\Controllers\MassAdjustmentController;
use App\Http\Controllers\ProductLocationController;
use App\Http\Controllers\ProductLocationSetupController;
use App\Http\Controllers\ProductLocationSetupV2Controller;
use App\Http\Controllers\QtyExceptionController;
use App\Http\Controllers\ScanAdjustmentController;
use App\Http\Controllers\StockDataController;
use App\Http\Controllers\StockTrackingController;
use App\Http\Controllers\StoreAgingController;
use App\Http\Controllers\MarketplaceManagerController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\StockTransferDataController;
use App\Http\Controllers\PointOfSaleController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Product Location
    Route::get('lokasi_simpan', [ProductLocationController::class, 'index'])->name('product_location');
    Route::get('product_location_datatables', [ProductLocationController::class, 'getDatatables']);
    Route::post('pl_save', [ProductLocationController::class, 'storeData']);
    Route::post('pl_delete', [ProductLocationController::class, 'deleteData']);
    Route::post('pl_import', [ProductLocationController::class, 'importData']);
    Route::post('pl_code_check_data', [ProductLocationController::class, 'checkCode']);


    // StoreAgingController
    Route::get(
        'store_aging',
        [StoreAgingController::class, 'index']
    );
    Route::get('sta_datatables', [StoreAgingController::class, 'getDatatables']);
    Route::get('sta_detail_datatables', [StoreAgingController::class, 'getDetailDatatables']);
    Route::get('oca_datatables', [StoreAgingController::class, 'getOCADatatables']);
    Route::post('sta_save', [StoreAgingController::class, 'storeData']);
    Route::post(
        'sta_delete',
        [StoreAgingController::class, 'deleteData']
    );
    Route::post('stas_save', [StoreAgingController::class, 'storeDetailData']);
    Route::post(
        'stas_delete',
        [StoreAgingController::class, 'deleteDetailData']
    );
    Route::post(
        'sta_checked',
        [StoreAgingController::class, 'updateChecked']
    );
    Route::post('oca_save', [StoreAgingController::class, 'storeOCAData']);
    Route::post(
        'oca_delete',
        [StoreAgingController::class, 'deleteOCAData']
    );

    // InstockApprovalController
    Route::get('instock_approval', [InstockApprovalController::class, 'index']);
    Route::get('ia_datatables', [InstockApprovalController::class, 'getDatatables']);
    Route::post('ia_save', [InstockApprovalController::class, 'storeData']);
    Route::post('ia_delete', [InstockApprovalController::class, 'deleteData']);

    // InstockListController
    Route::get(
        'instock_list',
        [InstockListController::class, 'index']
    );
    Route::get('il_datatables', [InstockListController::class, 'getDatatables']);
    Route::get('il_history_datatables', [InstockListController::class, 'getHistoryDatatables']);
    Route::post('il_save', [InstockListController::class, 'storeData']);

    // mass adjusmtnet
    Route::get('mass_adjustment', [MassAdjustmentController::class, 'index']);
    Route::get('mass_stock_datatables', [MassAdjustmentController::class, 'stockDatatables']);
    Route::get('mass_adjustment_datatables', [MassAdjustmentController::class, 'adjustmentDatatables']);
    Route::get('mass_adjustment_detail_datatables', [MassAdjustmentController::class, 'adjustmentDetailDatatables']);
    Route::post('export_mass_table', [MassAdjustmentController::class, 'exportTable']);
    Route::post('load_mass_asset', [MassAdjustmentController::class, 'loadAsset']);
    Route::post('load_mass_adjustment_location', [MassAdjustmentController::class, 'loadLocation']);
    Route::post('load_mass_approval', [MassAdjustmentController::class, 'loadApproval']);
    Route::get('export_mass_adjustment_template', [MassAdjustmentController::class, 'exportData']);
    Route::post('import_mass_adjustment_template', [MassAdjustmentController::class, 'importData']);
    Route::post('export_mass_adjustment_result', [MassAdjustmentController::class, 'exportResult']);
    Route::post('mass_adjustment_approval', [MassAdjustmentController::class, 'approvalData']);
    Route::post('mass_adjustment_exec', [MassAdjustmentController::class, 'execData']);
    Route::post('mass_stock_datatables_filter', [MassAdjustmentController::class, 'adjustmentDatatablesFilter']);

    // ScanAdjustmentController
    Route::get('scan_adjustment', [ScanAdjustmentController::class, 'index']);
    Route::get('scan_adjustment_datatables', [ScanAdjustmentController::class, 'getDatatables']);
    Route::get('start_scan_adjustment/{id}', [ScanAdjustmentController::class, 'scanPanel']);
    Route::get('start_scan_adjustment_datatables', [ScanAdjustmentController::class, 'getScanDatatables']);
    Route::get('scan_adjustment_product_datatables', [ScanAdjustmentController::class, 'getProductDatatables']);
    Route::get('scan_adjustment_custom_datatables', [ScanAdjustmentController::class, 'getCustomDatatables']);
    Route::post('scan_adjustment_barcode_update', [ScanAdjustmentController::class, 'updateBarcode']);
    Route::post('sa_save', [ScanAdjustmentController::class, 'storeData']);
    Route::post('sa_delete', [ScanAdjustmentController::class, 'deleteData']);
    Route::post('scan_adjustment_approval', [ScanAdjustmentController::class, 'approvalData']);
    Route::post('scan_adjustment_finish', [ScanAdjustmentController::class, 'finishData']);
    Route::post('scan_adjustment_barcode', [ScanAdjustmentController::class, 'doScan']);
    Route::get('scan_adjustment_export', [ScanAdjustmentController::class, 'doExport']);
    Route::post('scan_adjustment_qty', [ScanAdjustmentController::class, 'loadQty']);
    Route::post('scan_adjustment_reset', [ScanAdjustmentController::class, 'doReset']);
    Route::post('scan_adjustment_sync', [ScanAdjustmentController::class, 'doSync']);
    Route::post('fetch_start_scan_adjustment_bin', [ScanAdjustmentController::class, 'fetchBIN']);
    Route::post('do_start_scan_adjustment', [ScanAdjustmentController::class, 'doScan']);
    Route::post('reset_start_scan_adjustment', [ScanAdjustmentController::class, 'resetScan']);
    Route::post('fetch_start_scan_adjustment_brand', [ScanAdjustmentController::class, 'fetchBrand']);
    Route::post('fetch_start_scan_adjustment_sub_category', [ScanAdjustmentController::class, 'fetchSubCategory']);
    Route::post('fetch_start_scan_adj ustment_article', [ScanAdjustmentController::class, 'fetchArticle']);
    Route::post('min_plus_start_scan_adjustment', [ScanAdjustmentController::class, 'minPlus']);
    Route::post('scan_adjustment_manual', [ScanAdjustmentController::class, 'manual']);
    Route::post('fetch_start_scan_adjustment_article_barcode', [ScanAdjustmentController::class, 'fetchArticleBarcode']);
    Route::post('fetch_start_scan_adjustment_custom', [ScanAdjustmentController::class, 'fetchCustom']);
    Route::post('add_start_scan_adjustment_custom', [ScanAdjustmentController::class, 'addCustom']);
    Route::post('delete_start_scan_adjustment_custom', [ScanAdjustmentController::class, 'deleteCustom']);
    Route::get('export_start_scan_adjustment_bin', [ScanAdjustmentController::class, 'exportBIN']);
    Route::post('scan_adjustment_qty_update', [ScanAdjustmentController::class, 'updateQty']);
    Route::post('pos_barcode_scan', [PointOfSaleController::class, 'scanBarcode']);

    // Adjustment
    Route::get('adjustment', [AdjustmentController::class, 'index'])->name('adjustment');
    Route::get(
        '_datatables',
        [AdjustmentController::class, 'historyDatatables']
    );
    Route::get('validated_datatables', [AdjustmentController::class, 'validatedDatatables']);
    Route::get('not_validated_datatables', [AdjustmentController::class, 'notValidatedDatatables']);
    Route::get('adjustment_history_datatables', [AdjustmentController::class, 'adjustmentHistoryDatatables']);
    Route::get('article_adjustment_datatables', [AdjustmentController::class, 'articleDatatables']);
    Route::get('reload_location', [AdjustmentController::class, 'reloadLocation']);
    Route::post('sv_adjustment', [AdjustmentController::class, 'productAdjustment']);
    Route::post('finish_adjustment', [AdjustmentController::class, 'finishAdjustment']);
    Route::post('add_article_adjustment', [AdjustmentController::class, 'addArticle']);
    Route::post('autocomplete_article', [AdjustmentController::class, 'fetchArticle']);

    // Exception Location
    Route::get('exception_location', [ExceptionLocationController::class, 'index']);
    Route::get('exception_location_datatables', [ExceptionLocationController::class, 'getDatatables']);
    Route::post('el_save', [ExceptionLocationController::class, 'storeData']);
    Route::post('el_delete', [ExceptionLocationController::class, 'deleteData']);


    // B1G1 Location
    Route::get('b1g1_location', [B1g1Controller::class, 'index']);
    Route::get('b1g1_location_datatables', [B1g1Controller::class, 'getDatatables']);
    Route::post('b1g1_save', [B1g1Controller::class, 'storeData']);
    Route::post(
        'b1g1_delete',
        [B1g1Controller::class, 'deleteData']
    );
    Route::post(
        'b1g1_update',
        [B1g1Controller::class, 'updateData']
    );

    // Qty Exception
    Route::get('qty_exception', [QtyExceptionController::class, 'index']);
    Route::get('qe_datatables', [QtyExceptionController::class, 'getDatatables']);
    Route::post('qe_save', [QtyExceptionController::class, 'storeData']);
    Route::post('qe_delete', [QtyExceptionController::class, 'deleteData']);

    // Product Location Setup
    Route::get('setup_lokasi_stok', [ProductLocationSetupController::class, 'index'])->name('product_location_setup');
    Route::get('product_in_location_datatables', [ProductLocationSetupController::class, 'getDatatablesLocation']);
    Route::get('product_location_setup_datatables', [ProductLocationSetupController::class, 'getDatatables']);
    Route::get('product_item_location_datatables', [ProductController::class, 'getDatatablesLocation']);
    Route::get('pl_export', [ProductLocationSetupController::class, 'exportData']);
    Route::post('pls_save', [ProductLocationSetupController::class, 'storeData']);
    Route::post(
        'pls_delete',
        [ProductLocationSetupController::class, 'deleteData']
    );
    Route::post('check_product_in_location', [ProductLocationSetupController::class, 'checkProductInLocation']);
    Route::post(
        'sv_mutation',
        [ProductLocationSetupController::class, 'productMutation']
    );
    Route::post('sv_setup', [ProductLocationSetupController::class, 'productSetup']);
    Route::post('bin_reset_exec', [ProductLocationSetupController::class, 'binResetExec']);

    // Product Location Setup V2
    /**
     * TODO:
     * 1. Import Excel V
     * 2. Mapping data from excel
     * 3. Validation not found
     * 4. Validation QTY Stock
     */
    Route::get('setup_lokasi_stok_v2', [ProductLocationSetupV2Controller::class, 'index'])->name('product_location_setup_v2');
    Route::get('start_bin_datatables', [ProductLocationSetupV2Controller::class, 'startBinDatatables']);
    Route::get('check_total_qty_in_bin', [ProductLocationSetupV2Controller::class, 'totalQtyInBin']);
    Route::get('end_bin_datatables', [ProductLocationSetupV2Controller::class, 'endBinDatatables']);
    Route::get('bin_history_datatables', [ProductLocationSetupV2Controller::class, 'binHistoryDatatables']);
    Route::get('reload_start_bin', [ProductLocationSetupV2Controller::class, 'reloadStartBin']);
    Route::get('reload_end_bin', [ProductLocationSetupV2Controller::class, 'reloadEndBin']);
    Route::get('history_setup_export', [ProductLocationSetupV2Controller::class, 'exportData']);
    Route::delete('cancel_import', [ProductLocationSetupV2Controller::class, 'cancelImportData']);
    Route::post('sv_mutation_v2', [ProductLocationSetupV2Controller::class, 'productMutation']);
    Route::post('stock_location_import', [ProductLocationSetupV2Controller::class, 'importData']);

    // Stock Transfer
    Route::get('transfer_stok', [StockTransferController::class, 'index'])->name('stock_transfer');
    Route::get('start_transfer_datatables', [StockTransferController::class, 'startTransferDatatables']);
    Route::get('end_transfer_datatables', [StockTransferController::class, 'endTransferDatatables']);
    Route::get('transfer_history_datatables', [StockTransferController::class, 'transferHistoryDatatables']);
    Route::get('stock_transfer_list_datatables', [StockTransferController::class, 'transferListDatatables']);
    Route::get('reload_transfer_bin', [StockTransferController::class, 'reloadTransferBin']);
    Route::get('transfer_bin_datatables', [StockTransferController::class, 'transferBinDatatables']);
    Route::get('in_transfer_bin_datatables', [StockTransferController::class, 'inTransferBinDatatables']);
    Route::get('get_pending_stf_code', [StockTransferController::class, 'getPendingStfCode']);
    Route::get('reload_transfer_invoice', [StockTransferController::class, 'reloadTransferInvoice']);
    Route::get('reload_scan_transfer_invoice', [StockTransferController::class, 'reloadScanTransferInvoice']);
    Route::get('reload_transfer_invoice_check', [StockTransferController::class, 'reloadTransferInvoiceCheck']);
    Route::get('reload_order_invoice', [StockTransferController::class, 'reloadOrderInvoice']);
    Route::post('stock_transfer_exec', [StockTransferController::class, 'stockTransferExec']);
    Route::post('stock_transfer_draft', [StockTransferController::class, 'stockTransferDraft']);
    Route::post('stock_transfer_cancel', [StockTransferController::class, 'stockTransferCancel']);
    Route::post('stock_transfer_confirm', [StockTransferController::class, 'stockTransferConfirm']);
    Route::post('get_transfer_item', [StockTransferController::class, 'getTransferItem']);
    Route::post('cancel_transfer_item', [StockTransferController::class, 'cancelTransferItem']);
    Route::post('stock_transfer_done', [StockTransferController::class, 'stockTransferDone']);
    Route::post('stock_transfer_import', [StockTransferController::class, 'importData']);
    Route::delete('cancel_import_transfer', [StockTransferController::class, 'cancelImportData']);
    Route::post('product_transfer', [StockTransferController::class, 'productTransfer']);
//    Route::post('sv_transfer_v2', [ProductLocationSetupV2Controller::class, 'productMutation']);


    // Stock Transfer
    Route::get('transfer_stok', [StockTransferController::class, 'index'])->name('stock_transfer');
    Route::get('start_transfer_datatables', [StockTransferController::class, 'startTransferDatatables']);
    Route::get('end_transfer_datatables', [StockTransferController::class, 'endTransferDatatables']);
    Route::get('transfer_history_datatables', [StockTransferController::class, 'transferHistoryDatatables']);
    Route::get('stock_transfer_list_datatables', [StockTransferController::class, 'transferListDatatables']);
    Route::get('reload_transfer_bin', [StockTransferController::class, 'reloadTransferBin']);
    Route::get('transfer_bin_datatables', [StockTransferController::class, 'transferBinDatatables']);
    Route::get('in_transfer_bin_datatables', [StockTransferController::class, 'inTransferBinDatatables']);
    Route::get('get_pending_stf_code', [StockTransferController::class, 'getPendingStfCode']);
    Route::get('reload_transfer_invoice', [StockTransferController::class, 'reloadTransferInvoice']);
    Route::get('reload_scan_transfer_invoice', [StockTransferController::class, 'reloadScanTransferInvoice']);
    Route::get('reload_transfer_invoice_check', [StockTransferController::class, 'reloadTransferInvoiceCheck']);
    Route::get('reload_order_invoice', [StockTransferController::class, 'reloadOrderInvoice']);
    Route::post('stock_transfer_exec', [StockTransferController::class, 'stockTransferExec']);
    Route::post('stock_transfer_draft', [StockTransferController::class, 'stockTransferDraft']);
    Route::post('stock_transfer_cancel', [StockTransferController::class, 'stockTransferCancel']);
    Route::post('stock_transfer_confirm', [StockTransferController::class, 'stockTransferConfirm']);
    Route::post('get_transfer_item', [StockTransferController::class, 'getTransferItem']);
    Route::post('cancel_transfer_item', [StockTransferController::class, 'cancelTransferItem']);
    Route::post('stock_transfer_done', [StockTransferController::class, 'stockTransferDone']);
    Route::post('stock_transfer_import', [StockTransferController::class, 'importData']);
    // Stock Transfer Data
    Route::get('data_transfer_stok', [StockTransferDataController::class, 'index'])->name('stock_transfer_data');
    Route::get('transfer_data_datatables', [StockTransferDataController::class, 'getDatatables']);
    Route::get('transfer_data_accept_datatables', [StockTransferDataController::class, 'getAcceptDatatables']);
    Route::get('transfer_data_history_datatables', [StockTransferDataController::class, 'getHistoryDatatables']);
    Route::post('stock_transfer_accept', [StockTransferDataController::class, 'acceptTransfer']);
    Route::get('std_export', [StockTransferDataController::class, 'exportData']);
    Route::post('std_receive_transfer_data_import', [StockTransferDataController::class, 'importReceiveTransferData']);


    // Stock Transfer Data
    Route::get('data_transfer_stok', [StockTransferDataController::class, 'index'])->name('stock_transfer_data');
    Route::get('transfer_data_datatables', [StockTransferDataController::class, 'getDatatables']);
    Route::get('transfer_data_accept_datatables', [StockTransferDataController::class, 'getAcceptDatatables']);
    Route::get('transfer_data_history_datatables', [StockTransferDataController::class, 'getHistoryDatatables']);
    Route::post('stock_transfer_accept', [StockTransferDataController::class, 'acceptTransfer']);
    Route::get('std_export', [StockTransferDataController::class, 'exportData']);
    Route::post('std_receive_transfer_data_import', [StockTransferDataController::class, 'importReceiveTransferData']);

    // Marketplace Manager
    Route::get('marketplace_manager', [MarketplaceManagerController::class, 'index']);
    Route::get('marketplace_stock_datatables', [MarketplaceManagerController::class, 'stockDatatables']);
    Route::get('master_datatables', [MarketplaceManagerController::class, 'masterDatatables']);
    Route::post('check_marketplace_code', [MarketplaceManagerController::class, 'checkCode']);
    Route::post('update_marketplace_code', [MarketplaceManagerController::class, 'updateCode']);
    Route::post('marketplace_stock_refresh', [MarketplaceManagerController::class, 'refreshData']);
    Route::post('marketplace_template_import', [MarketplaceManagerController::class, 'importData']);
    Route::get(
        'fetch_master',
        [MarketplaceManagerController::class, 'fetchData']
    );

    // Stock Data
    Route::get('data_stok', [StockDataController::class, 'index'])->name('stock_data');
    Route::get('get_articles_promo/{article_id}', [StockDataController::class, 'getArticlesPromo']);
    Route::get('stock_data_datatables', [StockDataController::class, 'getDatatables']);
    Route::get('aging_datatables', [StockDataController::class, 'getAgingDatatables']);
    Route::get('helper_stock_data_datatables', [StockDataController::class, 'getHelperDatatables']);
    Route::post('stock_data_reload_category', [StockDataController::class, 'reloadCategory']);
    Route::post('stock_data_reload_sub_category', [StockDataController::class, 'reloadSubCategory']);
    Route::post('stock_data_reload_sub_sub_category', [StockDataController::class, 'reloadSubSubCategory']);
    Route::post('stock_data_reload_brand', [StockDataController::class, 'reloadBrand']);
    Route::post('stock_data_reload_size', [StockDataController::class, 'reloadSize']);

    // Stock Tracking
    Route::get('stock_tracking', [StockTrackingController::class, 'index'])->name('stock_tracking');
    Route::get('stock_tracking_datatables', [StockTrackingController::class, 'getDatatables']);
    Route::get('pickup_list_datatables', [StockTrackingController::class, 'getPickupDatatables']);
    Route::post('get_stock_notice', [StockTrackingController::class, 'getNotice']);
    Route::post('get_stock_graph', [StockTrackingController::class, 'getGraph']);
    Route::post(
        'pickup_item',
        [StockTrackingController::class, 'pickupItem']
    );
    Route::post('pickup_approval_item', [StockTrackingController::class, 'pickupApprovalItem']);
    Route::post('cancel_pickup_item', [StockTrackingController::class, 'cancelPickupItem']);
    Route::post(
        'plst_delete',
        [StockTrackingController::class, 'deleteData']
    );
});
