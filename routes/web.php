<?php


use App\Http\Controllers\HelperBackupV1Controller;
use App\Http\Controllers\ProductSubSubCategoryTestController;
use App\Http\Controllers\ProductSubSubCategoryController;
use App\Http\Controllers\PhotoController;

use App\Http\Controllers\TrackingV1Controller;
use App\Http\Controllers\UserShiftController;
use App\Http\Controllers\OvertimeTypeController;
use App\Models\ExternalAssignmentType;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceEditorController;
use App\Http\Controllers\InvoiceEditorOnlineController;
use App\Http\Controllers\PowerBiDashboardController;
use App\Http\Controllers\ApiController;

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
use App\Http\Controllers\ExternalAssignmentTypeController;

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
use App\Http\Controllers\OvertimeRequestController;

use App\Http\Controllers\AssetDetailController;

use App\Http\Controllers\UserMenuAccessController;
use App\Http\Controllers\MainMenuController;
use App\Http\Controllers\MenuAccessController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\SendNotificationController;

// hris
use App\Http\Controllers\ShiftCodeController;
use App\Http\Controllers\DailyScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BreakTimeController;
use App\Http\Controllers\BreakTimeBackupController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\UserPositionController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\UserDivisionController;
use App\Http\Controllers\UserDivisionV2Controller;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AnnouncementCategoryController;
use App\Http\Controllers\AnnouncementReactionController;
use App\Http\Controllers\ExternalAssignmentRequestController;


use App\Http\Controllers\WebConfigController;

use App\Http\Controllers\DataPerusahaanController;
use App\Http\Controllers\LockController;
use App\Models\PositionAccessController;
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
Route::get('login_amel', [AuthController::class, 'index_two'])->name('login_amel');
Route::get('login_v2', [AuthController::class, 'indexV2'])->name('login_v2');
Route::post('user_login', [AuthController::class, 'login']);

