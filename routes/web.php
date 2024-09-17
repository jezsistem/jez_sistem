<?php


use App\Http\Controllers\ProductSubSubCategoryTestController;

use App\Http\Controllers\UserShiftController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceEditorController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;

use App\Http\Controllers\StoreTypeController;
use App\Http\Controllers\StoreTypeDivisionController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductSupplierController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductSubCategoryController;

use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\GenderController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerTypeController;

use App\Http\Controllers\MainColorController;
use App\Http\Controllers\ColorController;

use App\Http\Controllers\SizeController;
use App\Http\Controllers\UserActivityController;

use App\Http\Controllers\PointOfSaleController;

use App\Http\Controllers\GroupController;

use App\Http\Controllers\TrackingController;
use App\Http\Controllers\InvoiceController;

use App\Http\Controllers\DebtListController;

use App\Http\Controllers\AssetController;

use App\Http\Controllers\InvoiceReportController;
use App\Http\Controllers\ArticleReportController;

use App\Http\Controllers\HelperBackupController;

use App\Http\Controllers\WebArticleController;

use App\Http\Controllers\PaymentCheckController;

use App\Http\Controllers\ArticleInformationController;

use App\Http\Controllers\DashboardV2Controller;

use App\Http\Controllers\UpcloudBalanceController;

use App\Http\Controllers\UpdatedDashboardController;

use App\Http\Controllers\AssetDetailController;

use App\Http\Controllers\UserMenuAccessController;
use App\Http\Controllers\MainMenuController;
use App\Http\Controllers\MenuAccessController;
use App\Http\Controllers\RedirectController;


use App\Http\Controllers\WebConfigController;

use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Validation
Route::get('', [AuthController::class, 'index'])->name('login');
Route::post('user_login', [AuthController::class, 'login']);

Route::get('payment_check/88991703/show', [PaymentCheckController::class, 'checkData']);
Route::get('auto/8899/close_data', [ArticleInformationController::class, 'getAutoUpdateArticleInformation']);
Route::get('auto/9999/close_data', [DashboardV2Controller::class, 'closeData']);

Route::get('print_invoice/{invoice}', [InvoiceController::class, 'printInvoice'])->name('print_invoice');
Route::get('print_offline_invoice/{invoice}', [InvoiceController::class, 'printOfflineInvoice'])->name('print_offline_invoice');

