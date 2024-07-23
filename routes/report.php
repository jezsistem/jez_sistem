<?php

use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\StockCardController;
use App\Http\Controllers\DashboardV2Controller;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PoReceiveReportController;
use App\Http\Controllers\ReportShiftController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Report 
    Route::get('laporan_penjualan', [SalesReportController::class, 'index'])->name('sales_report');
    Route::get('sales_report_datatables', [SalesReportController::class, 'getDatatables']);
    Route::get('check_hb_hj', [SalesReportController::class, 'hbhjDatatables']);
    Route::get('sales_export', [SalesReportController::class, 'exportData']);
    Route::post('cabang_summary', [SalesReportController::class, 'cabangSummary']);

    // StockCardController
    Route::get('stock_card', [StockCardController::class, 'index']);
    Route::get('stc_article_datatables', [StockCardController::class, 'getADatatables']);
    Route::post('stc_save', [StockCardController::class, 'saveData']);
    Route::post('stc_delete', [StockCardController::class, 'deleteData']);
    Route::post('stock_report_fill_data', [StockCardController::class, 'fillData']);
    Route::post('stock_report_export', [StockCardController::class, 'exportData']);
    Route::post('stock_report_phase2', [StockCardController::class, 'phase2']);
    Route::post('stock_report_phase3', [StockCardController::class, 'phase3']);

    // Dashboard V2
    Route::get('dashboard_v2', [DashboardV2Controller::class, 'index']);
    Route::get('store_info_datatables', [DashboardV2Controller::class, 'getStoreInfoDatatables']);
    Route::get('brand_info_datatables', [DashboardV2Controller::class, 'getBrandInfoDatatables']);
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
    Route::get('po_receive_datatables', [PoReceiveReportController::class, 'getDatatables']);
    Route::get('po_receive_detail_datatables', [PoReceiveReportController::class, 'getDetailDatatables']);
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
});
