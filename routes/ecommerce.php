<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\FreeShippingController;
use App\Http\Controllers\TopdealController;
use App\Http\Controllers\WebArticleController;
use App\Http\Controllers\WebBannerController;
use App\Http\Controllers\WebBrandController;
use App\Http\Controllers\WebCategoryController;
use App\Http\Controllers\WebConfirmationController;
use App\Http\Controllers\WebReminderController;
use App\Http\Controllers\WebSubCategoryController;
use App\Http\Controllers\WebTransactionController;

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function() {
    // Web TRANSACTION
    Route::get('website_transaction', [WebTransactionController::class, 'index']);
    Route::get('website_transaction_v2', [WebTransactionController::class, 'indexUpdated']);
    Route::get('wt_datatables', [WebTransactionController::class, 'getDatatables']);
    Route::get('wt_datatables_simple', [WebTransactionController::class, 'getDatatablesForSimple']);
    Route::post('wt_save', [WebTransactionController::class, 'storeData']);
    Route::post('update_data', [WebTransactionController::class, 'updateData']);

    // Web Confirmation
    Route::get('konfirmasi',
        [WebConfirmationController::class, 'index']
    );
    Route::get('konfirmasi_v2', [WebConfirmationController::class, 'indexUpdated']);
    Route::get('konfirmasi_datatables', [WebConfirmationController::class, 'getDatatables']);
    Route::get('konfirmasi_datatables_simple', [WebConfirmationController::class, 'getDatatablesForSimple']);
    Route::post('wbc_save', [WebConfirmationController::class, 'storeData']);
    Route::post('wbc_delete',
        [WebConfirmationController::class, 'deleteData']
    );
    Route::post('wbc_read', [WebConfirmationController::class, 'readData']);

    // Web Article 
    Route::get('web_artikel',
        [WebArticleController::class, 'index']
    );
    Route::get('web_artikel_v2', [WebArticleController::class, 'indexUpdated']);
    Route::get('web_article_datatables', [WebArticleController::class, 'getDatatables']);
    Route::get('web_article_datatables_simple', [WebArticleController::class, 'getDatatablesForSimple']);
    Route::post('generate_slug', [WebArticleController::class, 'generateSlug']);
    Route::post('save_description', [WebArticleController::class, 'saveDescription']);
    Route::post('update_main_image', [WebArticleController::class, 'saveMainImage']);
    Route::post('update_detail_image', [WebArticleController::class, 'saveDetailImage']);
    Route::post('save_weight', [WebArticleController::class, 'saveWeight']);

    // Topdeal
    Route::get('topdeals', [TopdealController::class, 'index']);
    Route::get('topdeals_v2', [TopdealController::class, 'indexUpdated']);
    Route::get('topdeals_datatables', [TopdealController::class, 'getDatatables']);
    Route::get('topdeals_datatables_simple', [TopdealController::class, 'getDatatablesForSimple']);
    Route::get('topdeals_article_datatables', [TopdealController::class, 'getTopdealsArticleDatatables']);
    Route::get('topdeals_article_datatables_simple', [TopdealController::class, 'getTopdealsArticleDatatablesForSimple']);
    Route::get('article_datatables', [TopdealController::class, 'getArticleDatatables']);
    Route::get('article_datatables_simple', [TopdealController::class, 'getArticleDatatablesForSimple']);
    Route::post('td_save', [TopdealController::class, 'storeData']);
    Route::post('td_delete',
        [TopdealController::class, 'deleteData']
    );
    Route::post('add_topdeals', [TopdealController::class, 'addTopdeals']);
    Route::post('delete_topdeals', [TopdealController::class, 'deleteTopdeals']);

    // Web Banner
    Route::get('web_banner',
        [WebBannerController::class, 'index']
    );
    Route::get('web_banner_v2', [WebBannerController::class, 'indexUpdated']);
    Route::get('wb_datatables', [WebBannerController::class, 'getDatatables']);
    Route::get('wb_datatables_simple', [WebBannerController::class, 'getDatatablesForSimple']);
    Route::get('bb_brand_datatables', [WebBannerController::class, 'getBrandDatatables']);
    Route::get('bb_brand_datatables_simple', [WebBannerController::class, 'getBrandDatatablesForSimple']);
    Route::get('bb_article_datatables', [WebBannerController::class, 'getArticleDatatables']);
    Route::get('bb_article_datatables_simple', [WebBannerController::class, 'getArticleDatatablesForSimple']);
    Route::get('reload_article', [WebBannerController::class, 'reloadArticle']);
    Route::post('wb_save', [WebBannerController::class, 'storeData']);
    Route::post('wb_delete',
        [WebBannerController::class, 'deleteData']
    );
    Route::post('bb_save', [WebBannerController::class, 'storeDataBrand']);
    Route::post('bb_delete',
        [WebBannerController::class, 'deleteDataBrand']
    );
    Route::post('bbd_save', [WebBannerController::class, 'storeDataArticle']);
    Route::post('bbd_delete',
        [WebBannerController::class, 'deleteDataArticle']
    );


    // Bank
    Route::get('bank', [BankController::class, 'index']);
    Route::get('bank_v2', [BankController::class, 'indexUpdated']);
    Route::get('bank_datatables', [BankController::class, 'getDatatables']);
    Route::get('bank_datatables_simple', [BankController::class, 'getDatatablesForSimple']);
    Route::post('bank_save',
        [BankController::class, 'storeData']
    );
    Route::post('bank_delete', [BankController::class, 'deleteData']);
    Route::post('check_exists_bank', [BankController::class, 'checkExistsBank']);


    // Web Brand
    Route::get('web_brand', [WebBrandController::class, 'index']);
    Route::get('web_brand_v2', [WebBrandController::class, 'indexUpdated']);
    Route::get('web_brand_datatables', [BrandController::class, 'getDatatables']);
    Route::get('web_brand_datatables_simple', [BrandController::class, 'getDatatablesForSimple']);
    Route::post('web_brand_save', [BrandController::class, 'storeData']);
    Route::post('web_brand_delete', [BrandController::class, 'deleteData']);
    Route::post('web_brand_delete_logo', [BrandController::class, 'deleteBrandImage']);
    Route::post('web_brand_delete_banner', [BrandController::class, 'deleteBannerImage']);
    Route::post('web_brand_import', [BrandController::class, 'importData']);
    Route::post('web_brand_check_exists', [BrandController::class, 'checkExistsBrand']);

    // Web Category
    Route::get('kategori_slug', [WebCategoryController::class, 'index']);
    Route::get('kategori_slug_v2', [WebCategoryController::class, 'indexUpdated']);
    Route::get('web_category_datatables', [WebCategoryController::class, 'getDatatables']);
    Route::get('web_category_datatables_simple', [WebCategoryController::class, 'getDatatablesForSimple']);
    Route::post('cs_save', [WebCategoryController::class, 'storeData']);
    Route::post('cs_delete',
        [WebCategoryController::class, 'deleteData']
    );
    Route::post('delete_image_kategori', [WebCategoryController::class, 'deleteImage']);
    Route::post('delete_banner_kategori', [WebCategoryController::class, 'deleteBannerImage']);

    // Web Sub Kategori
    Route::get('sub_kategori', [WebSubCategoryController::class, 'index']);
    Route::get('sub_kategori_v2', [WebSubCategoryController::class, 'indexUpdated']);
    Route::get('wsc_datatables', [WebSubCategoryController::class, 'getDatatables']);
    Route::get('wsc_datatables_simple', [WebSubCategoryController::class, 'getDatatablesForSimple']);
    Route::post('wsc_save', [WebSubCategoryController::class, 'storeData']);
    Route::post('delete_image_sub_kategori', [WebSubCategoryController::class, 'deleteImage']);


    // Free Shipping Controller
    Route::get('free_ongkir', [FreeShippingController::class, 'index']);
    Route::get('free_ongkir_v2', [FreeShippingController::class, 'indexUpdated']);
    Route::get('free_shipping_datatables', [FreeShippingController::class, 'getDatatables']);
    Route::get('free_shipping_datatables_simple', [FreeShippingController::class, 'getDatatablesForSimple']);
    Route::post('fs_save', [FreeShippingController::class, 'storeData']);
    Route::post('fs_delete', [FreeShippingController::class, 'deleteData']);


    // Web Reminder
    Route::get('web_reminder', [WebReminderController::class, 'index']);
    Route::get('web_reminder_v2', [WebReminderController::class, 'indexUpdated']);
    Route::get('wr_datatables', [WebReminderController::class, 'getDatatables']);
    Route::get('wr_datatables_simple', [WebReminderController::class, 'getDatatablesForSimple']);


    // Blog 
    Route::get('blog', [BlogController::class, 'index']);
    Route::get('blog_v2', [BlogController::class, 'indexUpdated']);
    Route::get('bcc_datatables', [BlogController::class, 'getDetailDatatables']);
    Route::get('bcc_datatables_simple', [BlogController::class, 'getDatatablesForSimple']);
    Route::post('get_bc', [BlogController::class, 'getData']);
    Route::post('bc_save', [BlogController::class, 'storeData']);
    Route::post('bc_delete', [BlogController::class, 'deleteData']);
    Route::post('bcc_save', [BlogController::class, 'storeDetailData']);
    Route::post('bcc_delete', [BlogController::class, 'deleteDetailData']);

});