Route::group(['middleware' => 'auth'], function () {
    // Redirect
    Route::get('redirect', [RedirectController::class, 'index'])->name('redirect');
    // Upcloud Balance
    Route::get('get_upcloud_balance', [UpcloudBalanceController::class, 'getBalance']);
    // Tracking
    Route::get('tracking', [TrackingController::class, 'index'])->name('tracking');
    Route::get('check_invoice/{invoice}', [InvoiceController::class, 'checkInvoice'])->name('check_invoice');
    Route::get('check_offline_invoice/{invoice}', [InvoiceController::class, 'checkOfflineInvoice'])->name('check_offline_invoice');
    Route::get('check_sync', [InvoiceController::class, 'checkSync']);
    Route::post('check_barcode_tracking', [TrackingController::class, 'checkBarcodeTracking']);
    Route::post('check_secret_code', [TrackingController::class, 'checkSecretCode']);
    Route::post('reload_bin_by_barcode', [TrackingController::class, 'reloadBinByBarcode']);
    Route::post('order_list_by_invoice', [PointOfSaleController::class, 'orderListByInvoice']);
    Route::post('packing_list_by_invoice', [PointOfSaleController::class, 'packingListByInvoice']);
    Route::post('save_out_activity', [TrackingController::class, 'saveOutActivity']);
    Route::post('save_in_activity', [TrackingController::class, 'saveInActivity']);
    Route::post('save_tracking_activity', [TrackingController::class, 'saveTrackingActivity']);
    Route::post('cancel_tracking_activity', [TrackingController::class, 'cancelTrackingActivity']);
    Route::post('save_packing_activity', [TrackingController::class, 'savePackingActivity']);
    Route::post('save_reject_activity', [TrackingController::class, 'saveRejectActivity']);
    Route::get('product_in_datatables', [TrackingController::class, 'inDatatables']);
    Route::get('product_out_datatables', [TrackingController::class, 'outDatatables']);
    Route::get('scan_product_out_datatables', [TrackingController::class, 'scanOutDatatables']);
    Route::get('scan_product_in_datatables', [TrackingController::class, 'scanInDatatables']);
    Route::post('autocomplete_fetch', [ArticleController::class, 'fetch']);
    Route::post('check_article', [ArticleController::class, 'checkArticle']);

    // POS
    Route::get('point_of_sale', [PointOfSaleController::class, 'index'])->name('point_of_sale');
    Route::get('/current-shift-data', [PointOfSaleController::class, 'getCurrentShiftData'])->name('current-shift.data');
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


    // Auth Controller
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('change_password', [AuthController::class, 'changePassword']);

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('brand_debt_datatables', [DebtListController::class, 'brandDebtDatatables']);
    Route::get('assets_datatables', [AssetController::class, 'getDatatables']);
    Route::get('brand_assets_datatables', [AssetController::class, 'brandDatatables']);
    Route::get('brand_nett_sales_datatables', [AssetController::class, 'brandNettSalesDatatables']);
    Route::post('dashboard_range', [DashboardController::class, 'rangeData']);
    Route::post('urban_dashboard_range', [DashboardController::class, 'urbanRangeData']);
    Route::post('load_profit', [AssetController::class, 'loadProfit']);
    Route::post('load_cprofit', [AssetController::class, 'loadcProfit']);
    Route::post('load_assets', [AssetController::class, 'loadAssets']);
    Route::post('load_cassets', [AssetController::class, 'loadCAssets']);


    // ==== Dashboard ==== //
    Route::get('asset_by_store_datatables', [AssetController::class, 'assetByStoreDatatables']);
    Route::get('debt_by_store_datatables', [AssetController::class, 'debtByStoreDatatables']);
    Route::get('nett_sale_by_store_datatables', [AssetController::class, 'nettSaleByStoreDatatables']);
    Route::get('brand_value_datatables', [AssetController::class, 'getAssetByBrand']);
    // ==== Dashboard ==== //
    // Helper Backup //
    Route::get('helper_backup', [HelperBackupController::class, 'index']);
    // Customer Type
    Route::get('customer_type_datatables', [CustomerTypeController::class, 'getDatatables']);
    Route::get('reload_customer_type', [CustomerTypeController::class, 'reloadCustomerType']);
    Route::get('province_datatables', [CustomerController::class, 'provinceDatatables']);
    Route::get('city_datatables', [CustomerController::class, 'cityDatatables']);
    Route::get('city_rank_datatables', [CustomerController::class, 'cityRankDatatables']);
    Route::get('subdistrict_datatables', [CustomerController::class, 'subdistrictDatatables']);
    Route::post('ct_save', [CustomerTypeController::class, 'storeData']);
    Route::post('ct_delete', [CustomerTypeController::class, 'deleteData']);

    // Store Type
    Route::get('store_type_datatables', [StoreTypeController::class, 'getDatatables']);
    Route::get('reload_store_type', [StoreTypeController::class, 'reloadStoreType']);
    Route::post('stt_save', [StoreTypeController::class, 'storeData']);
    Route::post('stt_delete', [StoreTypeController::class, 'deleteData']);
    // Store Division
    Route::get('data_divisi', [StoreTypeDivisionController::class, 'index'])->name('store_type_division');
    Route::get('store_type_division_datatables', [StoreTypeDivisionController::class, 'getDatatables']);
    Route::post('dv_save', [StoreTypeDivisionController::class, 'storeData']);
    Route::post('dv_delete', [StoreTypeDivisionController::class, 'deleteData']);
    // Store
    Route::get('data_store', [StoreController::class, 'index'])->name('store');
    Route::get('store_datatables', [StoreController::class, 'getDatatables']);
    Route::post('st_save', [StoreController::class, 'storeData']);
    Route::post('st_delete', [StoreController::class, 'deleteData']);
    // Product Supplier
    Route::get('data_supplier', [ProductSupplierController::class, 'index'])->name('product_supplier');
    Route::get('product_supplier_datatables', [ProductSupplierController::class, 'getDatatables']);
    Route::post('ps_save', [ProductSupplierController::class, 'storeData']);
    Route::post('ps_delete', [ProductSupplierController::class, 'deleteData']);
    Route::post('ps_import', [ProductSupplierController::class, 'importData']);
    Route::post('check_exists_supplier', [ProductSupplierController::class, 'checkExistsSupplier']);
    // Brand
    Route::get('brands', [BrandController::class, 'index'])->name('brands');
    Route::get('brand_datatables', [BrandController::class, 'getDatatables']);
    Route::post('br_save', [BrandController::class, 'storeData']);
    Route::post('br_delete', [BrandController::class, 'deleteData']);
    Route::post('delete_logo_brand', [BrandController::class, 'deleteBrandImage']);
    Route::post('delete_banner_brand', [BrandController::class, 'deleteBannerImage']);
    Route::post('br_import', [BrandController::class, 'importData']);
    Route::post('check_exists_brand', [BrandController::class, 'checkExistsBrand']);
    // Product Unit
    Route::get('satuan_produk', [ProductUnitController::class, 'index'])->name('product_unit');
    Route::get('product_unit_datatables', [ProductUnitController::class, 'getDatatables']);
    Route::post('pu_save', [ProductUnitController::class, 'storeData']);
    Route::post('pu_delete', [ProductUnitController::class, 'deleteData']);
    Route::post('pu_import', [ProductUnitController::class, 'importData']);
    Route::post('check_exists_product_unit', [ProductUnitController::class, 'checkExistsProductUnit']);
    // Gender
    Route::get('gender', [GenderController::class, 'index'])->name('gender');
    Route::get('gender_datatables', [GenderController::class, 'getDatatables']);
    Route::post('gn_save', [GenderController::class, 'storeData']);
    Route::post('gn_delete', [GenderController::class, 'deleteData']);
    Route::post('gn_import', [GenderController::class, 'importData']);
    Route::post('check_exists_gender', [GenderController::class, 'checkExistsGender']);
    // Season
    Route::get('season', [SeasonController::class, 'index'])->name('season');
    Route::get('season_datatables', [SeasonController::class, 'getDatatables']);
    Route::post('ss_save', [SeasonController::class, 'storeData']);
    Route::post('ss_delete', [SeasonController::class, 'deleteData']);
    Route::post('ss_import', [SeasonController::class, 'importData']);
    Route::post('check_exists_season', [SeasonController::class, 'checkExistsSeason']);
    // Product Category
    Route::get('kategori_produk', [ProductCategoryController::class, 'index'])->name('product_category');
    Route::get('product_category_datatables', [ProductCategoryController::class, 'getDatatables']);
    Route::post('pc_save', [ProductCategoryController::class, 'storeData']);
    Route::post('pc_delete', [ProductCategoryController::class, 'deleteData']);
    Route::post('pc_import', [ProductCategoryController::class, 'importData']);
    Route::post('check_exists_product_category', [ProductCategoryController::class, 'checkExistsProductCategory']);
    // Product Sub Category
    Route::get('sub_kategori_produk', [ProductSubCategoryController::class, 'index'])->name('product_sub_category');
    Route::get('product_sub_category_datatables', [ProductSubCategoryController::class, 'getDatatables']);
    Route::get('psc_reload', [ProductSubCategoryController::class, 'reloadPsc']);
    Route::post('psc_save', [ProductSubCategoryController::class, 'storeData']);
    Route::post('psc_delete', [ProductSubCategoryController::class, 'deleteData']);
    Route::post('psc_import', [ProductSubCategoryController::class, 'importData']);
    Route::post('check_exists_product_sub_category', [ProductSubCategoryController::class, 'checkExistsProductSubCategory']);
    // Product Sub Sub Category
    /**
    Route::get('sub_sub_kategori_produk', [ProductSubSubCategoryController::class, 'index'])->name('product_sub_sub_category');
    Route::get('product_sub_sub_category_datatables', [ProductSubSubCategoryController::class, 'getDatatables']);
    Route::get('reload_product_sub_category', [ProductSubCategoryController::class, 'reloadProductSubCategory']);
    Route::get('reload_product_sub_sub_category', [ProductSubSubCategoryController::class, 'reloadProductSubSubCategory']);
    Route::get('pssc_reload', [ProductSubSubCategoryController::class, 'reloadPssc']);
    Route::post('pssc_save', [ProductSubSubCategoryController::class, 'storeData']);
    Route::post('pssc_delete', [ProductSubSubCategoryController::class, 'deleteData']);
    Route::post('pssc_import', [ProductSubSubCategoryController::class, 'importData']);
     */
    Route::get('sub_sub_kategori_produk', [ProductSubSubCategoryTestController::class, 'index'])->name('product_sub_sub_category');
    Route::get('product_sub_sub_category_datatables', [ProductSubSubCategoryTestController::class, 'getDatatables']);
    Route::get('reload_product_sub_category', [ProductSubCategoryController::class, 'reloadProductSubCategory']);
    Route::get('reload_product_sub_sub_category', [ProductSubSubCategoryTestController::class, 'reloadProductSubSubCategory']);
    Route::get('get_product_sub_sub_category', [ProductSubSubCategoryTestController::class, 'getProductSubSubCategory']);
    Route::get('pssc_reload', [ProductSubSubCategoryTestController::class, 'reloadPssc']);
    Route::post('pssc_save', [ProductSubSubCategoryTestController::class, 'storeData']);
    Route::post('pssc_delete', [ProductSubSubCategoryTestController::class, 'deleteData']);
    Route::post('pssc_import', [ProductSubSubCategoryTestController::class, 'importData']);

    // Product Main Color
    Route::get('warna_produk', [MainColorController::class, 'index'])->name('main_color');
    Route::get('main_color_datatables', [MainColorController::class, 'getDatatables']);
    Route::post('mc_save', [MainColorController::class, 'storeData']);
    Route::post('mc_delete', [MainColorController::class, 'autocomplete_customer']);
    Route::post('mc_import', [MainColorController::class, 'importData']);
    Route::post('check_exists_main_color', [MainColorController::class, 'checkExistsMainColor']);
    // Product Sub Color
    Route::get('sub_warna_produk', [ColorController::class, 'index'])->name('color');
    Route::get('color_datatables', [ColorController::class, 'getDatatables']);
    Route::post('cl_save', [ColorController::class, 'storeData']);
    Route::post('cl_delete', [ColorController::class, 'deleteData']);
    Route::post('cl_import', [ColorController::class, 'importData']);
    // Product Size
    Route::get('size_produk', [SizeController::class, 'index'])->name('size');
    Route::get('size_datatables', [SizeController::class, 'getDatatables']);
    Route::get('reload_size', [SizeController::class, 'reloadSize']);
    Route::get('reload_size_schema', [SizeController::class, 'reloadSizeSchema']);
    Route::get('reload_size_schema_modal', [SizeController::class, 'reloadSizeSchemaModal']);
    Route::post('sz_save', [SizeController::class, 'storeData']);
    Route::post('sz_delete', [SizeController::class, 'deleteData']);
    Route::post('sz_import', [SizeController::class, 'importData']);
    Route::post('check_exists_size', [SizeController::class, 'checkExistsSize']);
    Route::post('check_schema_size_product_stock', [SizeController::class, 'checkSchemaSizeProductStock']);

    // Product
    Route::get('data_produk', [ProductController::class, 'index'])->name('product');
    Route::get('product_datatables', [ProductController::class, 'getDatatables']);
    Route::get('p_export', [ProductController::class, 'exportData']);
    Route::get('p_export_barcode', [ProductController::class, 'exportDataBarcode']);
    Route::post('product_detail', [ProductController::class, 'productDetail']);
    Route::post('p_save', [ProductController::class, 'storeData']);
    Route::post('p_delete', [ProductController::class, 'deleteData']);
    Route::post('p_import', [ProductController::class, 'importData']);
    Route::post('p_import_2', [ProductController::class, 'importData2']);
    Route::post('check_exists_barcode', [ProductController::class, 'checkExistsBarcode']);
    Route::post('check_exists_article_id', [ProductController::class, 'checkExistsArticleID']);
    Route::post('update_barcode', [ProductController::class, 'updateBarcode']);
    // User Activity
    Route::get('user_activity_datatables', [UserActivityController::class, 'getDatatables']);
    // Product Stock
    Route::post('check_product_stock', [ProductStockController::class, 'checkProductStock']);
    Route::post('update_sell_price', [ProductStockController::class, 'updateSellPrice']);
    Route::post('update_purchase_price', [ProductStockController::class, 'updatePurchasePrice']);
    Route::post('update_price_tag', [ProductStockController::class, 'updatePriceTag']);


    // RESELLER POS
    Route::get('reseller_pos', [PointOfSaleController::class, 'index'])->name('reseller_pos');

    // Bandung POS
    /**
     * NOTE: open when needed
     */
    // Route::get('bandung_point_of_sale', [BandungPosController::class, 'index'])->name('bandung_point_of_sale');

    // Group
    Route::get('group_datatables', [GroupController::class, 'getDatatables']);
    Route::get('reload_group', [GroupController::class, 'reloadGroup']);
    Route::post('gr_save', [GroupController::class, 'storeData']);
    Route::post('gr_delete', [GroupController::class, 'deleteData']);

    // Stock Summary
    /**
     * NOTE:Open when needed
     */
    // Route::get('stok_summary', [StockSummaryController::class, 'index'])->name('stock_summary');

    // Invoice Report
    Route::get('invoice_report_datatables', [InvoiceReportController::class, 'getDatatables']);
    Route::get('article_report_datatables', [ArticleReportController::class, 'getDatatables']);
    Route::get('article_cross_report_datatables', [ArticleReportController::class, 'getCrossDatatables']);

    // Free Sock
    Route::post('get_free_sock', [PointOfSaleController::class, 'getFreeSock']);
    Route::post('delete_rating', [PointOfSaleController::class, 'deleteRating']);

    // Voting
    /**
     * NOTE: Open when needed
     */
    // Route::get('voting', [VotingController::class, 'index']);
    // Route::get('voting_datatables', [VotingController::class, 'getDatatables']);
    // Route::get('check_ip_datatables', [VotingController::class, 'getIpDatatables']);
    // Route::post('vc_save', [VotingController::class, 'storeData']);
    // Route::post('vc_delete', [VotingController::class, 'deleteData']);
    // Route::post('voting_reset', [VotingController::class, 'resetData']);
    // Route::post('reload_chart', [VotingController::class, 'getChart']);

    // Voting Detail
    /**
     * NOTE: Open when needed
     */
    // Route::get('voting_detail_datatables', [VotingController::class, 'getDetailDatatables']);
    // Route::get('voting_detail_result_datatables', [VotingController::class, 'getDetailResultDatatables']);
    // Route::get('voting_detail_result_ip_datatables', [VotingController::class, 'getDetailResultIpDatatables']);
    // Route::post('customer_voting_delete', [VotingController::class, 'deleteCustomerVoting']);
    // Route::post('autocomplete_voting_item', [VotingController::class, 'fetchItem']);
    // Route::post('vcd_save', [VotingController::class, 'storeDetailData']);
    // Route::post('vcd_delete', [VotingController::class, 'deleteDetailData']);
    // Route::post('exec_block_ip', [VotingController::class, 'blockIp']);

    // Client Credential
    /**
     * NOTE: Open when needed
     */
    // Route::get('reseller_api_access', [ClientCredentialController::class, 'index']);
    // Route::get('client_credential_datatables', [ClientCredentialController::class, 'getDatatables']);
    // Route::get('client_credential_transaction_datatables', [ClientCredentialController::class, 'getTransactionDatatables']);
    // Route::post('cc_save', [ClientCredentialController::class, 'storeData']);
    // Route::post('cc_delete', [ClientCredentialController::class, 'deleteData']);
    // Route::post('regenerate_api', [ClientCredentialController::class, 'regenerateApi']);

    Route::post('web_article_image_save', [WebArticleController::class, 'saveImage']);
    Route::post('delete_main_image', [WebArticleController::class, 'deleteMainImage']);
    Route::post('delete_chart_image', [WebArticleController::class, 'deleteChartImage']);
    Route::post('delete_image', [WebArticleController::class, 'deleteImage']);

    // Check Confirmation and CheckPaid 
    Route::get('check_web_confirmation', [PaymentCheckController::class, 'checkConfirmation']);
    Route::get('check_web_paid', [PaymentCheckController::class, 'checkPaid']);
    Route::post('print_web_paid', [PaymentCheckController::class, 'printPaid']);

    // Laporan Artikel
    Route::get('laporan_artikel', [ArticleInformationController::class, 'index']);
    Route::get('ai_datatables', [ArticleInformationController::class, 'getDatatables']);
    Route::get('ai_history_datatables', [ArticleInformationController::class, 'getHistoryDatatables']);
    Route::post('ai_update', [ArticleInformationController::class, 'updateData']);
    Route::post('ai_daily_update', [ArticleInformationController::class, 'autoUpdateArticleInformation']);

    // Verify Voucher
    Route::post('verify_voucher', [PointOfSaleController::class, 'verifyVoucher']);
    Route::post('verify-vouchers', [PointOfSaleController::class, 'verifyVouchers']);

    // total discount point of sale
    Route::post('pos-total-discount', [PointOfSaleController::class, 'totalDiscount']);

    // Shopee
    /**
     * NOTE: Open when needed
     */
    // Route::get('shopee_data', [ShopeeController::class, 'index']);
    // Route::get('shopee_datatables', [ShopeeController::class, 'getDatatables']);
    // Route::get('unupload_datatables', [ShopeeController::class, 'getUnuploadDatatables']);
    // Route::post('shopee_import', [ShopeeController::class, 'importData']);
    // Route::post('shopee_export', [ShopeeController::class, 'exportData']);
    // Route::post('shopee_update', [ShopeeController::class, 'updateData']);

    // Updated Dashboard
    Route::get('dashboards', [UpdatedDashboardController::class, 'index']);
    Route::get('get_cross_nettsales', [UpdatedDashboardController::class, 'getCrossNettSales']);
    Route::get('get_cross_profits', [UpdatedDashboardController::class, 'getCrossProfits']);
    Route::get('get_nettsales', [UpdatedDashboardController::class, 'getNettSales']);
    Route::get('get_profits', [UpdatedDashboardController::class, 'getProfits']);
    Route::get('get_purchases', [UpdatedDashboardController::class, 'getPurchases']);
    Route::get('get_exc_cc_assets', [UpdatedDashboardController::class, 'getEXCCCAssets']);
    Route::get('get_exc_c_assets', [UpdatedDashboardController::class, 'getEXCCAssets']);
    Route::get('get_cc_assets', [UpdatedDashboardController::class, 'getCCAssets']);
    Route::get('get_c_assets', [UpdatedDashboardController::class, 'getCAssets']);
    Route::post('get_summaries', [UpdatedDashboardController::class, 'getSummaries']);
    Route::post('load_table', [UpdatedDashboardController::class, 'loadTable']);
    Route::post('load_admin_cost', [UpdatedDashboardController::class, 'loadAdminCost']);
    Route::post('load_graph', [UpdatedDashboardController::class, 'loadGraph']);
    Route::post('load_store', [UpdatedDashboardController::class, 'loadStore']);
    Route::post('export_table', [UpdatedDashboardController::class, 'exportTable']);

    // Reseller
    /**
     * NOTE: Open when needed
     */
    // Route::get('data_reseller', [ResellerController::class, 'index']);
    // Route::get('rs_customer_datatables', [ResellerController::class, 'getDatatables']);
    // Route::post('rs_save', [ResellerController::class, 'storeData']);
    // Route::post('rs_delete', [ResellerController::class, 'deleteData']);

    // Reseller
    /**
     * NOTE: Open when needed
     */
    // Route::get('reseller_deposit', [ResellerDepositController::class, 'index']);
    // Route::get('rsd_datatables', [ResellerDepositController::class, 'getDatatables']);
    // Route::get('rsdd_datatables', [ResellerDepositController::class, 'getDetailDatatables']);
    // Route::post('rsd_save', [ResellerDepositController::class, 'saveData']);
    // Route::post('rsdd_reload', [ResellerDepositController::class, 'reloadData']);



    // ResellerLevelController
    /**
     * NOTE: Open when needed
     */
    // Route::get('reseller_level', [ResellerLevelController::class, 'index']);
    // Route::get('rl_datatables', [ResellerLevelController::class, 'getDatatables']);
    // Route::post('rl_save', [ResellerLevelController::class, 'storeData']);
    // Route::post('rl_delete', [ResellerLevelController::class, 'deleteData']);

    // ResellerAddDiscountController
    /**
     * NOTE: Open when needed
     */
    // Route::get('reseller_additional_discount', [ResellerAddDiscountController::class, 'index']);
    // Route::get('rad_datatables', [ResellerAddDiscountController::class, 'getDatatables']);
    // Route::post('rad_save', [ResellerAddDiscountController::class, 'storeData']);
    // Route::post('rad_delete', [ResellerAddDiscountController::class, 'deleteData']);

    // ResellerConfirmationController
    /**
     * NOTE: Open when needed
     */
    // Route::get('reseller_konfirmasi', [ResellerConfirmationController::class, 'index']);
    // Route::get('rc_datatables', [ResellerConfirmationController::class, 'getDatatables']);
    // Route::post('rc_save', [ResellerConfirmationController::class, 'saveData']);
    // Route::post('rc_delete', [ResellerConfirmationController::class, 'deleteData']);

    // ResellerBrandLevelController
    /**
     * NOTE: Open when needed
     */
    // Route::get('reseller_brand_level', [ResellerBrandLevelController::class, 'index']);
    // Route::get('rbl_datatables', [ResellerBrandLevelController::class, 'getDatatables']);
    // Route::post('rbl_update', [ResellerBrandLevelController::class, 'updateData']);

    // ResellerTransactionController
    /**
     * NOTE: Open when needed
     */
    // Route::get('reseller_transaction', [ResellerTransactionController::class, 'index']);
    // Route::get('rt_datatables', [ResellerTransactionController::class, 'getDatatables']);
    // Route::get('invoice_preview_datatables', [ResellerTransactionController::class, 'getInvoiceDatatables']);
    // Route::post('rt_min_qty', [ResellerTransactionController::class, 'minQty']);
    // Route::post('rt_min_item_qty', [ResellerTransactionController::class, 'minItemQty']);
    // Route::post('rt_update_status', [ResellerTransactionController::class, 'updateStatus']);

    // WebinarController
    /**
     * NOTE: Open when needed
     */
    // Route::get('webinar', [WebinarController::class, 'index']);
    // Route::get('wbr_datatables', [WebinarController::class, 'getDatatables']);

    // ResellerActivityController
    /**
     * NOTE: Open when needed
     */
    // Route::get('reseller_activity', [ResellerActivityController::class, 'index']);
    // Route::get('ra_datatables', [ResellerActivityController::class, 'getDatatables']);
    // Route::get('ra_detail_datatables', [ResellerActivityController::class, 'getDetailDatatables']);

    // AssetDetailController
    Route::get('asset_detail', [AssetDetailController::class, 'index']);
    Route::get('ad_size_datatables', [AssetDetailController::class, 'getSizeDatatables']);
    Route::get('ad_color_datatables', [AssetDetailController::class, 'getColorDatatables']);
    Route::get('ad_brand_datatables', [AssetDetailController::class, 'getBrandDatatables']);
    Route::post('ad_load_data', [AssetDetailController::class, 'loadData']);
    Route::get('ad_export', [AssetDetailController::class, 'exportData']);
    Route::post('get_asset_sales_summaries', [AssetDetailController::class, 'getSummary']);

    // UserMenuAccessController
    Route::get('uma_datatables', [UserMenuAccessController::class, 'getDatatables']);
    Route::post('uma_save', [UserMenuAccessController::class, 'storeData']);
    Route::post('uma_delete', [UserMenuAccessController::class, 'deleteData']);
    Route::post('uma_default', [UserMenuAccessController::class, 'setDefault']);

    // MainMenuController
    Route::get('main_menu', [MainMenuController::class, 'index']);
    Route::get('mm_datatables', [MainMenuController::class, 'getDatatables']);
    Route::post('mm_save', [MainMenuController::class, 'storeData']);
    Route::post('mm_delete', [MainMenuController::class, 'deleteData']);
    Route::post('mm_update', [MainMenuController::class, 'updateData']);

    // MenuAccessController
    Route::get('menu_access', [MenuAccessController::class, 'index']);
    Route::get('ma_datatables', [MenuAccessController::class, 'getDatatables']);
    Route::post('ma_save', [MenuAccessController::class, 'storeData']);
    Route::post('ma_delete', [MenuAccessController::class, 'deleteData']);

    // WebConfigController
    Route::get('pengaturan_erp', [WebConfigController::class, 'index']);
    Route::get('perp_datatables', [WebConfigController::class, 'getDatatables']);
    Route::post('perp_save', [WebConfigController::class, 'storeData']);
    Route::post('perp_delete', [WebConfigController::class, 'deleteData']);
    Route::post('reset_erp', [WebConfigController::class, 'resetERP']);

    // User Shift
    Route::post('user_start_shift', [UserShiftController::class, 'startShift']);
    Route::post('user_end_shift', [UserShiftController::class, 'endShift']);
    Route::get('check_user_shift', [UserShiftController::class, 'checkUserShift']);







});

require __DIR__ . '/purchase_order.php';
require __DIR__ . '/sales.php';
require __DIR__ . '/customer.php';
require __DIR__ . '/finance.php';
require __DIR__ . '/report.php';
require __DIR__ . '/user.php';
require __DIR__ . '/inventory.php';
require __DIR__ . '/ecommerce.php';