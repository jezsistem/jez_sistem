<?php

use App\Http\Controllers\PurchaseOrderArticleController;
use App\Http\Controllers\PurchaseOrderArticleDetailController;
use App\Http\Controllers\PurchaseOrderArticleDetailStatusController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderImportExcelController;
use App\Http\Controllers\PurchaseOrderReceiveController;
use App\Http\Controllers\POPurchaseReceiveImportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\POReceiveApprovalController;
use App\Http\Controllers\PreOrderArticleController;
use App\Http\Controllers\PreOrderArticleDetailController;
use App\Http\Controllers\PreOrderController;
use App\Http\Controllers\PurchaseOrderReceiveCODController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Purchase Order Routes
|--------------------------------------------------------------------------
|
| Here is where you can register purchase order routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
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
    Route::post('po_invoice_image', [PurchaseOrderController::class, 'uploadImageInvoice']);
    Route::get('po_article_export', [PurchaseOrderController::class, 'exportPurchaseOrderArticleData']);
    Route::post('po_transfer_image', [PurchaseOrderController::class, 'uploadImageTransfer']);

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
    Route::get('po_delivery_order_image_datatable', [PurchaseOrderReceiveController::class, 'getImageDeliveryOrdersDatatables']);
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

    // POReceiveApprovalController
    Route::get('approval_penerimaan', [POReceiveApprovalController::class, 'index']);
    Route::get('ap_datatables', [POReceiveApprovalController::class, 'getDatatables']);
    Route::get('apd_datatables', [POReceiveApprovalController::class, 'getDetailDatatables']);
    Route::post('ap_save', [POReceiveApprovalController::class, 'saveData']);
    Route::post(
        'ap_delete',
        [POReceiveApprovalController::class, 'deleteData']
    );
    Route::post('apd_approve', [POReceiveApprovalController::class, 'approveData']);
    Route::get('apd_total_price', [POReceiveApprovalController::class, 'createTotalPrice']);

    Route::get('pre_order', [PreOrderController::class, 'index']);
    Route::get('pre_order_datatables', [PreOrderController::class, 'getDatatables']);
    Route::post('create_pre_order', [PreOrderController::class, 'createPreOrder']);
    Route::post('create_pre_order_detail', [PreOrderController::class, 'createPreOrderDetail']);
    Route::post('check_pre_order_detail', [PreOrderController::class, 'checkPreOrderDetail']);
    Route::post('reload_pre_order_detail', [PreOrderController::class, 'reloadPreOrderDetail']);
    Route::post('pre_order_choose_store', [PreOrderController::class, 'chooseStorePo']);
    Route::post('pre_order_choose_product_supplier', [PreOrderController::class, 'chooseProductSupplierPo']);
    Route::post('pre_order_choose_brand', [PreOrderController::class, 'chooseBrandePo']);
    Route::post('pre_order_choose_season', [PreOrderController::class, 'chooseSeasonPo']);
    Route::post('pre_order_detail', [PreOrderController::class, 'poDetail']);
    Route::post('cancel_pre_order', [PreOrderController::class, 'cancelPreOrder']);
    Route::post('pre_order_save_draft', [PreOrderController::class, 'poSaveDraft']);
    Route::post('check_pre_order_purchase_order', [PreOrderController::class, 'checkPreOrderPurchaseOrder']);

    // Purchase Order Article
    Route::post('proa_delete', [PreOrderArticleController::class, 'deleteData']);
    Route::post('proa_save_discount', [PreOrderArticleController::class, 'saveDiscount']);
    Route::post('proa_save_extra_discount', [PreOrderArticleController::class, 'saveExtraDiscount']);
    Route::post('proa_save_reminder', [PreOrderArticleController::class, 'saveReminder']);

    // Pre Order Article Detail
    Route::post('proad_delete', [PreOrderArticleDetailController::class, 'deleteData']);
    Route::post('proad_save_purchase_price', [PreOrderArticleDetailController::class, 'savePurchasePrice']);
    Route::post('proad_save_qty_total', [PreOrderArticleDetailController::class, 'saveQtyTotal']);

    // Penerimaan COD
    Route::get('penerimaan_cod', [PurchaseOrderReceiveCODController::class, 'index']);
    Route::get('poc_datatables', [PurchaseOrderReceiveCODController::class, 'getDatatables']);
    Route::post('poc_update_is_paid', [PurchaseOrderReceiveCODController::class, 'updateIsPaid']);
});