// Google OAuth Routes
Route::get('auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('payment_check/88991703/show', [PaymentCheckController::class, 'checkData']);
Route::get('auto/8899/close_data', [ArticleInformationController::class, 'getAutoUpdateArticleInformation']);
Route::get('auto/9999/close_data', [DashboardV2Controller::class, 'closeData']);
Route::post('send_notification_whatsapp', [SendNotificationController::class, 'sendNotification']);

Route::get('print_invoice/{invoice}', [InvoiceController::class, 'printInvoice'])->name('print_invoice');
Route::get('print_offline_invoice/{invoice}', [InvoiceController::class, 'printOfflineInvoice'])->name('print_offline_invoice');
Route::get('e_receipt/{invoice}', [InvoiceController::class, 'eReceiptInvoice'])->name('e_receipt');
Route::post('/upload-photo', [PhotoController::class, 'upload'])->name('upload.photo');

Route::get('daily-schedules/export-weekly-public', [DailyScheduleController::class, 'exportWeeklyPublic'])->name('daily-schedules.export-weekly-public');

Route::get('break-times/allowance', [BreakTimeController::class, 'getBreakAllowance'])->name('break-times.allowance');
Route::get('break-times/current-list', [BreakTimeController::class, 'getCurrentBreakList'])->name('break-times.current-list');
Route::get('break-times/test-filter', [BreakTimeController::class, 'testFilter'])->name('break-times.test-filter');
Route::get('break-times/debug-current-list', [BreakTimeController::class, 'debugCurrentBreakList'])->name('break-times.debug-current-list');
Route::get('break-times/current', [BreakTimeController::class, 'getCurrentBreak'])->name('break-times.current');
Route::post('break-times/start', [BreakTimeController::class, 'startBreak'])->name('break-times.start');
Route::post('break-times/end', [BreakTimeController::class, 'endBreak'])->name('break-times.end');
Route::get('daily-schedules/export-weekly-report-public', [DailyScheduleController::class, 'exportWeeklyReportPublic'])->name('daily-schedules.export-weekly-report-public');
Route::get('daily-schedules/export-weekly-report-pdf', [DailyScheduleController::class, 'exportWeeklyReportPDF'])->name('daily-schedules.export-weekly-report-pdf');
Route::get('daily-schedules/export-weekly-pdf', [DailyScheduleController::class, 'exportWeeklyPDF'])->name('daily-schedules.export-weekly-pdf');


Route::get('break-times-backup/allowance', [BreakTimeBackupController::class, 'getBreakAllowance'])->name('break-times-backup.allowance');
Route::get('break-times-backup/current-list', [BreakTimeBackupController::class, 'getCurrentBreakList'])->name('break-times-backup.current-list');
Route::get('break-times-backup/test-filter', [BreakTimeBackupController::class, 'testFilter'])->name('break-times-backup.test-filter');
Route::get('break-times-backup/debug-current-list', [BreakTimeBackupController::class, 'debugCurrentBreakList'])->name('break-times-backup.debug-current-list');
Route::get('break-times-backup/current', [BreakTimeBackupController::class, 'getCurrentBreak'])->name('break-times-backup.current');
Route::post('break-times-backup/start', [BreakTimeBackupController::class, 'startBreak'])->name('break-times-backup.start');
Route::post('break-times-backup/end', [BreakTimeBackupController::class, 'endBreak'])->name('break-times-backup.end');

// Public PDF export routes (no auth required)
Route::get('daily-schedules/export-weekly-pdf-public', [DailyScheduleController::class, 'exportWeeklyPDFPublic'])->name('daily-schedules.export-weekly-pdf-public');
Route::get('daily-schedules/export-weekly-report-pdf-public', [DailyScheduleController::class, 'exportWeeklyReportPDFPublic'])->name('daily-schedules.export-weekly-report-pdf-public');

// Public Excel export routes (no auth required)
Route::get('break-times/export/excel', [BreakTimeController::class, 'exportToExcel'])->name('break-times.export-excel');
Route::get('break-times-backup/export/excel', [BreakTimeBackupController::class, 'exportToExcel'])->name('break-times-backup.export-excel');

// Export routes (outside auth middleware)
Route::get('attendance/summary-report/export/excel', [AttendanceController::class, 'exportSummaryToExcel'])->name('attendance.summary-report-export-excel');
Route::get('attendance/summary-report/export/pdf', [AttendanceController::class, 'exportSummaryToPDF'])->name('attendance.summary-report-export-pdf');

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
    Route::post('save_out_activity_bin_selected', [TrackingController::class, 'saveOutActivityBinSelected']);
    Route::post('save_in_activity', [TrackingController::class, 'saveInActivity']);
    Route::post('save_in_refund_activity', [TrackingController::class, 'saveInRefundActivity']);
    Route::post('save_tracking_activity', [TrackingController::class, 'saveTrackingActivity']);
    Route::post('cancel_tracking_activity', [TrackingController::class, 'cancelTrackingActivity']);
    Route::post('save_packing_activity', [TrackingController::class, 'savePackingActivity']);
    Route::post('save_reject_activity', [TrackingController::class, 'saveRejectActivity']);
    Route::get('product_in_datatables', [TrackingController::class, 'inDatatables']);
    Route::get('product_out_datatables', [TrackingController::class, 'outDatatables']);
    Route::get('scan_product_out_datatables', [TrackingController::class, 'scanOutDatatables']);
    Route::get('get_bin_by_sa', [TrackingController::class, 'getBinByStorageArea']);
    Route::get('scan_product_in_datatables', [TrackingController::class, 'scanInDatatables']);
    Route::get('scan_product_in_refund_datatables', [TrackingController::class, 'scanInRefundDatatables']);
    Route::get('scan_product_online_datatables', [TrackingController::class, 'scanOnlineDatatables']);
    Route::post('autocomplete_fetch', [ArticleController::class, 'fetch']);
    Route::post('check_article', [ArticleController::class, 'checkArticle']);
    Route::get('export-stock-tracking', [TrackingController::class, 'exportExcel']);

    // Tracking
    Route::get('tracking_v1', [TrackingV1Controller::class, 'index'])->name('tracking_v1');
    Route::get('check_invoice/{invoice}', [InvoiceController::class, 'checkInvoice'])->name('check_invoice');
    Route::get('check_offline_invoice/{invoice}', [InvoiceController::class, 'checkOfflineInvoice'])->name('check_offline_invoice');
    Route::get('check_sync', [InvoiceController::class, 'checkSync']);
    Route::post('check_barcode_tracking_v1', [TrackingV1Controller::class, 'checkBarcodeTracking']);
    Route::post('check_secret_code_v1', [TrackingV1Controller::class, 'checkSecretCode']);
    Route::post('reload_bin_by_barcode_v1', [TrackingV1Controller::class, 'reloadBinByBarcode']);
    Route::post('order_list_by_invoice_v1', [PointOfSaleController::class, 'orderListByInvoice']);
    Route::post('packing_list_by_invoice_v1', [PointOfSaleController::class, 'packingListByInvoice']);
    Route::post('save_out_activity_v1', [TrackingV1Controller::class, 'saveOutActivity']);
    Route::post('save_in_activity_v1', [TrackingV1Controller::class, 'saveInActivity']);
    Route::post('save_in_refund_activity_v1', [TrackingV1Controller::class, 'saveInRefundActivity']);
    Route::post('save_tracking_activity_v1', [TrackingV1Controller::class, 'saveTrackingActivity']);
    Route::post('cancel_tracking_activity_v1', [TrackingV1Controller::class, 'cancelTrackingActivity']);
    Route::post('save_packing_activity_v1', [TrackingV1Controller::class, 'savePackingActivity']);
    Route::post('save_reject_activity_v1', [TrackingV1Controller::class, 'saveRejectActivity']);
    Route::get('product_in_datatables_v1', [TrackingV1Controller::class, 'inDatatables']);
    Route::get('product_out_datatables_v1', [TrackingV1Controller::class, 'outDatatables']);
    Route::get('scan_product_out_datatables_v1', [TrackingV1Controller::class, 'scanOutDatatables']);
    Route::get('scan_product_in_datatables_v1', [TrackingV1Controller::class, 'scanInDatatables']);
    Route::get('scan_product_in_refund_datatables_v1', [TrackingV1Controller::class, 'scanInRefundDatatables']);
    Route::get('scan_product_online_datatables_v1', [TrackingV1Controller::class, 'scanOnlineDatatables']);
    Route::post('autocomplete_fetch', [ArticleController::class, 'fetch']);
    Route::post('check_article', [ArticleController::class, 'checkArticle']);
    Route::get('export-stock-tracking_v1', [TrackingV1Controller::class, 'exportExcel']);

    // POS
    Route::get('point_of_sale', [PointOfSaleController::class, 'index'])->name('point_of_sale');
    Route::get('point_of_sale_v2', [PointOfSaleController::class, 'indexV2'])->name('point_of_sale_v2');
    Route::post('search_product_v2', [PointOfSaleController::class, 'searchProductV2']);
    Route::get('/current-shift-data', [PointOfSaleController::class, 'getCurrentShiftData'])->name('current-shift.data');
    Route::get('reload_refund', [PointOfSaleController::class, 'reloadRefund']);
    Route::get('reload_refund_offline', [PointOfSaleController::class, 'reloadRefundOffline']);
    Route::get('reload_dp_offline', [PointOfSaleController::class, 'reloadDpOffline']);
    Route::get('refund_retur_datatables', [PointOfSaleController::class, 'refundReturDatatables']);
    Route::get('dp_invoice_datatables', [PointOfSaleController::class, 'dpInvoiceDatatables']);
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
//    Route::post('autocomplete_amp', [PointOfSaleController::class, 'fetchAmp']);
    Route::post('autocomplete_by_waiting', [PointOfSaleController::class, 'fetchWaiting']);
    Route::post('autocomplete_invoice', [PointOfSaleController::class, 'fetchInvoice']);
    Route::post('reload_location_by_pst_id', [PointOfSaleController::class, 'reloadLocationByPstId']);
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
//    Route::post('ie_permission_check_active_edit', [InvoiceEditorController::class, 'checkActiveEdit']);
    Route::post('ie_permission_done_edit', [InvoiceEditorController::class, 'doneEdit']);
    Route::post('ie_permission_do_edit', [InvoiceEditorController::class, 'doEdit']);
    Route::post('ie_permission_cancel_item', [InvoiceEditorController::class, 'cancelItem']);
    Route::post('ie_permission_cancel_invoice', [InvoiceEditorController::class, 'cancelInvoice']);

    // InvoiceEditorController
    Route::get('invoice_editor_online', [InvoiceEditorOnlineController::class, 'index']);
    Route::get('ie_online_permission_datatables', [InvoiceEditorOnlineController::class, 'getPermissionDatatables']);
    Route::get('ie_online_permission_invoice_datatables', [InvoiceEditorOnlineController::class, 'getInvoiceDatatables']);
    Route::get('ie_online_permission_detail_datatables', [InvoiceEditorOnlineController::class, 'getDetailDatatables']);
    Route::get('ie_online_permission_tracking_datatables', [InvoiceEditorOnlineController::class, 'getTrackingDatatables']);
    Route::get('ie_online_permission_history_datatables', [InvoiceEditorOnlineController::class, 'getHistoryDatatables']);
    Route::post('ie_online_permission_save', [InvoiceEditorOnlineController::class, 'storePermissionData']);
    Route::post('ie_online_permission_delete', [InvoiceEditorOnlineController::class, 'deletePermissionData']);
    Route::post('ie_online_permission_invoice', [InvoiceEditorOnlineController::class, 'checkInvoice']);
    Route::post('ie_online_permission_check_active_edit', [InvoiceEditorOnlineController::class, 'checkActiveEdit']);
    Route::post('ie_online_permission_done_edit', [InvoiceEditorOnlineController::class, 'doneEdit']);
    Route::post('ie_online_permission_do_edit', [InvoiceEditorOnlineController::class, 'doEdit']);
    Route::post('ie_online_permission_edit_sku', [InvoiceEditorOnlineController::class, 'doEditSku']);
    Route::post('ie_online_permission_cancel_item', [InvoiceEditorOnlineController::class, 'cancelItem']);
    Route::post('ie_online_permission_cancel_invoice', [InvoiceEditorOnlineController::class, 'cancelInvoice']);

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

    // Helper Backup //
    Route::get('helper_backup', [HelperBackupController::class, 'index']);
    Route::get('helper_backup_v1', [HelperBackupV1Controller::class, 'index']);

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
    //Product
    // Route::post('stock_data_search_product', [ProductController::class, 'searchProduct']);

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
    Route::post('pc_dele te', [ProductCategoryController::class, 'deleteData']);
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
    Route::get('sub_sub_kategori_produk', [ProductSubSubCategoryController::class, 'index'])->name('product_sub_sub_category');
    Route::get('product_sub_sub_category_datatables', [ProductSubSubCategoryController::class, 'getDatatables']);
    Route::get('reload_product_sub_category', [ProductSubCategoryController::class, 'reloadProductSubCategory']);
    Route::get('reload_product_sub_sub_category', [ProductSubSubCategoryController::class, 'reloadProductSubSubCategory']);
    Route::get('pssc_reload', [ProductSubSubCategoryController::class, 'reloadPssc']);
    Route::post('pssc_save', [ProductSubSubCategoryController::class, 'storeData']);
    Route::post('pssc_delete', [ProductSubSubCategoryController::class, 'deleteData']);
    Route::post('pssc_import', [ProductSubSubCategoryController::class, 'importData']);


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
    Route::get('customer_details', [InvoiceReportController::class, 'detail']);

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

    // Power BI Dashboard
    Route::get('power_bi_dashboard', [PowerBiDashboardController::class, 'index']);

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

    //Data Perusahaan
    Route::get('data_perusahaan', [DataPerusahaanController::class, 'index'])->name('data_perusahaan');
    Route::get('data_perusahaan_datatables', [DataPerusahaanController::class, 'getDatatables']);
    Route::post('dp_save', [DataPerusahaanController::class, 'storeData']);
    Route::post('dp_delete', [DataPerusahaanController::class, 'deleteData']);
    Route::post('dp_import', [DataPerusahaanController::class, 'importData']);
    Route::post('check_exists_data_perusahaan', [DataPerusahaanController::class, 'checkExistsDataPerusahaan']);
    Route::get('export-perusahaan', [DataPerusahaanController::class, 'exportData']);

    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'fetchNotifications']);
    Route::post('/notifications/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/{id}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markSingleAsRead']);

    // HR Management Routes
    // ShiftCodeController
    Route::get('shift-codes', [ShiftCodeController::class, 'index'])->name('shift-codes.index');
    Route::get('shift-codes/datatables', [ShiftCodeController::class, 'getDatatables'])->name('shift-codes.datatables');
    Route::get('shift-codes/create', [ShiftCodeController::class, 'create'])->name('shift-codes.create');
    Route::post('shift-codes', [ShiftCodeController::class, 'store'])->name('shift-codes.store');
    Route::get('shift-codes/{id}', [ShiftCodeController::class, 'show'])->name('shift-codes.show');
    Route::get('shift-codes/{id}/edit', [ShiftCodeController::class, 'edit'])->name('shift-codes.edit');
    Route::put('shift-codes/{id}', [ShiftCodeController::class, 'update'])->name('shift-codes.update');
    Route::delete('shift-codes/{id}', [ShiftCodeController::class, 'destroy'])->name('shift-codes.destroy');
    Route::post('shift-codes/{id}/toggle-status', [ShiftCodeController::class, 'toggleStatus'])->name('shift-codes.toggle-status');
    Route::get('shift-codes/type/{type}', [ShiftCodeController::class, 'getShiftCodesByType'])->name('shift-codes.by-type');

    // DailyScheduleController
    Route::get('daily-schedules', [DailyScheduleController::class, 'index'])->name('daily-schedules.index');
    Route::get('daily-schedules/datatables', [DailyScheduleController::class, 'getDatatables'])->name('daily-schedules.datatables');
    Route::get('daily-schedules/statistics', [DailyScheduleController::class, 'getStatistics'])->name('daily-schedules.statistics');
    Route::get('daily-schedules/create', [DailyScheduleController::class, 'create'])->name('daily-schedules.create');
    Route::post('daily-schedules', [DailyScheduleController::class, 'store'])->name('daily-schedules.store');
    Route::get('daily-schedules/bulk/create', [DailyScheduleController::class, 'bulkCreate'])->name('daily-schedules.bulk-create');
    Route::post('daily-schedules/bulk', [DailyScheduleController::class, 'bulkStore'])->name('daily-schedules.bulk-store');
    Route::get('daily-schedules/create-range', [DailyScheduleController::class, 'createRange'])->name('daily-schedules.create-range');
    Route::post('daily-schedules/store-range', [DailyScheduleController::class, 'storeRange'])->name('daily-schedules.store-range');
    Route::get('daily-schedules/export', [DailyScheduleController::class, 'export'])->name('daily-schedules.export');
    Route::get('daily-schedules/weekly', [DailyScheduleController::class, 'weeklySchedule'])->name('daily-schedules.weekly');
    Route::get('daily-schedules/weekly-report', [DailyScheduleController::class, 'weeklyReport'])->name('daily-schedules.weekly-report');
    Route::get('daily-schedules/monthly-report', [DailyScheduleController::class, 'monthlyReport'])->name('daily-schedules.monthly-report');
    Route::get('daily-schedules/export-monthly-excel', [DailyScheduleController::class, 'exportMonthlyExcel'])->name('daily-schedules.export-monthly-excel');
    Route::get('daily-schedules/export-monthly-pdf', [DailyScheduleController::class, 'exportMonthlyPDF'])->name('daily-schedules.export-monthly-pdf');
    Route::get('daily-schedules/get-users-by-division', [DailyScheduleController::class, 'getUsersByDivision'])->name('daily-schedule.get-users-by-division');
    Route::get('daily-schedules/get-weekly-schedules', [DailyScheduleController::class, 'getWeeklySchedules'])->name('daily-schedule.get-weekly-schedules');
    Route::post('daily-schedules/save-weekly-schedule', [DailyScheduleController::class, 'saveWeeklySchedule'])->name('daily-schedule.save-weekly-schedule');

    Route::get('daily-schedules/{id}', [DailyScheduleController::class, 'show'])->name('daily-schedules.show');
    Route::get('daily-schedules/{id}/edit', [DailyScheduleController::class, 'edit'])->name('daily-schedules.edit');
    Route::put('daily-schedules/{id}', [DailyScheduleController::class, 'update'])->name('daily-schedules.update');
    Route::delete('daily-schedules/{id}', [DailyScheduleController::class, 'destroy'])->name('daily-schedules.destroy');
    Route::post('daily-schedules/{id}/status', [DailyScheduleController::class, 'updateStatus'])->name('daily-schedules.update-status');


    // AttendanceController
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/datatables', [AttendanceController::class, 'getDatatables'])->name('attendance.datatables');
    Route::get('attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('attendance/upload', [AttendanceController::class, 'upload'])->name('attendance.upload');
    Route::post('attendance/upload', [AttendanceController::class, 'processUpload'])->name('attendance.process-upload');

    Route::get('attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::get('attendance/summary-report', [AttendanceController::class, 'summaryReport'])->name('attendance.summary-report');
    Route::get('attendance/summary-report/datatables', [AttendanceController::class, 'getSummaryReportDatatables'])->name('attendance.summary-report-datatables');

    Route::get('attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('attendance/{id}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('attendance/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('attendance/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

    Route::post('attendance/reprocess-all', [AttendanceController::class, 'reprocessAll'])->name('attendance.reprocess-all');
    Route::get('attendance/debug-status', [AttendanceController::class, 'debugAttendanceStatus'])->name('attendance.debug-status');
    Route::post('attendance/reprocess-status', [AttendanceController::class, 'reprocessAttendanceStatus'])->name('attendance.reprocess-status');
    Route::post('attendance/reprocess-single', [AttendanceController::class, 'reprocessSingleAttendance'])->name('attendance.reprocess-single');
    Route::get('attendance/staff/{user_id}', [AttendanceController::class, 'staffDetail'])->name('attendance.staff-detail');
    Route::get('attendance/staff/{user_id}/datatables', [AttendanceController::class, 'staffDatatables'])->name('attendance.staff-datatables');
    Route::get('attendance/staff/{user_id}/stats', [AttendanceController::class, 'staffStats'])->name('attendance.staff-stats');
    Route::get('attendance/staff/{user_id}/alpha-dates', [AttendanceController::class, 'getStaffAlphaDates'])->name('attendance.staff-alpha-dates');

    // Export routes
    Route::get('attendance/export/excel', [AttendanceController::class, 'exportToExcel'])->name('attendance.export-excel');
    Route::get('attendance/export/pdf', [AttendanceController::class, 'exportToPDF'])->name('attendance.export-pdf');
    Route::get('attendance/staff/{user_id}/export/excel', [AttendanceController::class, 'exportStaffToExcel'])->name('attendance.staff-export-excel');
    Route::get('attendance/staff/{user_id}/export/pdf', [AttendanceController::class, 'exportStaffToPDF'])->name('attendance.staff-export-pdf');

    // BreakTimeController
    Route::get('break-times', [BreakTimeController::class, 'index'])->name('break-times.index');
    Route::get('break-times/report', [BreakTimeController::class, 'report'])->name('break-times.report');
    Route::get('break-times/summary-report', [BreakTimeController::class, 'summaryReport'])->name('break-times.summary-report');
    Route::get('break-times/summary-report/datatables', [BreakTimeController::class, 'getSummaryReportDatatables'])->name('break-times.summary-report-datatables');
    Route::get('break-times/summary-report/stats', [BreakTimeController::class, 'getSummaryReportStats'])->name('break-times.summary-report-stats');
    Route::get('break-times/datatables', [BreakTimeController::class, 'getDatatables'])->name('break-times.datatables');
    Route::get('break-times/export/pdf', [BreakTimeController::class, 'exportToPDF'])->name('break-times.export-pdf');
    Route::get('break-times/summary-report/export/excel', [BreakTimeController::class, 'exportSummaryToExcel'])->name('break-times.summary-report-export-excel');
    Route::get('break-times/summary-report/export/pdf', [BreakTimeController::class, 'exportSummaryToPDF'])->name('break-times.summary-report-export-pdf');
    Route::get('break-times/staff/{user_id}', [BreakTimeController::class, 'staffDetail'])->name('break-times.staff-detail');
    Route::get('break-times/staff/{user_id}/datatables', [BreakTimeController::class, 'staffDatatables'])->name('break-times.staff-datatables');
    Route::get('break-times/staff/{user_id}/stats', [BreakTimeController::class, 'staffStats'])->name('break-times.staff-stats');
    Route::get('break-times/stats', [BreakTimeController::class, 'getBreakTimeStats'])->name('break-times.stats');
    Route::get('break-times/staff/{user_id}/export/excel', [BreakTimeController::class, 'exportStaffToExcel'])->name('break-times.staff-export-excel');
    Route::get('break-times/staff/{user_id}/export/pdf', [BreakTimeController::class, 'exportStaffToPDF'])->name('break-times.staff-export-pdf');
    Route::get('break-times/create', [BreakTimeController::class, 'create'])->name('break-times.create');
    Route::post('break-times', [BreakTimeController::class, 'store'])->name('break-times.store');
    Route::get('break-times/{id}', [BreakTimeController::class, 'show'])->name('break-times.show');
    Route::get('break-times/{id}/edit', [BreakTimeController::class, 'edit'])->name('break-times.edit');
    Route::put('break-times/{id}', [BreakTimeController::class, 'update'])->name('break-times.update');
    Route::delete('break-times/{id}', [BreakTimeController::class, 'destroy'])->name('break-times.destroy');
    Route::post('break-times/clock-in', [BreakTimeController::class, 'clockIn'])->name('break-times.clock-in');
    Route::post('break-times/clock-out', [BreakTimeController::class, 'clockOut'])->name('break-times.clock-out');
    Route::post('break-times/{id}/cancel', [BreakTimeController::class, 'cancelBreakTime'])->name('break-times.cancel');
    Route::post('break-times/cleanup', [BreakTimeController::class, 'cleanupInvalidBreaks'])->name('break-times.cleanup');

    // BreakTimeBackupController
    Route::get('break-times-backup', [BreakTimeBackupController::class, 'index'])->name('break-times-backup.index');
    Route::get('break-times-backup/report', [BreakTimeBackupController::class, 'report'])->name('break-times-backup.report');
    Route::get('break-times-backup/summary-report', [BreakTimeBackupController::class, 'summaryReport'])->name('break-times-backup.summary-report');
    Route::get('break-times-backup/summary-report/datatables', [BreakTimeBackupController::class, 'getSummaryReportDatatables'])->name('break-times-backup.summary-report-datatables');
    Route::get('break-times-backup/summary-report/stats', [BreakTimeBackupController::class, 'getSummaryReportStats'])->name('break-times-backup.summary-report-stats');
    Route::get('break-times-backup/datatables', [BreakTimeBackupController::class, 'getDatatables'])->name('break-times-backup.datatables');
    Route::get('break-times-backup/export/pdf', [BreakTimeBackupController::class, 'exportToPDF'])->name('break-times-backup.export-pdf');
    Route::get('break-times-backup/summary-report/export/excel', [BreakTimeBackupController::class, 'exportSummaryToExcel'])->name('break-times-backup.summary-report-export-excel');
    Route::get('break-times-backup/summary-report/export/pdf', [BreakTimeBackupController::class, 'exportSummaryToPDF'])->name('break-times-backup.summary-report-export-pdf');
    Route::get('break-times-backup/staff/{user_id}', [BreakTimeBackupController::class, 'staffDetail'])->name('break-times-backup.staff-detail');
    Route::get('break-times-backup/staff/{user_id}/datatables', [BreakTimeBackupController::class, 'staffDatatables'])->name('break-times-backup.staff-datatables');
    Route::get('break-times-backup/staff/{user_id}/stats', [BreakTimeBackupController::class, 'staffStats'])->name('break-times-backup.staff-stats');
    Route::get('break-times-backup/stats', [BreakTimeBackupController::class, 'getBreakTimeStats'])->name('break-times-backup.stats');
    Route::get('break-times-backup/staff/{user_id}/export/excel', [BreakTimeBackupController::class, 'exportStaffToExcel'])->name('break-times-backup.staff-export-excel');
    Route::get('break-times-backup/staff/{user_id}/export/pdf', [BreakTimeBackupController::class, 'exportStaffToPDF'])->name('break-times-backup.staff-export-pdf');
    Route::get('break-times-backup/create', [BreakTimeBackupController::class, 'create'])->name('break-times-backup.create');
    Route::post('break-times-backup', [BreakTimeBackupController::class, 'store'])->name('break-times-backup.store');
    Route::get('break-times-backup/{id}', [BreakTimeBackupController::class, 'show'])->name('break-times-backup.show');
    Route::get('break-times-backup/{id}/edit', [BreakTimeBackupController::class, 'edit'])->name('break-times-backup.edit');
    Route::put('break-times-backup/{id}', [BreakTimeBackupController::class, 'update'])->name('break-times-backup.update');
    Route::delete('break-times-backup/{id}', [BreakTimeBackupController::class, 'destroy'])->name('break-times-backup.destroy');
    Route::post('break-times-backup/clock-in', [BreakTimeBackupController::class, 'clockIn'])->name('break-times-backup.clock-in');
    Route::post('break-times-backup/clock-out', [BreakTimeBackupController::class, 'clockOut'])->name('break-times-backup.clock-out');
    Route::post('break-times-backup/cleanup', [BreakTimeBackupController::class, 'cleanupInvalidBreaks'])->name('break-times-backup.cleanup');


// LeaveTypeController
    Route::get('leave-types', [LeaveTypeController::class, 'index'])->name('leave-types.index');
    Route::get('leave-types/datatables', [LeaveTypeController::class, 'getDatatables'])->name('leave-types.datatables');
    Route::get('leave-types/create', [LeaveTypeController::class, 'create'])->name('leave-types.create');
    Route::post('leave-types', [LeaveTypeController::class, 'store'])->name('leave-types.store');
    Route::get('leave-types/{id}', [LeaveTypeController::class, 'show'])->name('leave-types.show');
    Route::get('leave-types/{id}/edit', [LeaveTypeController::class, 'edit'])->name('leave-types.edit');
    Route::put('leave-types/{id}', [LeaveTypeController::class, 'update'])->name('leave-types.update');
    Route::delete('leave-types/{id}', [LeaveTypeController::class, 'destroy'])->name('leave-types.destroy');
    Route::get('leave-types/{id}/toggle-status', [LeaveTypeController::class, 'toggleStatus'])->name('leave-types.toggle-status');
    Route::get('leave-types/type/{type}', [LeaveTypeController::class, 'getLeaveTypesByType'])->name('leave-types.by-type');

    // LeaveRequestController
    // Leave Summary Report Routes (MUST be before {id} routes to avoid conflicts)
    Route::get('leave-requests/summary-report', [LeaveRequestController::class, 'summaryReport'])->name('leave-requests.summary-report');
    Route::get('leave-requests/summary-report/datatables', [LeaveRequestController::class, 'getSummaryReportDatatables'])->name('leave-requests.summary-report-datatables');
    Route::get('leave-requests/summary-report/export/excel', [LeaveRequestController::class, 'exportSummaryToExcel'])->name('leave-requests.summary-report-export-excel');
    Route::get('leave-requests/summary-report/export/pdf', [LeaveRequestController::class, 'exportSummaryToPDF'])->name('leave-requests.summary-report-export-pdf');
    Route::get('leave-requests/staff/{user_id}', [LeaveRequestController::class, 'staffDetail'])->name('leave-requests.staff-detail');
    Route::get('leave-requests/staff/{user_id}/datatables', [LeaveRequestController::class, 'staffDatatables'])->name('leave-requests.staff-datatables');
    Route::get('leave-requests/staff/{user_id}/stats', [LeaveRequestController::class, 'staffStats'])->name('leave-requests.staff-stats');
    Route::get('leave-requests/staff/{user_id}/export/excel', [LeaveRequestController::class, 'exportStaffToExcel'])->name('leave-requests.staff-export-excel');
    Route::get('leave-requests/staff/{user_id}/export/pdf', [LeaveRequestController::class, 'exportStaffToPDF'])->name('leave-requests.staff-export-pdf');

    // Leave Balance Route
    Route::get('leave-requests/balance/{leaveTypeId}', [LeaveRequestController::class, 'getLeaveBalance'])->name('leave-requests.balance');

    // Leave Request Specific Routes (MUST be before {id} routes to avoid conflicts)
    Route::get('leave-requests/datatables', [LeaveRequestController::class, 'getDatatables'])->name('leave-requests.datatables');
    Route::get('leave-requests/stats', [LeaveRequestController::class, 'getStats'])->name('leave-requests.stats');
    Route::get('leave-requests/create', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('leave-requests/reprocess-all', [LeaveRequestController::class, 'reprocessAll'])->name('leave-requests.reprocess-all');

    // Leave Request CRUD Routes (with {id} parameter)
    Route::get('leave-requests/{id}', [LeaveRequestController::class, 'show'])->name('leave-requests.show');
    Route::get('leave-requests/{id}/edit', [LeaveRequestController::class, 'edit'])->name('leave-requests.edit');
    Route::put('leave-requests/{id}', [LeaveRequestController::class, 'update'])->name('leave-requests.update');
    Route::delete('leave-requests/{id}', [LeaveRequestController::class, 'destroy'])->name('leave-requests.destroy');
    Route::get('leave-requests/{id}/process-status', [LeaveRequestController::class, 'processStatus'])->name('leave-requests.process-status');
    Route::post('leave-requests/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{id}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');

    // Leave Request Index and Store Routes (MUST be AFTER {id} routes to avoid conflicts)
    Route::get('leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::post('leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');

// Debug route for testing CSRF
    Route::get('test-csrf', function () {
        return response()->json([
            'csrf_token' => csrf_token(),
            'session_id' => session()->getId(),
            'user_id' => auth()->id()
        ]);
    })->middleware('auth');

    // UserPositionController
    Route::get('user-positions', [UserPositionController::class, 'index'])->name('user-positions.index');
    Route::get('user-positions/datatables', [UserPositionController::class, 'getDatatables'])->name('user-positions.datatables');
    Route::get('user-positions/create', [UserPositionController::class, 'create'])->name('user-positions.create');
    Route::post('user-positions', [UserPositionController::class, 'store'])->name('user-positions.store');
    Route::get('user-positions/{id}', [UserPositionController::class, 'show'])->name('user-positions.show');
    Route::get('user-positions/{id}/edit', [UserPositionController::class, 'edit'])->name('user-positions.edit');
    Route::put('user-positions/{id}', [UserPositionController::class, 'update'])->name('user-positions.update');
    Route::delete('user-positions/{id}', [UserPositionController::class, 'destroy'])->name('user-positions.destroy');

    // UserDivisionController
    Route::get('user-divisions', [UserDivisionController::class, 'index'])->name('user-divisions.index');
    Route::get('user-divisions/datatables', [UserDivisionController::class, 'getDatatables'])->name('user-divisions.datatables');
    Route::get('user-divisions/create', [UserDivisionController::class, 'create'])->name('user-divisions.create');
    Route::post('user-divisions', [UserDivisionController::class, 'store'])->name('user-divisions.store');
    Route::get('user-divisions/{id}', [UserDivisionController::class, 'show'])->name('user-divisions.show');
    Route::get('user-divisions/{id}/edit', [UserDivisionController::class, 'edit'])->name('user-divisions.edit');
    Route::put('user-divisions/{id}', [UserDivisionController::class, 'update'])->name('user-divisions.update');
    Route::delete('user-divisions/{id}', [UserDivisionController::class, 'destroy'])->name('user-divisions.destroy');

    // UserDivisionController
    Route::get('user-divisions-v2', [UserDivisionV2Controller::class, 'index'])->name('user-divisions-v2.index');
    Route::get('user-divisions-v2/datatables', [UserDivisionV2Controller::class, 'getDatatables'])->name('user-divisions-v2.datatables');

    // UserTypeController
    Route::get('user-types', [UserTypeController::class, 'index'])->name('user-types.index');
    Route::get('user-types/datatables', [UserTypeController::class, 'getDatatables'])->name('user-types.datatables');
    Route::get('user-types/{id}', [UserTypeController::class, 'show'])->name('user-types.show');
    Route::post('user-types', [UserTypeController::class, 'store'])->name('user-types.store');
    Route::put('user-types/{id}', [UserTypeController::class, 'update'])->name('user-types.update');
    Route::delete('user-types/{id}', [UserTypeController::class, 'destroy'])->name('user-types.destroy');

    // StaffController
    Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('staff/datatables', [StaffController::class, 'getDatatables'])->name('staff.datatables');
    Route::post('staff/{id}/position', [StaffController::class, 'updatePosition'])->name('staff.update-position');
    Route::post('staff/{id}/division', [StaffController::class, 'updateDivision'])->name('staff.update-division');
    Route::post('staff/{id}/user-type', [StaffController::class, 'updateUserType'])->name('staff.update-user-type');
    Route::post('staff/{id}/leave-balance', [StaffController::class, 'updateLeaveBalance'])->name('staff.update-leave-balance');

    // User Select for AJAX
    Route::get('users/select', [UserController::class, 'select'])->name('users.select');

    // Announcement System
    Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('announcements/manage', [AnnouncementController::class, 'manage'])->name('announcements.manage');
    Route::get('announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::get('announcements/{id}', [AnnouncementController::class, 'show'])->name('announcements.show');
    Route::post('announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('announcements/{id}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('announcements/{id}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::post('announcements/react', [AnnouncementController::class, 'react'])->name('announcements.react');
    Route::post('announcements/{id}/pin', [AnnouncementController::class, 'togglePin'])->name('announcements.toggle-pin');
    Route::get('announcements/{id}/reactions', [AnnouncementController::class, 'getReactionDetails'])->name('announcements.reaction-details');
    Route::post('announcements/{id}/view', [AnnouncementController::class, 'trackView'])->name('announcements.track-view');
    Route::get('announcements/{id}/viewers', [AnnouncementController::class, 'getViewers'])->name('announcements.viewers');
    Route::delete('announcements/attachment/{id}/remove', [AnnouncementController::class, 'removeAttachment'])->name('announcements.remove-attachment');


    // Announcement Categories
    Route::get('announcement-categories', [AnnouncementCategoryController::class, 'index'])->name('announcement-categories.index');
    Route::post('announcement-categories', [AnnouncementCategoryController::class, 'store'])->name('announcement-categories.store');
    Route::put('announcement-categories/{id}', [AnnouncementCategoryController::class, 'update'])->name('announcement-categories.update');
    Route::delete('announcement-categories/{id}', [AnnouncementCategoryController::class, 'destroy'])->name('announcement-categories.destroy');

    // Announcement Reactions
    Route::get('announcement-reactions', [AnnouncementReactionController::class, 'index'])->name('announcement-reactions.index');
    Route::post('announcement-reactions', [AnnouncementReactionController::class, 'store'])->name('announcement-reactions.store');
    Route::put('announcement-reactions/{id}', [AnnouncementReactionController::class, 'update'])->name('announcement-reactions.update');
    Route::delete('announcement-reactions/{id}', [AnnouncementReactionController::class, 'destroy'])->name('announcement-reactions.destroy');

    Route::get('daily-schedules/get-weekly-schedules', [DailyScheduleController::class, 'getWeeklySchedules'])->name('daily-schedule.get-weekly-schedules');
    Route::get('daily-schedules/test-data', [DailyScheduleController::class, 'testData'])->name('daily-schedule.test-data');

    // Export routes for weekly schedule and report
    Route::get('daily-schedules/export-weekly', [DailyScheduleController::class, 'exportWeekly'])->name('daily-schedules.export-weekly');
    Route::post('daily-schedules/import-weekly-excel', [DailyScheduleController::class, 'importWeeklyExcel'])->name('daily-schedules.import-weekly-excel');
    Route::get('daily-schedules/export-weekly-report', [DailyScheduleController::class, 'exportWeeklyReport'])->name('daily-schedules.export-weekly-report');

    // Test export route for debugging

    // Grup rute untuk locking, hanya bisa diakses oleh user yang sudah login
    Route::prefix('lock')->controller(LockController::class)->group(function () {
        Route::post('/acquire', 'acquireLock')->name('lock.acquire');
        Route::put('/extend', 'extendLock')->name('lock.extend'); // Untuk heartbeat
        Route::delete('/release', 'releaseLock')->name('lock.release');
    });

    Route::get('position-access', [PositionAccessController::class, 'index']);
    Route::get('position-access-datatables', [PositionAccessController::class, 'getDatatables']);
    Route::post('position-access-save', [PositionAccessController::class, 'storeData']);
    Route::post('position-access-delete/{route}/{position_id}', [PositionAccessController::class, 'deleteData']);
    Route::get('reload_position', [PositionAccessController::class, 'reloadPosition']);
    Route::post('change_access', [PositionAccessController::class, 'changeAccess']);

    //External Assignment Type
    Route::get('external_assignment_type', [ExternalAssignmentTypeController::class, 'index'])->name('external_assignment_type');
    Route::get('external_assignment_type_datatables', [ExternalAssignmentTypeController::class, 'getDatatables']);
    Route::post('ea_save', [ExternalAssignmentTypeController::class, 'storeData']);
    Route::post('ea_delete', [ExternalAssignmentTypeController::class, 'deleteData']);
    Route::post('ea_import', [ExternalAssignmentTypeController::class, 'importData']);
    Route::post('check_exists_external_assignment_type', [ExternalAssignmentTypeController::class, 'checkExistsExternalTypes']);
    Route::get('export-perusahaan', [ExternalAssignmentTypeController::class, 'exportData']);


    //External Assignment Request
    Route::get('external-assignment', [ExternalAssignmentRequestController::class, 'index'])->name('external-assignments.index');
    Route::get('external-assignment/summary-report', [ExternalAssignmentRequestController::class, 'summaryReport'])->name('external-assignment.summary-report');
    Route::get('external-assignment/summary-report/datatables', [ExternalAssignmentRequestController::class, 'getExternalAssignmentSummaryDatatables'])->name('external-assignment.summary-report-datatables');
    Route::get('external-assignment/summary-report/export/excel', [ExternalAssignmentRequestController::class, 'exportSummaryToExcel'])->name('external-assignment.summary-report-export-excel');
    Route::get('external-assignment/summary-report/export/pdf', [ExternalAssignmentRequestController::class, 'exportSummaryToPDF'])->name('external-assignment.summary-report-export-pdf');
    Route::get('external-assignment/staff/{user_id}', [ExternalAssignmentRequestController::class, 'staffDetail'])->name('external-assignment.staff-detail');
    Route::get('external-assignment/staff/{user_id}/datatables', [ExternalAssignmentRequestController::class, 'staffDatatables'])->name('external-assignment.staff-datatables');
    Route::get('external-assignment/staff/{user_id}/stats', [ExternalAssignmentRequestController::class, 'staffStats'])->name('external-assignment.staff-stats');
    Route::get('external-assignment/staff/{user_id}/export/excel', [ExternalAssignmentRequestController::class, 'exportStaffToExcel'])->name('external-assignment.staff-export-excel');
    Route::get('external-assignment/staff/{user_id}/export/pdf', [ExternalAssignmentRequestController::class, 'exportStaffToPDF'])->name('external-assignment.staff-export-pdf');

    // Leave Balance Route
    Route::get('external-assignment/balance/{leaveTypeId}', [ExternalAssignmentRequestController::class, 'getLeaveBalance'])->name('external-assignment.balance');

    // Leave Request Specific Routes (MUST be before {id} routes to avoid conflicts)
    Route::get('external-assignment/datatables', [ExternalAssignmentRequestController::class, 'getDatatables'])->name('external-assignment.datatables');
    Route::get('external-assignment/stats', [ExternalAssignmentRequestController::class, 'getStats'])->name('external-assignment.stats');
    Route::get('external-assignment/create', [ExternalAssignmentRequestController::class, 'create'])->name('external-assignment.create');
    Route::post('external-assignment/reprocess-all', [ExternalAssignmentRequestController::class, 'reprocessAll'])->name('external-assignment.reprocess-all');

    // Leave Request CRUD Routes (with {id} parameter)
    Route::get('external-assignment/{id}', [ExternalAssignmentRequestController::class, 'show'])->name('external-assignment.show');
    Route::get('external-assignment/{id}/edit', [ExternalAssignmentRequestController::class, 'edit'])->name('external-assignment.edit');
    Route::put('external-assignment/{id}', [ExternalAssignmentRequestController::class, 'update'])->name('external-assignment.update');
    Route::delete('external-assignment/{id}', [ExternalAssignmentRequestController::class, 'destroy'])->name('external-assignment.destroy');
    Route::get('external-assignment/{id}/process-status', [ExternalAssignmentRequestController::class, 'processStatus'])->name('external-assignment.process-status');
    Route::post('external-assignment/{id}/approve', [ExternalAssignmentRequestController::class, 'approve'])->name('external-assignment.approve');
    Route::post('external-assignment/{id}/reject', [ExternalAssignmentRequestController::class, 'reject'])->name('external-assignment.reject');

    // Leave Request Index and Store Routes (MUST be AFTER {id} routes to avoid conflicts)
//    Route::get('external-assignment', [ExternalAssignmentRequestController::class, 'index'])->name('external-assignment.index');
//    Route::post('external-assignment', [ExternalAssignmentRequestController::class, 'store'])->name('external-assignment.store');
    Route::post('/external-assignment-requests/create', [ExternalAssignmentRequestController::class, 'store'])->name('external-assignment-requests.store');
    Route::get('/external-assignment-requests/{id}', [ExternalAssignmentRequestController::class, 'show'])
        ->name('external-assignment-requests.show');
    Route::post('/ear/{id}/approve', [ExternalAssignmentRequestController::class, 'approve'])
        ->name('ear.approve');
    Route::post('/ear/{id}/report/store', [ExternalAssignmentRequestController::class, 'storeReport'])
        ->name('ear.report.store');


    //Overtime Type
    Route::get('overtime_type', [OvertimeTypeController::class, 'index'])->name('overtime_type');
    Route::get('overtime_type_datatables', [OvertimeTypeController::class, 'getDatatables']);
    Route::post('ot_save', [OvertimeTypeController::class, 'storeData']);
    Route::post('ot_delete', [OvertimeTypeController::class, 'deleteData']);
    Route::post('ot_import', [OvertimeTypeController::class, 'importData']);
    Route::post('check_exists_overtime_type', [OvertimeTypeController::class, 'checkExistsExternalTypes']);
    Route::get('export-perusahaan', [OvertimeTypeController::class, 'exportData']);

    // overtime
    Route::get('/overtime', [OvertimeRequestController::class, 'index'])->name('overtime.index');
    Route::get('/overtime/create', [OvertimeRequestController::class, 'create'])->name('overtime.create');
    Route::post('/overtime/store', [OvertimeRequestController::class, 'store'])->name('overtime.store');
    Route::get('/overtime/data', [OvertimeRequestController::class, 'getData'])->name('overtime.index.data');
    Route::get('/overtime/{id}', [OvertimeRequestController::class, 'show'])->name('overtime.show');
    Route::post('/overtime/{id}/approve', [OvertimeRequestController::class, 'approve'])->name('overtime.approve');
    Route::post('/overtime/{id}/report', [OvertimeRequestController::class, 'reportSubmit'])->name('overtime.report.submit');
    Route::post('/overtime/{id}/approve-hr', [OvertimeRequestController::class, 'approveHr'])->name('overtime.approve.hr');


    // absen manual
    Route::get('/manual-attendance', [AttendanceController::class, 'manualAttendance'])->name('manual.absensi');
    Route::post('/attendance/manualStore', [AttendanceController::class, 'manualStore'])->name('attendance.manual-store');
});


require __DIR__ . '/purchase_order.php';
require __DIR__ . '/sales.php';
require __DIR__ . '/customer.php';
require __DIR__ . '/finance.php';
require __DIR__ . '/report.php';
require __DIR__ . '/user.php';
require __DIR__ . '/inventory.php';
require __DIR__ . '/ecommerce.php';
require __DIR__ . '/amp.php';
