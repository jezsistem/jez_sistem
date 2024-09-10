
<?php $__env->startSection('content'); ?>
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-2">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5 fs-3"><?php echo e($data['subtitle']); ?></h5>
                <!--end::Page Title-->
            </div>
            <!--end::Info-->
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <!--begin::Daterange-->
                <a href="<?php echo e(url('dashboards')); ?>" class="btn btn-inventory mr-2" id="del_activity_btn">Dashboard V2</a>
                <input type="hidden" id="dashboard_date" value=""/>
                <a href="https://report.jez.co.id" target="_blank" class="btn btn-dark mr-2" id="urban_panel_btn" style="font-weight:500;">Report Dashboard</a>
                <input type="hidden" id="urban_panel" value="" />
                <a class="btn btn-date-info font-weight-bold" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Select dashboard daterange" data-placement="left" style="cursor:pointer;">
                    <span class="font-size-base" id="kt_dashboard_daterangepicker_title">Today</span>
                    <span class="font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
                    <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                    </svg>
                </a>
                <!--end::Daterange-->
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <input type="hidden" id="c_first_load" value=""/>
    <input type="hidden" id="ca_first_load" value=""/>
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Dashboard-->
            <!--begin::Row-->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <a href="#" class="btn btn-primary d-flex align-items-center" id="detail_activity_btn">
                    <i class="ki-outline ki-questionnaire-tablet pr-0">
                    <i class="path1"></i>
                    <i class="path2"></i>
                    </i>
                    Aktifitas User
                </a>
            </div>
            <div class="row">
                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 11-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="col-6 px-0 title">
                                    <span class="font-weight-bold fs-4 d-flex align-items-center mb-2 title">
                                        <i class="ki-outline ki-financial-schedule fs-2 mr-2">
                                            <i class="path1"></i>
                                            <i class="path2"></i>
                                            <i class="path3"></i>
                                            <i class="path4"></i>
                                        </i>
                                        Sales
                                    </span>
                                    <div class="select-card">
                                        <select class="select-sm bg-light-primary" id="scross_filter">
                                            <option value="nocross">Tanpa Cross</option>
                                            <option value="cross">Dengan Cross</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="text-right col-6 px-0">
                                    <a class="font-weight-bold fs-5 btn btn-dark" id="nett_sales_label"><span id="nett_sales_label_reload" style="white-space:nowrap;"></span></a>
                                </div>
                            </div>
                            <center class="button-show_ns pt-10">
                                <a class="btn btn-inventory" id="ns_show_btn">Tampilkan</a>
                                <img class="d-none" id="ns_loading" src="<?php echo e(asset('upload/loading/loading.gif')); ?>" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="nettsaleChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 11-->
                </div>
                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="col-6 px-0 title">
                                    <span class="font-weight-bold fs-4 d-flex align-items-center mb-2 title">
                                        <i class="ki-outline ki-wallet fs-2 mr-2">
                                            <i class="path1"></i>
                                            <i class="path2"></i>
                                            <i class="path3"></i>
                                            <i class="path4"></i>
                                        </i>
                                        Profit
                                    </span>
                                    <div class="select-card">
                                        <select class="select-sm ml-2 bg-light-primary p-1" id="pcross_filter">
                                            <option value="nocross">Tanpa Cross</option>
                                            <option value="cross">Dengan Cross</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="text-right col-6 px-0">
                                    <span class="font-weight-bold fs-5 btn btn-dark" id="profit_label"><span id="profit_label_reload" style="white-space:nowrap;"></span></span>
                                </div>
                            </div>
                            <center class="button-show_pr pt-10">
                                <a class="btn btn-inventory" id="pr_show_btn">Tampilkan</a>
                                <img class="d-none" id="pr_loading" src="<?php echo e(asset('upload/loading/loading.gif')); ?>" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="profitChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>

                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 11-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-grow-1">
                                <span class="font-weight-bold fs-4 d-flex align-items-center mb-2 title">
                                    <i class="ki-outline ki-graph-up fs-2 mr-2">
                                        <i class="path1"></i>
                                        <i class="path2"></i>
                                        <i class="path3"></i>
                                        <i class="path4"></i>
                                        <i class="path5"></i>
                                        <i class="path6"></i>
                                    </i>
                                    Cross Sales
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="font-weight-bold fs-5 btn btn-dark"  id="cnett_sales_label"><span id="cnett_sales_label_reload"></span></span>
                                </div>
                            </div>
                            <center class="button-show_cns pt-20">
                                <a class="btn btn-inventory" id="cns_show_btn">Tampilkan</a>
                                <img class="d-none" id="cns_loading" src="<?php echo e(asset('upload/loading/loading.gif')); ?>" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="cnettsaleChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 11-->
                </div>
                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-grow-1">
                                <span class="font-weight-bold fs-4 d-flex align-items-center mb-2 title">
                                    <i class="ki-outline ki-bill fs-2 mr-2">
                                        <i class="path1"></i>
                                        <i class="path2"></i>
                                        <i class="path3"></i>
                                        <i class="path4"></i>
                                        <i class="path5"></i>
                                        <i class="path6"></i>
                                    </i>                                    
                                    Cross Profit
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="font-weight-bold fs-5 btn btn-dark"  id="cprofit_label"><span id="cprofit_label_reload"></span></span>
                                </div>
                            </div>
                            <center class="button-show_cpr pt-20">
                                <a class="btn btn-inventory" id="cpr_show_btn">Tampilkan</a>
                                <img class="d-none" id="cpr_loading" src="<?php echo e(asset('upload/loading/loading.gif')); ?>" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="cprofitChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>

                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 11-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-grow-1">
                                <span class="font-weight-bold fs-4 d-flex align-items-center mb-2 title">
                                    <i class="ki-outline ki-basket fs-2 mr-2">
                                        <i class="path1"></i>
                                        <i class="path2"></i>
                                        <i class="path3"></i>
                                        <i class="path4"></i>
                                    </i>
                                    Pembelian
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="font-weight-bold fs-5 btn btn-dark"  id="purchase_label"><span id="purchase_label_reload"></span></span>
                                </div>
                            </div>
                            <center class="button-show_pc pt-20">
                                <a class="btn btn-inventory" id="pc_show_btn">Tampilkan</a>
                                <img class="d-none" id="pc_loading" src="<?php echo e(asset('upload/loading/loading.gif')); ?>" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="purchaseChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 11-->
                </div>
                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-grow-1">
                                <span class="font-weight-bold fs-4 d-flex align-items-center mb-2 title">
                                    <i class="ki-outline ki-home-1 fs-2 mr-2">
                                        <i class="path1"></i>
                                        <i class="path2"></i>
                                    </i>
                                    Aset Cash/Credit
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="font-weight-bold fs-5 btn btn-dark"  id="assets_label"></span>
                                </div>
                            </div>
                            <center class="button-show_a pt-20">
                                <a class="btn btn-inventory" id="a_show_btn">Tampilkan</a>
                                <img class="d-none" id="a_loading" src="<?php echo e(asset('upload/loading/loading.gif')); ?>" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="assetChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>
                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-grow-1">
                                <span class="font-weight-bold fs-4 d-flex align-items-center mb-2 title">
                                    <i class="ki-outline ki-exit-down fs-2 mr-2">
                                        <i class="path1"></i>
                                        <i class="path2"></i>
                                    </i>
                                    Consignment
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="font-weight-bold fs-5 btn btn-dark"  id="consign_assets_label"></span>
                                </div>
                            </div>
                            <center class="button-show_ca pt-20">
                                <a class="btn btn-inventory" id="ca_show_btn">Tampilkan</a>
                                <img class="d-none" id="ca_loading" src="<?php echo e(asset('upload/loading/loading.gif')); ?>" style="width:20%; padding-bottom:20px;"/></center>
                            </center>
                            <div class="d-none" id="consignAssetChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>
                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-grow-1">
                                <span class="font-weight-bold fs-4 d-flex align-items-center mb-2 title">
                                    <i class="ki-outline ki-file-down fs-2 mr-2">
                                        <i class="path1"></i>
                                        <i class="path2"></i>
                                    </i>
                                    Hutang
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="font-weight-bold fs-5 btn btn-dark"  id="debt_label"></span>
                                </div>
                            </div>
                            <center class="button-show_d pt-20">
                                <a class="btn btn-inventory" id="d_show_btn">Tampilkan</a>
                                <img class="d-none" id="d_loading" src="<?php echo e(asset('upload/loading/loading.gif')); ?>" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="debtChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>
            </div>
            <!--end::Row-->
            <!--end::Dashboard-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
<?php echo $__env->make('app.dashboard.dashboard_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app.dashboard.dashboard_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.structure', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\88 Other\Dev\jez_sistem\resources\views/app/dashboard/dashboard.blade.php ENDPATH**/ ?>