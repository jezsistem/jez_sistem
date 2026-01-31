<?php

use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\StockCardController;
use App\Http\Controllers\DashboardV2Controller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PoReceiveReportController;
use App\Http\Controllers\ReportShiftController;
use App\Http\Controllers\InvoiceReportController;
use App\Http\Controllers\ArticleReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransaksiOnlineController;

Route::middleware(['auth'])->group(function () {
    // Report 
    Route::get('laporan_penjualan', [SalesReportController::class, 'index'])->name('sales_report');
    Route::get('laporan_penjualan_v2', [SalesReportController::class, 'indexUpdated']);
    Route::get('sales_report_datatables', [SalesReportController::class, 'getDatatables']);
    Route::get('invoice_report_datatables_simple', [InvoiceReportController::class, 'getDatatablesForSimple']);
    Route::get('article_report_datatables_simple', [ArticleReportController::class, 'getDatatablesForSimple']);
    Route::get('check_hb_hj', [SalesReportController::class, 'hbhjDatatables']);
    Route::get('check_hb_hj_simple', [SalesReportController::class, 'getHbhjDatatablesForSimple']);
    Route::get('sales_export', [SalesReportController::class, 'exportData']);
    Route::get('online_sales_export', [TransaksiOnlineController::class, 'exportDataOnline']);
    Route::post('cabang_summary', [SalesReportController::class, 'cabangSummary']);

    // StockCardController
    Route::get('stock_card', [StockCardController::class, 'index']);
    Route::get('stock_card_v2', [StockCardController::class, 'indexUpdated']);
    Route::get('stc_article_datatables', [StockCardController::class, 'getADatatables']);
    Route::get('stc_article_datatables_simple', [StockCardController::class, 'getDatatablesForSimple']);
    Route::get('export-article-stock', [StockCardController::class, 'exportArticleStock']);
    Route::post('stc_save', [StockCardController::class, 'saveData']);
    Route::post('stc_delete', [StockCardController::class, 'deleteData']);
    Route::post('stock_report_fill_data', [StockCardController::class, 'fillData']);
    Route::post('stock_report_export', [StockCardController::class, 'exportData']);
    Route::post('stock_report_phase2', [StockCardController::class, 'phase2']);
    Route::post('stock_report_phase3', [StockCardController::class, 'phase3']);

    // Dashboard V2
    Route::get('dashboard_v2', [DashboardV2Controller::class, 'index']);
    Route::get('dashboard_v2_v2', [DashboardV2Controller::class, 'indexUpdated']);
    Route::get('store_info_datatables', [DashboardV2Controller::class, 'getStoreInfoDatatables']);
    Route::get('store_info_datatables_simple', [DashboardV2Controller::class, 'getStoreInfoDatatablesForSimple']);
    Route::get('brand_info_datatables', [DashboardV2Controller::class, 'getBrandInfoDatatables']);
    Route::get('brand_info_datatables_simple', [DashboardV2Controller::class, 'getBrandInfoDatatablesForSimple']);
    Route::post('dashboard_v2_summary', [DashboardV2Controller::class, 'summaryV2']);
    Route::post('get_sales_graph', [DashboardController::class, 'getSalesGraph']);
    Route::post('get_profit_graph', [DashboardController::class, 'getProfitGraph']);
    Route::post('get_csales_graph', [DashboardController::class, 'getcSalesGraph']);
    Route::post('get_cprofit_graph', [DashboardController::class, 'getcProfitGraph']);
    Route::post('get_purchase_graph', [DashboardController::class, 'getPurchaseGraph']);
    Route::post('get_cc_asset_graph', [DashboardController::class, 'getCCAssetGraph']);
    Route::post('get_ca_asset_graph', [DashboardController::class, 'getCAAssetGraph']);
    Route::post('get_debt_graph', [DashboardController::class, 'getDebtGraph']);

    // Po Receive Report
    Route::get('laporan_datang_barang', [PoReceiveReportController::class, 'index']);
    Route::get('laporan_datang_barang_v2', [PoReceiveReportController::class, 'indexUpdated']);
    Route::get('po_receive_datatables', [PoReceiveReportController::class, 'getDatatables']);
    Route::get('po_receive_datatables_simple', [PoReceiveReportController::class, 'getDatatablesForSimple']);
    Route::get('po_receive_detail_datatables', [PoReceiveReportController::class, 'getDetailDatatables']);
    Route::get('po_receive_detail_datatables_simple', [PoReceiveReportController::class, 'getDetailDatatablesForSimple']);
    Route::get('po_receive_export', [PoReceiveReportController::class, 'exportData']);

    // Report Shift
    /**
     * TODO:
     * 1. Report Shift
     * 2. Report Shift Detail
     * 3. Report Shift Detail Print
     * 4. Report Shift Detail Print Excel
     */
    Route::get('report_shift', [ReportShiftController::class, 'index']);
    Route::get('report_shift_datatables', [ReportShiftController::class, 'getDatatables']);
    Route::post('report_shift_detail', [ReportShiftController::class, 'detail']);
    Route::post('report_shift_product_sold', [ReportShiftController::class, 'productSold']);
    Route::post('report_shift_product_refund', [ReportShiftController::class, 'productRefund']);
    //    Route::post('report_shift_detail_datatables', [ReportShiftController::class, 'getDetailDatatables']);
    //    Route::post('report_shift_detail_print', [ReportShiftController::class, 'printDetail']);
    //    Route::post('report_shift_detail_print_excel', [ReportShiftController::class, 'printDetailExcel']);

    // Report Shift V2
    Route::get('report_shift_v2', [ReportShiftController::class, 'indexUpdated'])->name('report_shift_v2');
    Route::get('report_shift_datatables_simple', [ReportShiftController::class, 'getDatatablesForSimple']);


    // Report Current Shift
    /**
     * TODO:
     * 1. Report Current Shift
     */
    Route::get('current_shift', [ReportShiftController::class, 'current_shift']);
    Route::get('report_current_shift_datatables', [ReportShiftController::class, 'getDatatablesCurrentShift']);
    Route::post('current_shift_detail', [ReportShiftController::class, 'current_shift_detail']);
    Route::post('report_shift_product_sold', [ReportShiftController::class, 'productSold']);
    Route::post('report_shift_product_refund', [ReportShiftController::class, 'productRefund']);
    //    Route::post('report_shift_detail_datatables', [ReportShiftController::class, 'getDetailDatatables']);
    //    Route::post('report_shift_detail_print', [ReportShiftController::class, 'printDetail']);
    //    Route::post('report_shift_detail_print_excel', [ReportShiftController::class, 'printDetailExcel']);

    // Report Current Shift V2
    Route::get('current_shift_v2', [ReportShiftController::class, 'currentShiftUpdated'])->name('current_shift_v2');
    Route::get('report_current_shift_datatables_simple', [ReportShiftController::class, 'getDatatablesCurrentShiftForSimple']);
});
