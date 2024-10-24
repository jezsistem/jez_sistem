<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\UserRatingController;

use Illuminate\Support\Facades\Route;

Route::get('rating_app', [UserRatingController::class, 'customerIndex']);

Route::middleware(['auth'])->group(function () {
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
    Route::get('customer_data_export', [CustomerController::class, 'exportData']);
    // User Rating
    Route::get('user_rating', [UserRatingController::class, 'index']);
    Route::get('rating_by_customer', [UserRatingController::class, 'customerIndex']);
    Route::get('rating_datatables', [UserRatingController::class, 'getDatatables']);
    Route::get('rating_history_datatables', [UserRatingController::class, 'getHistoryDatatables']);
    Route::post('autocomplete_subdistrict', [UserRatingController::class, 'fetchSubdistrict']);
    Route::post('autocomplete_city', [UserRatingController::class, 'fetchCity']);
    Route::post('check_waiting_for_review', [UserRatingController::class, 'checkWaitingForReview']);
    Route::post('save_rating', [UserRatingController::class, 'storeData']);
    Route::post('check_customer_rating_phone', [UserRatingController::class, 'checkCustomerPhone']);
    Route::post('check_rating_for_checkout', [UserRatingController::class, 'checkWaitingForCheckout']);
    // Whatsapp
    Route::get('whatsapp', [WhatsappController::class, 'index']);
    Route::get('whatsapp_datatables', [WhatsappController::class, 'getDatatables']);
    Route::post('send_wa', [WhatsappController::class, 'executeBlast']);
});