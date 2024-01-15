<?php

use App\Http\Controllers\POPurchaseReceiveImportController;
use App\Http\Controllers\ProductSubSubCategoryTestController;
use App\Http\Controllers\PurchaseOrderImportExcelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountClassificationController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\StockTypeController;
use App\Http\Controllers\StoreTypeController;
use App\Http\Controllers\StoreTypeDivisionController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductSupplierController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductSubCategoryController;
use App\Http\Controllers\ProductSubSubCategoryController;
use App\Http\Controllers\ProductLocationController;
use App\Http\Controllers\ProductLocationSetupController;
use App\Http\Controllers\ProductLocationSetupV2Controller;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\GenderController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerTypeController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\MainColorController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\SubColorController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderReceiveController;
use App\Http\Controllers\PurchaseOrderArticleController;
use App\Http\Controllers\PurchaseOrderArticleDetailController;
use App\Http\Controllers\PurchaseOrderArticleDetailStatusController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PointOfSaleController;
use App\Http\Controllers\BandungPosController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockTrackingController;
use App\Http\Controllers\InvoiceTrackingController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\StockDataController;
use App\Http\Controllers\StockSummaryController;
use App\Http\Controllers\PosSummaryController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\MarketplaceManagerController;
use App\Http\Controllers\NamesetDataController;
use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\ResellerPosController;
use App\Http\Controllers\DebtListController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\ProductDiscountController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\StockTransferDataController;
use App\Http\Controllers\CrossOrderController;
use App\Http\Controllers\ExceptionLocationController;
use App\Http\Controllers\InvoiceReportController;
use App\Http\Controllers\ArticleReportController;
use App\Http\Controllers\UserRatingController;
use App\Http\Controllers\HelperBackupController;
use App\Http\Controllers\VotingController;
use App\Http\Controllers\PoReceiveReportController;
use App\Http\Controllers\ClientCredentialController;

use App\Http\Controllers\TopdealController;
use App\Http\Controllers\WebArticleController;
use App\Http\Controllers\WebBannerController;
use App\Http\Controllers\WebCategoryController;
use App\Http\Controllers\WebBrandController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\WebSubCategoryController;
use App\Http\Controllers\FreeShippingController;
use App\Http\Controllers\WebTransactionController;
use App\Http\Controllers\WebConfirmationController;
use App\Http\Controllers\PaymentCheckController;
use App\Http\Controllers\WebReminderController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\ArticleInformationController;
use App\Http\Controllers\B1g1Controller;
use App\Http\Controllers\DashboardV2Controller;
use App\Http\Controllers\QtyExceptionController;
use App\Http\Controllers\ShopeeController;
use App\Http\Controllers\UpcloudBalanceController;
use App\Http\Controllers\InvestorController;
use App\Http\Controllers\UpdatedDashboardController;
use App\Http\Controllers\MassAdjustmentController;
use App\Http\Controllers\ScanAdjustmentController;
use App\Http\Controllers\POReceiveApprovalController;
use App\Http\Controllers\ResellerController;
use App\Http\Controllers\ResellerDepositController;
use App\Http\Controllers\StockCardController;
use App\Http\Controllers\ResellerLevelController;
use App\Http\Controllers\ResellerAddDiscountController;
use App\Http\Controllers\ResellerConfirmationController;
use App\Http\Controllers\ResellerBrandLevelController;
use App\Http\Controllers\ResellerTransactionController;
use App\Http\Controllers\WebinarController;
use App\Http\Controllers\ResellerActivityController;
use App\Http\Controllers\AssetDetailController;
use App\Http\Controllers\StoreAgingController;
use App\Http\Controllers\InstockApprovalController;
use App\Http\Controllers\InvoiceEditorController;
use App\Http\Controllers\InstockListController;
use App\Http\Controllers\UserManagementController;
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

