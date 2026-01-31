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
use App\Http\Controllers\PosV2Controller;
use App\Http\Controllers\KategoriPosV2Controller;
use App\Http\Controllers\ProdukPosV2Controller;
use App\Http\Controllers\ArtikelPromoController;


use App\Http\Controllers\SatuanPosV2Controller;
use App\Http\Controllers\BahanBakuPosV2Controller;
use App\Http\Controllers\DataUserPosV2Controller;
use App\Http\Controllers\PromoRecommendationController;

Route::middleware(['auth'])->group(function () {

    // Nameset Data
    Route::get('data_nameset', [NamesetDataController::class, 'index'])->name('nameset_data');
    Route::get('data_nameset_v2', [NamesetDataController::class, 'indexUpdated'])->name('updated_data_nameset');
    Route::get('nameset_datatables', [NamesetDataController::class, 'getDatatables']);
    Route::get('nameset_datatables_simple', [NamesetDataController::class, 'getDatatablesForSimple']);
    Route::post('update_data_nameset', [NamesetDataController::class, 'updateData']);
    Route::get('export_nameset', [NamesetDataController::class, 'exportData'])->name('export.nameset');

    // Target
    Route::get('target', [TargetController::class, 'index'])->name('target');
    Route::get('target_v2', [TargetController::class, 'indexUpdated'])->name('target_v2');
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
    Route::post('import-csv', [TargetController::class, 'importCSV'])->name('import.csv'); //fitur baru -> template csv

    // Product Discount (Setup Diskon)
    Route::get('setup_diskon', [ProductDiscountController::class, 'index'])->name('product_discount');
    Route::get('setup_diskon_v2', [ProductDiscountController::class, 'indexUpdated'])->name('product_discount_v2');
    Route::get('product_discount_datatables', [ProductDiscountController::class, 'getDatatables']);
    Route::get('product_discount_detail_datatables', [ProductDiscountController::class, 'getDetailDatatables']);
    Route::get('product_discount_article_datatables', [ProductDiscountController::class, 'getArticleDatatables']);
    Route::post('pd_save', [ProductDiscountController::class, 'storeData']);
    Route::post('pd_delete', [ProductDiscountController::class, 'deleteData']);
    Route::post('add_item_to_discount', [ProductDiscountController::class, 'addItemToDiscount']);
    Route::post('delete_item_discount', [ProductDiscountController::class, 'deleteItemDiscount']);
    Route::post('discount_import', [ProductDiscountController::class, 'importData']);
    Route::post('mass_import_product_discount', [ProductDiscountController::class, 'massImportData']);

    // Voucher
    Route::get('voucher', [VoucherController::class, 'index']);
    Route::get('voucher_v2', [VoucherController::class, 'indexUpdated'])->name('voucher_v2');
    Route::get('voucher_datatables', [VoucherController::class, 'getDatatables']);
    Route::post('voucher_save', [VoucherController::class, 'storeData']);
    Route::post('voucher_delete', [VoucherController::class, 'deleteData']);
    Route::post('check_exists_voucher', [VoucherController::class, 'checkExistsVoucher']);

    // Pos Summary
    Route::get('pos_summary', [PosSummaryController::class, 'index'])->name('pos_summary');
    Route::get('pos_summary_v2', [PosSummaryController::class, 'indexUpdated'])->name('pos_summary_v2');
    Route::get('pos_summary_online_datatables', [PosSummaryController::class, 'onlineDatatables']);
    Route::get('pos_summary_offline_datatables', [PosSummaryController::class, 'offlineDatatables']);
    Route::get('sales_detail_datatables', [PosSummaryController::class, 'salesDetailDatatables']);
    Route::get('sales_item_detail_datatables', [PosSummaryController::class, 'salesItemDetailDatatables']);
    Route::post('target_chart', [PosSummaryController::class, 'targetChart']);
    Route::post('cross_chart', [PosSummaryController::class, 'crossChart']);

    // Cross Order
    Route::get('cross_order', [CrossOrderController::class, 'index'])->name('cross');
    Route::get('cross_order_v2', [CrossOrderController::class, 'indexUpdated'])->name('cross_order_v2');
    Route::get('cross_order_datatables', [CrossOrderController::class, 'getDatatables']);
    Route::get('cross_order_datatables_simple', [CrossOrderController::class, 'getDatatablesForSimple']);
    Route::get('confirmation_datatables', [CrossOrderController::class, 'confirmationDatatables']);
    Route::get('detail_datatables', [CrossOrderController::class, 'detailDatatables']);
    Route::get('take_confirmation_datatables', [CrossOrderController::class, 'takeConfirmationDatatables']);
    Route::get('cross_invoice/{invoice}', [CrossOrderController::class, 'printInvoice']);
    Route::get('check_cross_order', [CrossOrderController::class, 'checkCrossOrder']);
    Route::post('sv_cross_order', [CrossOrderController::class, 'saveData']);
    Route::post('sv_cross_status', [CrossOrderController::class, 'saveStatus']);
    Route::post('sv_cross_note', [CrossOrderController::class, 'saveNote']);
    Route::post('print_cross_invoice', [CrossOrderController::class, 'checkPrint']);
    Route::post('print_resi', [CrossOrderController::class, 'printResi']);
    Route::post('check_resi', [CrossOrderController::class, 'checkResi']);
    Route::post('reload_cross_order_invoice', [CrossOrderController::class, 'reloadCrossOrderInvoice']);
    Route::post('get_cross_item_status', [CrossOrderController::class, 'getCrossItem']);
    Route::post('get_resi_pdf', [CrossOrderController::class, 'getPdf']);
    Route::get('get_resi_detail/{id}', [CrossOrderController::class, 'getResiDetail']);

    // Invoice Tracking
    Route::get('invoice_tracking', [InvoiceTrackingController::class, 'index'])->name('invoice_tracking');
    Route::get('invoice_tracking_v2', [InvoiceTrackingController::class, 'indexUpdated'])->name('invoice_tracking_v2');
    Route::get('invoice_tracking_datatables', [InvoiceTrackingController::class, 'getDatatables']);
    Route::post('shipping_number_save', [InvoiceTrackingController::class, 'updateData']);
    Route::post('waybill_tracking', [InvoiceTrackingController::class, 'waybillTracking']);
    Route::post('all_waybill_tracking', [InvoiceTrackingController::class, 'allWaybillTracking']);
    Route::get('invoice_dp_repayment_details/{id}', [InvoiceTrackingController::class, 'invoiceDpRepaymentDetails']);
    Route::post('invoice_dp_repayment', [InvoiceTrackingController::class, 'invoiceDpRepayment']);
    Route::get('get_total_transactions', [InvoiceTrackingController::class, 'getTotalTransactions']);

    // Artikel Promo
    Route::get('artikel_promo', [ArtikelPromoController::class, 'index']);
    Route::get('artikel_promo_v2', [ArtikelPromoController::class, 'indexUpdated'])->name('artikel_promo_v2');
    Route::get('artikel_promo_datatables', [ArtikelPromoController::class, 'getDatatables']);
    Route::get('artikel_promo_datatables_simple', [ArtikelPromoController::class, 'getDatatablesForSimple']);
    Route::get('artikel_promo_detail_datatables', [ArtikelPromoController::class, 'getArtikelPromoDetails']);
    Route::post('artikel_promo_save', [ArtikelPromoController::class, 'storeData']);
    Route::post('artikel_promo_delete', [ArtikelPromoController::class, 'deleteData']);
    Route::post('check_exists_artikel_promo', [ArtikelPromoController::class, 'checkExistsArtikelPromo']);
    Route::get('export_artikel_promo', [ArtikelPromoController::class, 'exportData']);
    Route::post('artikel_promo_import', [ArtikelPromoController::class, 'saveArtikelPromoImport']);

    // Invoice 
    Route::post('search_invoice', [InvoiceController::class, 'searchInvoice']);

    /*
    POS VERSI 2 -- 27-09-24
    */
    Route::prefix('pos_v2')->group(function () {
        Route::get('/', [PosV2Controller::class, 'index'])->name('pos.dashboard-posv2'); // Dashboard

        Route::get('/satuan', [SatuanPosV2Controller::class, 'satuan'])->name('posv2.masterdata.satuan-posv2');
        Route::get('/bahanbaku', [BahanBakuPosV2Controller::class, 'bahan'])->name('posv2.masterdata.bahanbaku-posv2');
        Route::get('/produk', [ProdukPosV2Controller::class, 'produk'])->name('posv2.masterdata.produk-posv2');



    });

    // Promo Recommendations
    Route::get('threshold_promo', [PromoRecommendationController::class, 'index']);
    Route::get('threshold_promo_v2', [PromoRecommendationController::class, 'indexUpdated'])->name('threshold_promo_v2');
    Route::get('threshold_promo_datatables', [PromoRecommendationController::class, 'getDatatables']);
    Route::get('threshold_promo_datatables_simple', [PromoRecommendationController::class, 'getDatatablesForSimple']);
    Route::get('threshold_promo_detail_datatables', [PromoRecommendationController::class, 'getPromoRecommendationDetails']);
    Route::get('threshold_promo_detail_datatables_simple', [PromoRecommendationController::class, 'getPromoRecommendationDetailsForSimple']);
    Route::post('threshold_promo_save', [PromoRecommendationController::class, 'storeData']);
    Route::post('threshold_promo_delete', [PromoRecommendationController::class, 'deleteData']);
    Route::post('check_exists_threshold_promo', [PromoRecommendationController::class, 'checkExistsPromoRecommendation']);
    Route::get('export_threshold_promo', [PromoRecommendationController::class, 'exportData']);
    Route::get('export_threshold_promo_detail', [PromoRecommendationController::class, 'exportDetailData']);
    Route::post('threshold_promo_import', [PromoRecommendationController::class, 'savePromoRecommendationImport']);
    Route::delete('threshold_promo_detail_delete/{id}', [PromoRecommendationController::class, 'deletePromoRecommendationDetail']);
    Route::post('threshold_promo_detail_update/{id}', [PromoRecommendationController::class, 'updatePromoRecommendationDetail']);
});


