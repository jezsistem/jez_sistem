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
                <a href="#" id="synchronize_btn" class="btn-sm btn-info">Synchronize</a>
                <input type="hidden" id="dashboard_date" value=""/>
                <a class="btn btn-date-info font-weight-bold mr-2" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Select dashboard daterange" data-placement="left" style="cursor:pointer;">
                    <span class="font-size-base" id="kt_dashboard_daterangepicker_title">Today</span>
                    <span class="font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                </a>
                <!--end::Daterange-->
                <!--begin::Dropdowns-->
                <div class="dropdown dropdown-inline" data-toggle="tooltip" title="Quick actions" data-placement="left">
                    <a href="#" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="svg-icon svg-icon-white svg-icon-lg">
                            <!--begin::Svg Icon | path:assets/media/svg/icons/Files/File-plus.svg-->
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.00068431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path d="M11,14 L9,14 C8.44771525,14 8,13.5522847 8,13 C8,12.4477153 8.44771525,12 9,12 L11,12 L11,10 C11,9.44771525 11.4477153,9 12,9 C12.5522847,9 13,9.44771525 13,10 L13,12 L15,12 C15.5522847,12 16,12.4477153 16,13 C16,13.5522847 15.5522847,14 15,14 L13,14 L13,16 C13,16.5522847 12.5522847,17 12,17 C11.4477153,17 11,16.5522847 11,16 L11,14 Z" fill="#000000" />
                                </g>
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                    </a>
                </div>
                <!--end::Dropdowns-->
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Dashboard-->
            <!--begin::Row-->
            <div class="row">
                <div class="col-lg-6 col-xxl-4">
                    <!--begin::Stats Widget 11-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bold fs-4">
                                    Jual Bersih
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="text-white font-weight-bolder font-size-h1 btn-sm btn-primary" id="nett_sales_label">000000</span>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 11-->
                </div>
                <div class="col-lg-6 col-xxl-4">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bold fs-4">
                                    Profit
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="text-white font-weight-bolder font-size-h1 btn-sm btn-primary" id="profit_label">000000</span>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>
                
                <div class="col-lg-6 col-xxl-4">
                    <!--begin::Stats Widget 11-->
                    <div class="card card-custom card-stretch gutter-b border" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bold fs-4">
                                    Pembelian
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="text-white font-weight-bolder font-size-h1 btn-sm btn-primary" id="purchase_label">000000</span>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 11-->
                </div>
                <div class="col-lg-6 col-xxl-4">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bold fs-4" id="cash_credit_asset_label">
                                    Asset CC
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="text-white font-weight-bolder font-size-h1 btn-sm btn-primary" id="assets_label">000000</span>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>
                <div class="col-lg-6 col-xxl-4">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bold fs-4" id="consignment_asset_label">
                                    Asset CON 
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="text-white font-weight-bolder font-size-h1 btn-sm btn-primary" id="consign_assets_label">000000</span>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>
                <div class="col-lg-6 col-xxl-4">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->  
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bold fs-4">
                                    Hutang
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="text-white font-weight-bolder font-size-h1 btn-sm btn-primary" id="debt_label"> 000000 </span>
                                </div>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>
                <div class="col-lg-12 col-xxl-12">
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px; padding:10px;">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-info font-weight-bold fs-4">
                                    Store
                                </span>
                                <span class="btn-sm btn-primary font-weight-bold fs-4" id="store_excel">
                                    Excel
                                </span>
                            </div>
                            <div class="table-responsive">
                            <input type="search" class="form-control form-control-sm" id="store_search" placeholder="Cari store"/><br/>
                            <table class="table table-hover table-checkable" id="Storetb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Tanggal</th>
                                        <th class="text-dark">Store</th>
                                        <th class="text-dark">Sales</th>
                                        <th class="text-dark">Profit</th>
                                        <th class="text-dark">Purchase</th>
                                        <th class="text-dark">Asset CC</th>
                                        <th class="text-dark">Asset Con</th>
                                        <th class="text-dark">Hutang</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-xxl-12">
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px; padding:10px;">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-info font-weight-bold fs-4">
                                    Brand
                                </span>
                                <span class="btn-sm btn-primary font-weight-bold fs-4" id="brand_excel">
                                    Excel
                                </span>
                            </div>
                            <div class="table-responsive">
                            <input type="search" class="form-control form-control-sm" id="brand_search" placeholder="Cari brand"/><br/>
                            <table class="table table-hover table-checkable" id="Brandtb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Tanggal</th>
                                        <th class="text-dark">Brand</th>
                                        <th class="text-dark">Sales</th>
                                        <th class="text-dark">Profit</th>
                                        <th class="text-dark">Purchase</th>
                                        <th class="text-dark">Asset CC</th>
                                        <th class="text-dark">Asset Con</th>
                                        <th class="text-dark">Hutang</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Row-->
            <!--end::Dashboard-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>

<!-- Modal-->
<div class="modal fade" id="NotifModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Silahkan klik tombol synchronize untuk mendapatkan update data terbaru, karena robot otomatis baru akan melakukan close setiap pukul 23.57</h5>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="WaitingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Mohon menunggu, permintaan anda sedang kami proses, sekitar 30 detik sampai 1 menit untuk kalkulasi summary per brand</h5>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
<!--end::Content-->
<?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app.dashboard_v2.dashboard_v2_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.structure', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/jezpro.id/public_html/resources/views/app/dashboard_v2/dashboard_v2.blade.php ENDPATH**/ ?>