Route::group(['middleware' => 'auth'], function() {
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

    // Investor
    Route::get('investor', [InvestorController::class, 'index']);
    Route::get('i_datatables', [InvestorController::class, 'getDatatables']);
    Route::post('i_save', [InvestorController::class, 'storeData']);
    Route::post('i_delete', [InvestorController::class, 'deleteData']);
    Route::post('i_username', [InvestorController::class, 'checkUsername']);

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
    // Customer
    Route::get('customer', [CustomerController::class, 'index'])->name('customer');
    Route::get('customer_datatables', [CustomerController::class, 'getDatatables']);
    Route::get('customer_detail_datatables', [CustomerController::class, 'getDetailDatatables']);
    Route::get('customer_transaction_datatables', [CustomerController::class, 'customerTransactionDatatables']);
    Route::get('reload_customer', [CustomerController::class, 'reloadCustomer']);
    Route::get('reload_customer_by_division', [CustomerController::class, 'reloadCustomerByDivision']);
    Route::get('reload_sub_customer_by_division', [CustomerController::class, 'reloadSubCustomerByDivision']);
    Route::get('reload_city', [CustomerController::class, 'reloadCity']);
    Route::get('reload_subdistrict', [CustomerController::class, 'reloadSubdistrict']);
    Route::post('get_customer_graph', [CustomerController::class, 'loadGraph']);
    Route::post('cust_save', [CustomerController::class, 'storeData']);
    Route::post('cust_delete', [CustomerController::class, 'deleteData']);
    Route::post('cust_import', [CustomerController::class, 'importData']);
    Route::post('check_exists_customer', [CustomerController::class, 'checkExistsCustomer']);
    Route::post('check_customer', [CustomerController::class, 'checkCustomer']);
    Route::post('autocomplete_customer', [CustomerController::class, 'fetchCustomer']);
    Route::get('store_traffic', [CustomerController::class, 'storeTraffic']);
    Route::post('update_traffic_customer', [CustomerController::class, 'updateTrafficCustomer']);
    Route::get('count_total_customer_type', [CustomerController::class, 'countTotalCustomerType']);

    // Tax
    Route::get('data_pajak', [TaxController::class, 'index'])->name('tax');
    Route::get('tax_datatables', [TaxController::class, 'getDatatables']);
    Route::post('tx_save', [TaxController::class, 'storeData']);
    Route::post('tx_delete', [TaxController::class, 'deleteData']);
    Route::post('tx_import', [TaxController::class, 'importData']);
    Route::post('check_exists_tax', [TaxController::class, 'checkExistsTax']);
    // Payment Method
    Route::get('metode_pembayaran', [PaymentMethodController::class, 'index'])->name('payment_method');
    Route::get('payment_method_datatables', [PaymentMethodController::class, 'getDatatables']);
    Route::post('pm_save', [PaymentMethodController::class, 'storeData']);
    Route::post('pm_delete', [PaymentMethodController::class, 'deleteData']);
    Route::post('pm_import', [PaymentMethodController::class, 'importData']);
    Route::post('check_exists_pm', [PaymentMethodController::class, 'checkExistsPm']);
    // Stock Type
    Route::get('tipe_stok', [StockTypeController::class, 'index'])->name('stock_type');
    Route::get('stock_type_datatables', [StockTypeController::class, 'getDatatables']);
    Route::post('stkt_save', [StockTypeController::class, 'storeData']);
    Route::post('stkt_delete', [StockTypeController::class, 'deleteData']);
    Route::post('stkt_import', [StockTypeController::class, 'importData']);
    Route::post('check_exists_stock_type', [StockTypeController::class, 'checkExistsStockType']);
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
    // Marketplace Manager
    Route::get('marketplace_manager', [MarketplaceManagerController::class, 'index']);
    Route::get('marketplace_stock_datatables', [MarketplaceManagerController::class, 'stockDatatables']);
    Route::get('master_datatables', [MarketplaceManagerController::class, 'masterDatatables']);
    Route::post('check_marketplace_code', [MarketplaceManagerController::class, 'checkCode']);
    Route::post('update_marketplace_code', [MarketplaceManagerController::class, 'updateCode']);
    Route::post('marketplace_stock_refresh', [MarketplaceManagerController::class, 'refreshData']);
    Route::post('marketplace_template_import', [MarketplaceManagerController::class, 'importData']);
    Route::get('fetch_master', [MarketplaceManagerController::class, 'fetchData']);
    // Product Main Color
    Route::get('warna_produk', [MainColorController::class, 'index'])->name('main_color');
    Route::get('main_color_datatables', [MainColorController::class, 'getDatatables']);
    Route::post('mc_save', [MainColorController::class, 'storeData']);
    Route::post('mc_delete', [MainColorController::class, 'deleteData']);
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
    Route::get('reload_size_schema',[SizeController::class, 'reloadSizeSchema']);
    Route::post('sz_save', [SizeController::class, 'storeData']);
    Route::post('sz_delete', [SizeController::class, 'deleteData']);
    Route::post('sz_import', [SizeController::class, 'importData']);
    Route::post('check_exists_size', [SizeController::class, 'checkExistsSize']);
    // Product Location
    Route::get('lokasi_simpan', [ProductLocationController::class, 'index'])->name('product_location');
    Route::get('product_location_datatables', [ProductLocationController::class, 'getDatatables']);
    Route::post('pl_save', [ProductLocationController::class, 'storeData']);
    Route::post('pl_delete', [ProductLocationController::class, 'deleteData']);
    Route::post('pl_import', [ProductLocationController::class, 'importData']);
    Route::post('pl_code_check_data', [ProductLocationController::class, 'checkCode']);
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
    Route::post('check_exists_product_code', [ProductController::class, 'checkExistsProductCode']);
    Route::post('update_barcode', [ProductController::class, 'updateBarcode']);
    // User Activity
    Route::get('user_activity_datatables', [UserActivityController::class, 'getDatatables']);
    // Product Stock
    Route::post('check_product_stock', [ProductStockController::class, 'checkProductStock']);
    Route::post('update_sell_price', [ProductStockController::class, 'updateSellPrice']);
    Route::post('update_purchase_price', [ProductStockController::class, 'updatePurchasePrice']);
    Route::post('update_price_tag', [ProductStockController::class, 'updatePriceTag']);
    // Product Location Setup
    Route::get('setup_lokasi_stok', [ProductLocationSetupController::class, 'index'])->name('product_location_setup');
    Route::get('product_in_location_datatables', [ProductLocationSetupController::class, 'getDatatablesLocation']);
    Route::get('product_location_setup_datatables', [ProductLocationSetupController::class, 'getDatatables']);
    Route::get('product_item_location_datatables', [ProductController::class, 'getDatatablesLocation']);
    Route::get('pl_export', [ProductLocationSetupController::class, 'exportData']);
    Route::post('pls_save', [ProductLocationSetupController::class, 'storeData']);
    Route::post('pls_delete', [ProductLocationSetupController::class, 'deleteData']);
    Route::post('check_product_in_location', [ProductLocationSetupController::class, 'checkProductInLocation']);
    Route::post('sv_mutation', [ProductLocationSetupController::class, 'productMutation']);
    Route::post('sv_setup', [ProductLocationSetupController::class, 'productSetup']);
    Route::post('bin_reset_exec', [ProductLocationSetupController::class, 'binResetExec']);
    // Product Location Setup V2 
    Route::get('setup_lokasi_stok_v2', [ProductLocationSetupV2Controller::class, 'index'])->name('product_location_setup_v2');
    Route::get('start_bin_datatables', [ProductLocationSetupV2Controller::class, 'startBinDatatables']);
    Route::get('check_total_qty_in_bin', [ProductLocationSetupV2Controller::class, 'totalQtyInBin']);
    Route::get('end_bin_datatables', [ProductLocationSetupV2Controller::class, 'endBinDatatables']);
    Route::get('bin_history_datatables', [ProductLocationSetupV2Controller::class, 'binHistoryDatatables']);
    Route::get('reload_start_bin', [ProductLocationSetupV2Controller::class, 'reloadStartBin']);
    Route::get('reload_end_bin', [ProductLocationSetupV2Controller::class, 'reloadEndBin']);
    Route::get('history_setup_export', [ProductLocationSetupV2Controller::class, 'exportData']);
    Route::post('sv_mutation_v2', [ProductLocationSetupV2Controller::class, 'productMutation']);
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


    // Purchase Order
    Route::get('pembelian', [PurchaseOrderController::class, 'index'])->name('purchase_order');
    Route::get('purchase_order_datatables', [PurchaseOrderController::class, 'getDatatables']);
    Route::get('product_item_datatables', [ProductController::class, 'getDatatablesItem']);
    Route::post('create_po', [PurchaseOrderController::class, 'createPo']);
    Route::post('create_po_detail', [PurchaseOrderController::class, 'createPoDetail']);
    Route::post('check_po_detail', [PurchaseOrderController::class, 'checkPoDetail']);
    Route::post('reload_po_detail', [PurchaseOrderController::class, 'reloadPoDetail']);
    Route::post('cancel_po', [PurchaseOrderController::class, 'cancelPo']);
    Route::post('po_save', [PurchaseOrderController::class, 'storeData']);
    Route::post('po_delete', [PurchaseOrderController::class, 'deleteData']);
    Route::post('check_product_po', [PurchaseOrderController::class, 'checkProductPo']);
    Route::post('po_choose_store', [PurchaseOrderController::class, 'chooseStorePo']);
    Route::post('po_choose_supplier', [PurchaseOrderController::class, 'chooseSupplierPo']);
    Route::post('po_choose_stock_type', [PurchaseOrderController::class, 'chooseStockType']);
    Route::post('po_choose_tax', [PurchaseOrderController::class, 'chooseTaxPo']);
    Route::post('po_choose_payment', [PurchaseOrderController::class, 'choosePaymentPo']);
    Route::post('po_description', [PurchaseOrderController::class, 'descriptionPo']);
    Route::post('po_shipping_cost', [PurchaseOrderController::class, 'shippingCostPo']);
    Route::post('po_save_draft', [PurchaseOrderController::class, 'poSaveDraft']);
    Route::post('po_detail', [PurchaseOrderController::class, 'poDetail']);
    Route::post('po_import', [PurchaseOrderImportExcelController::class, 'importExcel']);
    Route::post('po_invoice_image', [PurchaseOrderController::class, 'upladImageInvoice']);
    Route::get('po_article_export', [PurchaseOrderController::class, 'exportPurchaseOrderArticleData']);

    // Purchase Order Receive
    Route::get('penerimaan', [PurchaseOrderReceiveController::class, 'index'])->name('purchase_order_receive');
    Route::post('check_po_receive_detail', [PurchaseOrderReceiveController::class, 'checkPoReceiveDetail']);
    Route::post('po_receive_detail', [PurchaseOrderReceiveController::class, 'poReceiveDetail']);
    Route::post('po_export', [PurchaseOrderReceiveController::class, 'poExport']);
    Route::get('por_export', [PurchaseOrderReceiveController::class, 'exportData']);
    Route::post('por_import', [POPurchaseReceiveImportController::class, 'importExcel']);
    Route::get('po_invoice_image_datatable', [PurchaseOrderReceiveController::class, 'getImageInvoiceDatatables']);
    Route::post('po_delivery_order_image', [PurchaseOrderReceiveController::class, 'uploadDeliveryOrdersImage']);
    Route::post('po_invoice_image_delete', [PurchaseOrderReceiveController::class, 'deleteImageInvoice']);
    ROute::get('po_delivery_order_image_datatable',[PurchaseOrderReceiveController::class, 'getImageDeliveryOrdersDatatables']);
    Route::post('po_delivery_order_image_delete', [PurchaseOrderReceiveController::class, 'deleteImagePOSuratJalan']);
    Route::post('check_barcode_import', [PurchaseOrderReceiveController::class, 'checkBarcodeImport']);

    // Purchase Order Article
    Route::post('poa_delete', [PurchaseOrderArticleController::class, 'deleteData']);
    Route::post('poa_save_discount', [PurchaseOrderArticleController::class, 'saveDiscount']);
    Route::post('poa_save_extra_discount', [PurchaseOrderArticleController::class, 'saveExtraDiscount']);
    Route::post('poa_save_reminder', [PurchaseOrderArticleController::class, 'saveReminder']);

    // Purchase Order Article Detail
    Route::post('poad_delete', [PurchaseOrderArticleDetailController::class, 'deleteData']);
    Route::post('poad_save_purchase_price', [PurchaseOrderArticleDetailController::class, 'savePurchasePrice']);
    Route::post('poad_save_qty_total', [PurchaseOrderArticleDetailController::class, 'saveQtyTotal']);

    // Purchase Order Article Detail Status
    Route::post('poads_save', [PurchaseOrderArticleDetailStatusController::class, 'storeData']);
    Route::get('poads_datatables', [PurchaseOrderArticleDetailStatusController::class, 'getDatatables']);
    Route::post('sv_poads_revision', [PurchaseOrderArticleDetailStatusController::class, 'revisionData']);
    Route::post('dl_poads_revision', [PurchaseOrderArticleDetailStatusController::class, 'deleteData']);
    Route::post('check_po_invoice', [PurchaseOrderArticleDetailStatusController::class, 'checkInvoice']);

	// RESELLER POS
	Route::get('reseller_pos', [PointOfSaleController::class, 'index'])->name('reseller_pos');

    // Bandung POS
    Route::get('bandung_point_of_sale', [BandungPosController::class, 'index'])->name('bandung_point_of_sale');

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

    // Account Type
    Route::get('jenis_akun', [AccountTypeController::class, 'index'])->name('account_type');
    Route::get('account_type_datatables', [AccountTypeController::class, 'getDatatables']);
    Route::post('at_save', [AccountTypeController::class, 'storeData']);
    Route::post('at_delete', [AccountTypeController::class, 'deleteData']);
    Route::post('check_exists_account_type', [AccountTypeController::class, 'checkExistsAccountType']);

    // Account Klasifikasi
    Route::get('klasifikasi_akun', [AccountClassificationController::class, 'index'])->name('account_classification');
    Route::get('account_classification_datatables', [AccountClassificationController::class, 'getDatatables']);
    Route::post('ac_save', [AccountClassificationController::class, 'storeData']);
    Route::post('ac_delete', [AccountClassificationController::class, 'deleteData']);
    Route::post('check_exists_account_classification', [AccountClassificationController::class, 'checkExistsAccountClassification']);

    // Account
    Route::get('data_akun', [AccountController::class, 'index'])->name('account');
    Route::get('account_datatables', [AccountController::class, 'getDatatables']);
    Route::post('a_save', [AccountController::class, 'storeData']);
    Route::post('a_delete', [AccountController::class, 'deleteData']);
    Route::post('check_exists_account', [AccountController::class, 'checkExistsAccount']);
    Route::post('check_exists_account_code', [AccountController::class, 'checkExistsAccountCode']);

    // Group
    Route::get('group_datatables', [GroupController::class, 'getDatatables']);
    Route::get('reload_group', [GroupController::class, 'reloadGroup']);
    Route::post('gr_save', [GroupController::class, 'storeData']);
    Route::post('gr_delete', [GroupController::class, 'deleteData']);

    // UserController
    Route::get('load_user_store', [UserController::class, 'loadStore']);
    Route::get('data_user', [UserController::class, 'index']);
    Route::get('user_datatables', [UserController::class, 'getDatatables']);
    Route::post('u_save', [UserController::class, 'storeData']);
    Route::post('u_delete', [UserController::class, 'deleteData']);
    Route::post('check_exists_secret_code', [UserController::class, 'checkExistsSecretCode']);
    Route::post('autocomplete_menu', [UserController::class, 'autocompleteMenu']);
    Route::post('autocomplete_store', [UserController::class, 'autocompleteStore']);
    Route::post('load_user_menu', [UserController::class, 'loadUserMenu']);

    // Stock Tracking
    Route::get('stock_tracking', [StockTrackingController::class, 'index'])->name('stock_tracking');
    Route::get('stock_tracking_datatables', [StockTrackingController::class, 'getDatatables']);
    Route::get('pickup_list_datatables', [StockTrackingController::class, 'getPickupDatatables']);
    Route::post('get_stock_notice', [StockTrackingController::class, 'getNotice']);
    Route::post('get_stock_graph', [StockTrackingController::class, 'getGraph']);
    Route::post('pickup_item', [StockTrackingController::class, 'pickupItem']);
    Route::post('pickup_approval_item', [StockTrackingController::class, 'pickupApprovalItem']);
    Route::post('cancel_pickup_item', [StockTrackingController::class, 'cancelPickupItem']);
    Route::post('plst_delete', [StockTrackingController::class, 'deleteData']);

	// Stock Summary
    Route::get('stok_summary', [StockSummaryController::class, 'index'])->name('stock_summary');
    // Invoice Tracking
    Route::get('invoice_tracking', [InvoiceTrackingController::class, 'index'])->name('invoice_tracking');
    Route::get('invoice_tracking_datatables', [InvoiceTrackingController::class, 'getDatatables']);
    Route::post('shipping_number_save', [InvoiceTrackingController::class, 'updateData']);
    Route::post('waybill_tracking', [InvoiceTrackingController::class, 'waybillTracking']);
    Route::post('all_waybill_tracking', [InvoiceTrackingController::class, 'allWaybillTracking']);

    // Invoice 
    Route::post('search_invoice', [InvoiceController::class, 'searchInvoice']);

    // Product Main Color
    Route::get('kurir_pengiriman', [CourierController::class, 'index'])->name('courier');
    Route::get('courier_datatables', [CourierController::class, 'getDatatables']);
    Route::post('cr_save', [CourierController::class, 'storeData']);
    Route::post('cr_delete', [CourierController::class, 'deleteData']);

    // Stock Data
    Route::get('data_stok', [StockDataController::class, 'index'])->name('stock_data');
    Route::get('stock_data_datatables', [StockDataController::class, 'getDatatables']);
    Route::get('aging_datatables', [StockDataController::class, 'getAgingDatatables']);
    Route::get('helper_stock_data_datatables', [StockDataController::class, 'getHelperDatatables']);
    Route::post('stock_data_reload_category', [StockDataController::class, 'reloadCategory']);
    Route::post('stock_data_reload_sub_category', [StockDataController::class, 'reloadSubCategory']);
    Route::post('stock_data_reload_sub_sub_category', [StockDataController::class, 'reloadSubSubCategory']);
    Route::post('stock_data_reload_brand', [StockDataController::class, 'reloadBrand']);
    Route::post('stock_data_reload_size', [StockDataController::class, 'reloadSize']);

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

    // Pos Summary
    Route::get('pos_summary', [PosSummaryController::class, 'index'])->name('pos_summary');
    Route::get('pos_summary_online_datatables', [PosSummaryController::class, 'onlineDatatables']);
    Route::get('pos_summary_offline_datatables', [PosSummaryController::class, 'offlineDatatables']);
    Route::get('sales_detail_datatables', [PosSummaryController::class, 'salesDetailDatatables']);
    Route::get('sales_item_detail_datatables', [PosSummaryController::class, 'salesItemDetailDatatables']);
    Route::post('target_chart', [PosSummaryController::class, 'targetChart']);
    Route::post('cross_chart', [PosSummaryController::class, 'crossChart']);

    // Adjustment
    Route::get('adjustment', [AdjustmentController::class, 'index'])->name('adjustment');
    Route::get('_datatables', [AdjustmentController::class, 'historyDatatables']);
    Route::get('validated_datatables', [AdjustmentController::class, 'validatedDatatables']);
    Route::get('not_validated_datatables', [AdjustmentController::class, 'notValidatedDatatables']);
    Route::get('adjustment_history_datatables', [AdjustmentController::class, 'adjustmentHistoryDatatables']);
    Route::get('article_adjustment_datatables', [AdjustmentController::class, 'articleDatatables']);
    Route::get('reload_location', [AdjustmentController::class, 'reloadLocation']);
    Route::post('sv_adjustment', [AdjustmentController::class, 'productAdjustment']);
    Route::post('finish_adjustment', [AdjustmentController::class, 'finishAdjustment']);
    Route::post('add_article_adjustment', [AdjustmentController::class, 'addArticle']);
    Route::post('autocomplete_article', [AdjustmentController::class, 'fetchArticle']);

    // Nameset Data
    Route::get('data_nameset', [NamesetDataController::class, 'index'])->name('nameset_data');
    Route::get('nameset_datatables', [NamesetDataController::class, 'getDatatables']);
    Route::post('update_data', [NamesetDataController::class, 'updateData']);

    // Report 
    Route::get('laporan_penjualan', [SalesReportController::class, 'index'])->name('sales_report');
    Route::get('sales_report_datatables', [SalesReportController::class, 'getDatatables']);
    Route::get('check_hb_hj', [SalesReportController::class, 'hbhjDatatables']);
    Route::get('sales_export', [SalesReportController::class, 'exportData']);
    Route::post('cabang_summary', [SalesReportController::class, 'cabangSummary']);

    // Invoice Report
    Route::get('invoice_report_datatables', [InvoiceReportController::class, 'getDatatables']);
    Route::get('article_report_datatables', [ArticleReportController::class, 'getDatatables']);
    Route::get('article_cross_report_datatables', [ArticleReportController::class, 'getCrossDatatables']);

    // Debt List
    Route::get('daftar_hutang', [DebtListController::class, 'index'])->name('debt_list');
    Route::get('debt_list_datatables', [DebtListController::class, 'getDatatables']);
    Route::get('payment_datatables', [DebtListController::class, 'paymentDatatables']);
    Route::post('dl_save', [DebtListController::class, 'storeData']);
    Route::post('dl_delete', [DebtListController::class, 'deleteData']);
    Route::post('dlp_save', [DebtListController::class, 'storeDataPayment']);
    Route::post('dlp_delete', [DebtListController::class, 'deleteDataPayment']);
    Route::post('debt_import', [DebtListController::class, 'importData']);

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

    // Cross Order
    Route::get('cross_order', [CrossOrderController::class, 'index'])->name('store');
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

    // Exception Location
    Route::get('exception_location', [ExceptionLocationController::class, 'index']);
    Route::get('exception_location_datatables', [ExceptionLocationController::class, 'getDatatables']);
    Route::post('el_save', [ExceptionLocationController::class, 'storeData']);
    Route::post('el_delete', [ExceptionLocationController::class, 'deleteData']);
    
    // B1G1 Location
    Route::get('b1g1_location', [B1g1Controller::class, 'index']);
    Route::get('b1g1_location_datatables', [B1g1Controller::class, 'getDatatables']);
    Route::post('b1g1_save', [B1g1Controller::class, 'storeData']);
    Route::post('b1g1_delete', [B1g1Controller::class, 'deleteData']);
    Route::post('b1g1_update', [B1g1Controller::class, 'updateData']);

    // User Rating
    Route::get('user_rating', [UserRatingController::class, 'index']);
    Route::get('rating_by_customer', [UserRatingController::class, 'customerIndex']);
    Route::get('rating_app', [UserRatingController::class, 'customerIndex']);
    Route::get('rating_datatables', [UserRatingController::class, 'getDatatables']);
    Route::get('rating_history_datatables', [UserRatingController::class, 'getHistoryDatatables']);
    Route::post('autocomplete_subdistrict', [UserRatingController::class, 'fetchSubdistrict']);
    Route::post('autocomplete_city', [UserRatingController::class, 'fetchCity']);
    Route::post('check_waiting_for_review', [UserRatingController::class, 'checkWaitingForReview']);
    Route::post('save_rating', [UserRatingController::class, 'storeData']);
    Route::post('check_customer_rating_phone', [UserRatingController::class, 'checkCustomerPhone']);
    Route::post('check_rating_for_checkout', [UserRatingController::class, 'checkWaitingForCheckout']);

    // Free Sock
    Route::post('get_free_sock', [PointOfSaleController::class, 'getFreeSock']);
    Route::post('delete_rating', [PointOfSaleController::class, 'deleteRating']);
    
    // Voting
    Route::get('voting', [VotingController::class, 'index']);
    Route::get('voting_datatables', [VotingController::class, 'getDatatables']);
    Route::get('check_ip_datatables', [VotingController::class, 'getIpDatatables']);
    Route::post('vc_save', [VotingController::class, 'storeData']);
    Route::post('vc_delete', [VotingController::class, 'deleteData']);
    Route::post('voting_reset', [VotingController::class, 'resetData']);
    Route::post('reload_chart', [VotingController::class, 'getChart']);

    // Voting Detail
    Route::get('voting_detail_datatables', [VotingController::class, 'getDetailDatatables']);
    Route::get('voting_detail_result_datatables', [VotingController::class, 'getDetailResultDatatables']);
    Route::get('voting_detail_result_ip_datatables', [VotingController::class, 'getDetailResultIpDatatables']);
    Route::post('customer_voting_delete', [VotingController::class, 'deleteCustomerVoting']);
    Route::post('autocomplete_voting_item', [VotingController::class, 'fetchItem']);
    Route::post('vcd_save', [VotingController::class, 'storeDetailData']);
    Route::post('vcd_delete', [VotingController::class, 'deleteDetailData']);
    Route::post('exec_block_ip', [VotingController::class, 'blockIp']);
    
    // Po Receive Report
    Route::get('laporan_datang_barang', [PoReceiveReportController::class, 'index']);
    Route::get('po_receive_datatables', [PoReceiveReportController::class, 'getDatatables']);
    Route::get('po_receive_detail_datatables', [PoReceiveReportController::class, 'getDetailDatatables']);
    Route::get('po_receive_export', [PoReceiveReportController::class, 'exportData']);
    
    // Client Credential
    Route::get('reseller_api_access', [ClientCredentialController::class, 'index']);
    Route::get('client_credential_datatables', [ClientCredentialController::class, 'getDatatables']);
    Route::get('client_credential_transaction_datatables', [ClientCredentialController::class, 'getTransactionDatatables']);
    Route::post('cc_save', [ClientCredentialController::class, 'storeData']);
    Route::post('cc_delete', [ClientCredentialController::class, 'deleteData']);
    Route::post('regenerate_api', [ClientCredentialController::class, 'regenerateApi']);
    
    // Web Article 
    Route::get('web_artikel', [WebArticleController::class, 'index']);
    Route::get('web_article_datatables', [WebArticleController::class, 'getDatatables']);
    Route::post('generate_slug', [WebArticleController::class, 'generateSlug']);
    Route::post('save_description', [WebArticleController::class, 'saveDescription']);
    Route::post('update_main_image', [WebArticleController::class, 'saveMainImage']);
    Route::post('update_detail_image', [WebArticleController::class, 'saveDetailImage']);
    Route::post('save_weight', [WebArticleController::class, 'saveWeight']);
    
    Route::post('web_article_image_save', [WebArticleController::class, 'saveImage']);
    Route::post('delete_main_image', [WebArticleController::class, 'deleteMainImage']);
    Route::post('delete_chart_image', [WebArticleController::class, 'deleteChartImage']);
    Route::post('delete_image', [WebArticleController::class, 'deleteImage']);

    // Topdeal
    Route::get('topdeals', [TopdealController::class, 'index']);
    Route::get('topdeals_datatables', [TopdealController::class, 'getDatatables']);
    Route::get('topdeals_article_datatables', [TopdealController::class, 'getTopdealsArticleDatatables']);
    Route::get('article_datatables', [TopdealController::class, 'getArticleDatatables']);
    Route::post('td_save', [TopdealController::class, 'storeData']);
    Route::post('td_delete', [TopdealController::class, 'deleteData']);
    Route::post('add_topdeals', [TopdealController::class, 'addTopdeals']);
    Route::post('delete_topdeals', [TopdealController::class, 'deleteTopdeals']);

    // Web Banner
    Route::get('web_banner', [WebBannerController::class, 'index']);
    Route::get('wb_datatables', [WebBannerController::class, 'getDatatables']);
    Route::get('bb_brand_datatables', [WebBannerController::class, 'getBrandDatatables']);
    Route::get('bb_article_datatables', [WebBannerController::class, 'getArticleDatatables']);
    Route::get('reload_article', [WebBannerController::class, 'reloadArticle']);
    Route::post('wb_save', [WebBannerController::class, 'storeData']);
    Route::post('wb_delete', [WebBannerController::class, 'deleteData']);
    Route::post('bb_save', [WebBannerController::class, 'storeDataBrand']);
    Route::post('bb_delete', [WebBannerController::class, 'deleteDataBrand']);
    Route::post('bbd_save', [WebBannerController::class, 'storeDataArticle']);
    Route::post('bbd_delete', [WebBannerController::class, 'deleteDataArticle']);

    // Bank
    Route::get('bank', [BankController::class, 'index']);
    Route::get('bank_datatables', [BankController::class, 'getDatatables']);
    Route::post('bank_save', [BankController::class, 'storeData']);
    Route::post('bank_delete', [BankController::class, 'deleteData']);
    Route::post('check_exists_bank', [BankController::class, 'checkExistsBank']);

    // Web Category
    Route::get('kategori_slug', [WebCategoryController::class, 'index']);
    Route::get('web_category_datatables', [WebCategoryController::class, 'getDatatables']);
    Route::post('cs_save', [WebCategoryController::class, 'storeData']);
    Route::post('cs_delete', [WebCategoryController::class, 'deleteData']);

    // Web Brand
    Route::get('web_brand', [WebBrandController::class, 'index']);

    // Voucher
    Route::get('voucher', [VoucherController::class, 'index']);
    Route::get('voucher_datatables', [VoucherController::class, 'getDatatables']);
    Route::post('voucher_save', [VoucherController::class, 'storeData']);
    Route::post('voucher_delete', [VoucherController::class, 'deleteData']);
    Route::post('check_exists_voucher', [VoucherController::class, 'checkExistsVoucher']);

    // Web Sub Kategori
    Route::get('sub_kategori', [WebSubCategoryController::class, 'index']);
    Route::get('wsc_datatables', [WebSubCategoryController::class, 'getDatatables']);
    Route::post('wsc_save', [WebSubCategoryController::class, 'storeData']);

    // Free Shipping Controller
    Route::get('free_ongkir', [FreeShippingController::class, 'index']);
    Route::get('free_shipping_datatables', [FreeShippingController::class, 'getDatatables']);
    Route::post('fs_save', [FreeShippingController::class, 'storeData']);
    Route::post('fs_delete', [FreeShippingController::class, 'deleteData']);

    // Web TRANSACTION
    Route::get('website_transaction', [WebTransactionController::class, 'index']);
    Route::get('wt_datatables', [WebTransactionController::class, 'getDatatables']);
    Route::post('wt_save', [WebTransactionController::class, 'storeData']);

    // Web Confirmation
    Route::get('konfirmasi', [WebConfirmationController::class, 'index']);
    Route::get('konfirmasi_datatables', [WebConfirmationController::class, 'getDatatables']);
    Route::post('wbc_save', [WebConfirmationController::class, 'storeData']);
    Route::post('wbc_delete', [WebConfirmationController::class, 'deleteData']);
    Route::post('wbc_read', [WebConfirmationController::class, 'readData']);

    // Check Confirmation and CheckPaid 
    Route::get('check_web_confirmation', [PaymentCheckController::class, 'checkConfirmation']);
    Route::get('check_web_paid', [PaymentCheckController::class, 'checkPaid']);
    Route::post('print_web_paid', [PaymentCheckController::class, 'printPaid']);

    // Web Reminder
    Route::get('web_reminder', [WebReminderController::class, 'index']);
    Route::get('wr_datatables', [WebReminderController::class, 'getDatatables']);

    // Blog 
    Route::get('blog', [BlogController::class, 'index']);
    Route::get('bcc_datatables', [BlogController::class, 'getDetailDatatables']);
    Route::post('get_bc', [BlogController::class, 'getData']);
    Route::post('bc_save', [BlogController::class, 'storeData']);
    Route::post('bc_delete', [BlogController::class, 'deleteData']);
    Route::post('bcc_save', [BlogController::class, 'storeDetailData']);
    Route::post('bcc_delete', [BlogController::class, 'deleteDetailData']);

    // Whatsapp
    Route::get('whatsapp', [WhatsappController::class, 'index']);
    Route::get('whatsapp_datatables', [WhatsappController::class, 'getDatatables']);
    Route::post('send_wa', [WhatsappController::class, 'executeBlast']);

    // Laporan Artikel
    Route::get('laporan_artikel', [ArticleInformationController::class, 'index']);
    Route::get('ai_datatables', [ArticleInformationController::class, 'getDatatables']);
    Route::get('ai_history_datatables', [ArticleInformationController::class, 'getHistoryDatatables']);
    Route::post('ai_update', [ArticleInformationController::class, 'updateData']);
    Route::post('ai_daily_update', [ArticleInformationController::class, 'autoUpdateArticleInformation']);

    // Qty Exception
    Route::get('qty_exception', [QtyExceptionController::class, 'index']);
    Route::get('qe_datatables', [QtyExceptionController::class, 'getDatatables']);
    Route::post('qe_save', [QtyExceptionController::class, 'storeData']);
    Route::post('qe_delete', [QtyExceptionController::class, 'deleteData']);

    // Verify Voucher
    Route::post('verify_voucher', [PointOfSaleController::class, 'verifyVoucher']);
    Route::post('verify-vouchers', [PointOfSaleController::class, 'verifyVouchers']);

    // total discount point of sale
    Route::post('pos-total-discount', [PointOfSaleController::class, 'totalDiscount']);

    // Shopee
    Route::get('shopee_data', [ShopeeController::class, 'index']);
    Route::get('shopee_datatables', [ShopeeController::class, 'getDatatables']);
    Route::get('unupload_datatables', [ShopeeController::class, 'getUnuploadDatatables']);
    Route::post('shopee_import', [ShopeeController::class, 'importData']);
    Route::post('shopee_export', [ShopeeController::class, 'exportData']);
    Route::post('shopee_update', [ShopeeController::class, 'updateData']);

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
    Route::post('fetch_start_scan_adjustment_article', [ScanAdjustmentController::class, 'fetchArticle']);
    Route::post('min_plus_start_scan_adjustment', [ScanAdjustmentController::class, 'minPlus']);
    Route::post('scan_adjustment_manual', [ScanAdjustmentController::class, 'manual']);
    Route::post('fetch_start_scan_adjustment_article_barcode', [ScanAdjustmentController::class, 'fetchArticleBarcode']);
    Route::post('fetch_start_scan_adjustment_custom', [ScanAdjustmentController::class, 'fetchCustom']);
    Route::post('add_start_scan_adjustment_custom', [ScanAdjustmentController::class, 'addCustom']);
    Route::post('delete_start_scan_adjustment_custom', [ScanAdjustmentController::class, 'deleteCustom']);
    Route::get('export_start_scan_adjustment_bin', [ScanAdjustmentController::class, 'exportBIN']);
    Route::post('scan_adjustment_qty_update', [ScanAdjustmentController::class, 'updateQty']);
    
    Route::post('pos_barcode_scan', [PointOfSaleController::class, 'scanBarcode']);

    // POReceiveApprovalController
    Route::get('approval_penerimaan', [POReceiveApprovalController::class, 'index']);
    Route::get('ap_datatables', [POReceiveApprovalController::class, 'getDatatables']);
    Route::get('apd_datatables', [POReceiveApprovalController::class, 'getDetailDatatables']);
    Route::post('ap_save', [POReceiveApprovalController::class, 'saveData']);
    Route::post('ap_delete', [POReceiveApprovalController::class, 'deleteData']);
    Route::post('apd_approve', [POReceiveApprovalController::class, 'approveData']);

    // Reseller
    Route::get('data_reseller', [ResellerController::class, 'index']);
    Route::get('rs_customer_datatables', [ResellerController::class, 'getDatatables']);
    Route::post('rs_save', [ResellerController::class, 'storeData']);
    Route::post('rs_delete', [ResellerController::class, 'deleteData']);

    // Reseller
    Route::get('reseller_deposit', [ResellerDepositController::class, 'index']);
    Route::get('rsd_datatables', [ResellerDepositController::class, 'getDatatables']);
    Route::get('rsdd_datatables', [ResellerDepositController::class, 'getDetailDatatables']);
    Route::post('rsd_save', [ResellerDepositController::class, 'saveData']);
    Route::post('rsdd_reload', [ResellerDepositController::class, 'reloadData']);

    // StockCardController
    Route::get('stock_card', [StockCardController::class, 'index']);
    Route::get('stc_article_datatables', [StockCardController::class, 'getADatatables']);
    Route::post('stc_save', [StockCardController::class, 'saveData']);
    Route::post('stc_delete', [StockCardControllerr::class, 'deleteData']);
    Route::post('stock_report_fill_data', [StockCardController::class, 'fillData']);
    Route::post('stock_report_export', [StockCardController::class, 'exportData']);
    Route::post('stock_report_phase2', [StockCardController::class, 'phase2']);
    Route::post('stock_report_phase3', [StockCardController::class, 'phase3']);

    // ResellerLevelController
    Route::get('reseller_level', [ResellerLevelController::class, 'index']);
    Route::get('rl_datatables', [ResellerLevelController::class, 'getDatatables']);
    Route::post('rl_save', [ResellerLevelController::class, 'storeData']);
    Route::post('rl_delete', [ResellerLevelController::class, 'deleteData']);

    // ResellerAddDiscountController
    Route::get('reseller_additional_discount', [ResellerAddDiscountController::class, 'index']);
    Route::get('rad_datatables', [ResellerAddDiscountController::class, 'getDatatables']);
    Route::post('rad_save', [ResellerAddDiscountController::class, 'storeData']);
    Route::post('rad_delete', [ResellerAddDiscountController::class, 'deleteData']);

    // ResellerConfirmationController
    Route::get('reseller_konfirmasi', [ResellerConfirmationController::class, 'index']);
    Route::get('rc_datatables', [ResellerConfirmationController::class, 'getDatatables']);
    Route::post('rc_save', [ResellerConfirmationController::class, 'saveData']);
    Route::post('rc_delete', [ResellerConfirmationController::class, 'deleteData']);

    // ResellerBrandLevelController
    Route::get('reseller_brand_level', [ResellerBrandLevelController::class, 'index']);
    Route::get('rbl_datatables', [ResellerBrandLevelController::class, 'getDatatables']);
    Route::post('rbl_update', [ResellerBrandLevelController::class, 'updateData']);

    // ResellerTransactionController
    Route::get('reseller_transaction', [ResellerTransactionController::class, 'index']);
    Route::get('rt_datatables', [ResellerTransactionController::class, 'getDatatables']);
    Route::get('invoice_preview_datatables', [ResellerTransactionController::class, 'getInvoiceDatatables']);
    Route::post('rt_min_qty', [ResellerTransactionController::class, 'minQty']);
    Route::post('rt_min_item_qty', [ResellerTransactionController::class, 'minItemQty']);
    Route::post('rt_update_status', [ResellerTransactionController::class, 'updateStatus']);

    // WebinarController
    Route::get('webinar', [WebinarController::class, 'index']);
    Route::get('wbr_datatables', [WebinarController::class, 'getDatatables']);

    // ResellerActivityController
    Route::get('reseller_activity', [ResellerActivityController::class, 'index']);
    Route::get('ra_datatables', [ResellerActivityController::class, 'getDatatables']);
    Route::get('ra_detail_datatables', [ResellerActivityController::class, 'getDetailDatatables']);

    // AssetDetailController
    Route::get('asset_detail', [AssetDetailController::class, 'index']);
    Route::get('ad_size_datatables', [AssetDetailController::class, 'getSizeDatatables']);
    Route::get('ad_color_datatables', [AssetDetailController::class, 'getColorDatatables']);
    Route::get('ad_brand_datatables', [AssetDetailController::class, 'getBrandDatatables']);
    Route::post('ad_load_data', [AssetDetailController::class, 'loadData']);
    Route::get('ad_export', [AssetDetailController::class, 'exportData']);
    Route::post('get_asset_sales_summaries', [AssetDetailController::class, 'getSummary']);

    // StoreAgingController
    Route::get('store_aging', [StoreAgingController::class, 'index']);
    Route::get('sta_datatables', [StoreAgingController::class, 'getDatatables']);
    Route::get('sta_detail_datatables', [StoreAgingController::class, 'getDetailDatatables']);
    Route::get('oca_datatables', [StoreAgingController::class, 'getOCADatatables']);
    Route::post('sta_save', [StoreAgingController::class, 'storeData']);
    Route::post('sta_delete', [StoreAgingController::class, 'deleteData']);
    Route::post('stas_save', [StoreAgingController::class, 'storeDetailData']);
    Route::post('stas_delete', [StoreAgingController::class, 'deleteDetailData']);
    Route::post('sta_checked', [StoreAgingController::class, 'updateChecked']);
    Route::post('oca_save', [StoreAgingController::class, 'storeOCAData']);
    Route::post('oca_delete', [StoreAgingController::class, 'deleteOCAData']);

    // InstockApprovalController
    Route::get('instock_approval', [InstockApprovalController::class, 'index']);
    Route::get('ia_datatables', [InstockApprovalController::class, 'getDatatables']);
    Route::post('ia_save', [InstockApprovalController::class, 'storeData']);
    Route::post('ia_delete', [InstockApprovalController::class, 'deleteData']);

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

    // InstockListController
    Route::get('instock_list', [InstockListController::class, 'index']);
    Route::get('il_datatables', [InstockListController::class, 'getDatatables']);
    Route::get('il_history_datatables', [InstockListController::class, 'getHistoryDatatables']);
    Route::post('il_save', [InstockListController::class, 'storeData']);

    // UserManagementController
    Route::get('user_management', [UserManagementController::class, 'index']);
    Route::get('um_datatables', [UserManagementController::class, 'getDatatables']);
    Route::post('um_save', [UserManagementController::class, 'storeData']);
    Route::post('um_delete', [UserManagementController::class, 'deleteData']);

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
});
