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
                <!--end::Daterange-->
                <!--begin::Dropdowns-->
                <div class="dropdown dropdown-inline" data-toggle="tooltip" title="Quick actions" data-placement="left">
                    <a href="#" class="btn btn-sm btn-clean btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="svg-icon svg-icon-white svg-icon-lg">
                            <!--begin::Svg Icon | path:assets/media/svg/icons/Files/File-plus.svg-->
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
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
                <div class="col-lg-6 col-xxl-6" id="user_activity_reload">
                    <!--begin::List Widget 9-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Body-->
                        <div class="card-body pt-4">
                            <!--begin::Timeline-->
                            <a class="btn btn-date-info font-weight-bold mr-2" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Select dashboard daterange" data-placement="left" style="cursor:pointer;">
                                <span class="font-size-base" id="kt_dashboard_daterangepicker_title">Today</span>
                                <span class="font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                            </a>
                            <select class="form-control mt-2 bg-primary text-white" id="store_filter">
                                <option value=''>- Store -</option>
                                <?php $__currentLoopData = $data['st_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <select class="form-control mt-2 bg-primary text-white" id="brand_filter">
                                <option value=''>- Tentukan Brand -</option>
                                <?php $__currentLoopData = $data['br_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <a class="btn btn-success mt-2" id="exec_btn">Tampilkan</a>
                            <!--end::Timeline-->
                        </div>
                        <!--end: Card Body-->
                    </div>
                    <!--end: List Widget 9-->
                </div>
                <div class="col-lg-12 col-xxl-12 d-none" style="min-height: 315px;" id="graph_panel">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bolder">
                                    Kartu Stok
                                </span>
                            </div>
                            <div id="chart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>
                <div class="col-xl-12 d-none" id="article_panel">
                    <!--begin::Tables Widget 9-->
                    <div class="card card-xl-stretch mb-5 mb-xl-12">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-5">
                            <h4 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold mb-1">Artikel</span>
                            </h4>
                            <div class="card-toolbar" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover" title="Detail">
                                <a href="#" class="btn btn-sm btn-success font-weight-bolder"  id="export_btn">
                                <i class="fa fa-download"></i> Download Excel</a>
                            </div>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body py-3">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <input type="search" class="form-control  col-6" id="article_search" placeholder="Cari Artikel"/><br/>
                                <table class="table table-border table-striped table-row-gray-300 align-middle gs-0 gy-4" id="article_table">
                                    <!--begin::Table head-->
                                    <thead class="bg-primary">
                                        <tr class="fw-bold text-black">
                                            <th class="text-white">No</th>
                                            <th class="text-white">Brand</th>
                                            <th class="text-white">Article</th>
                                            <th class="text-white">Color</th>
                                            <th class="text-white">Size</th>
                                            <th class="text-white">Beginning Stock</th>
                                            <th class="text-white">Purchase</th>
                                            <th class="text-white">Trans-In</th>
                                            <th class="text-white">Trans-Out</th>
                                            <th class="text-white">Sales</th>
                                            <th class="text-white">Refund</th>
                                            <th class="text-white">Adj(-)</th>
                                            <th class="text-white">Adj(+)</th>
                                            <th class="text-white">MAdj(-)</th>
                                            <th class="text-white">MAdj(+)</th>
                                            <th class="text-white">SAdj(-)</th>
                                            <th class="text-white">SAdj(+)</th>
                                            <th class="text-info">Waiting (OFF/CO/TAKE)</th>
                                            <th class="text-danger">Cross Setup In</th>
                                            <th class="text-danger">Cross Setup Out</th>
                                            <th class="text-success">Ending Stock</th>
                                            <th class="text-warning">Today's Exception</th>
                                            <th class="text-warning">Today's Stock</th>
                                            <th class="text-white">HB</th>
                                            <th class="text-white">HJ</th>
                                        </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>

                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table container-->
                        </div>
                        <!--begin::Body-->
                    </div>
                    <!--end::Tables Widget 9-->
                </div>
                <!-- INCOMING STOCK -->
            </div>
            <!--end::Row-->
            <!--end::Dashboard-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
<?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app.stock_card.stock_card_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.structure', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/stock_card/stock_card.blade.php ENDPATH**/ ?>