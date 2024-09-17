
<?php $__env->startSection('content'); ?>
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Page Heading-->
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold my-1 mr-5">[<?php echo e($data['sa_code']); ?>] <?php echo e($data['st_name']); ?></h5>
                    <a id="back_btn" class="btn btn-sm btn-success">Kembali</a>
                    <?php if($data['sa_custom'] == '1'): ?>
                    <a id="custom_btn" class="btn btn-sm btn-info ml-4">Lihat BIN Custom</a>
                    <?php endif; ?>
                    <!--end::Page Title-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3 row">
                            <div class="card-title col-5 row">
                                <input type="search" class="form-control col-9" value="" id="bin_search" style="border:2px solid #000;" placeholder="BIN"/> <span class="col-2"><i id="locked_btn" class="fa fa-lock bg-primary text-white d-none" style="padding:5px; border-radius:10px;"></i></span>
                                <div class="col-12" id="itemList"></div>
                                <select class="form-control col-12 bg-primary text-white d-none" id="scan_filter">
                                    <option value='scanned'>Sudah Scan</option>
                                    <option value='unscan'>Belum Scan</option>
                                </select>
                            </div>
                            <div class="card-toolbar col-6 ">
                                <!--begin::Button-->
                                <a href="#" class="btn btn-primary font-weight-bolder mr-2 ml-auto" id="current_qty"></a>
                                <!--end::Button-->
                                <!--begin::Button-->
                                <a href="#" class="btn btn-light-primary font-weight-bolder" id="scanned_qty"></a>
                                <!--end::Button-->
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <div class="row col-12">
                            <input type="search" class="form-control col-10 mb-2" value="" style="border:2px solid #000;" id="barcode" placeholder="BARCODE"/>
                            <div class="col-1">
                            <i class="fa fa-camera bg-primary text-white p-2" id="scanner_btn"></i></div>
                            </div>
                            <div id="qr-reader" style="width:100%;"></div>
                            <input type="search" class="form-control col-12 mb-2" value="" style="border:2px solid #000;" id="manual" placeholder="cari artikel manual (brand nama warna size)"/>
                            <div id="manualList"></div>
                            <table class="table table-hover table-checkable" id="SAtb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">Artikel</th>
                                        <th class="text-dark"><input class="form-control" type="text" id="search_article" value="" placeholder="cari artikel pada list ..."/></th>
                                        <th class="text-dark"><?php if($data['sa_status'] != 1): ?><a class="btn btn-sm btn-info" id="sync_btn">Sync</a> <a class="btn btn-sm btn-danger" id="reset_btn">Reset</a><?php endif; ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <!--end: Datatable-->
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
<?php echo $__env->make('app.scan_adjustment.do_scan.do_scan_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app.scan_adjustment.do_scan.do_scan_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.structure', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/scan_adjustment/do_scan/do_scan.blade.php ENDPATH**/ ?>