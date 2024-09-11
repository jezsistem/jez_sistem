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
                        <h5 class="text-dark font-weight-bold my-1 mr-5"><?php echo e($data['subtitle']); ?></h5>
                        <!--end::Page Title-->
                    </div>
                    <!--end::Page Heading-->
                </div>
                <!--end::Info-->
            </div>
        </div>
        <!--end::Subheader-->
        <!--begin::Body-->
        <div class="card-body p-0">
            <!--begin::Stats-->
            <div class="card-spacer" style="padding-top: 0px;">
                <!--begin::Row-->
                <div class="row m-0">
                    <div class="col-lg-6 col-xxl-6" id="user_activity_reload">
                        <!--begin::List Widget 9-->
                        <div class="card card-custom card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header align-items-center border-0 mt-4">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="font-weight-bolder btn-sm btn-primary text-white">Filter Template</span>
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pt-4">
                                <select class="form-control mt-2 bg-primary text-white" id="st_filter">
                                    <option value='all'>- Semua Store -</option>
                                    <?php $__currentLoopData = $data['st_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <select class="form-control mt-2 bg-primary text-white" id="br_filter">
                                    <option value='all'>- Semua Brand -</option>
                                    <?php $__currentLoopData = $data['br_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <select class="form-control mt-2 bg-primary text-white" id="psc_filter">
                                    <option value='all'>- Semua Sub Kategori -</option>
                                    <?php $__currentLoopData = $data['psc_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <select class="form-control mt-2 bg-primary text-white" id="qty_filter">
                                    <option value='1'>- Hanya yang Ada Stok -</option>
                                    <option value='0'>- Termasuk yang Sudah Habis -</option>
                                </select>
                                <div id="bin_panel"></div><br/>
                                <div class="row" id="bin_filter_panel">

                                </div>
                                <!--end::Timeline-->
                            </div>
                            <!--end: Card Body-->
                        </div>
                        <!--end: List Widget 9-->
                    </div>
                    <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                        <!--begin::Stats Widget 11-->
                        <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary">
                                    <div class="font-weight-bold fs-4"><span id="cc_qty"></span></div>
                                    <div>Cash/Credit</div>
                                </span>
                                    <span class="btn-sm btn-primary">
                                    <div class="font-weight-bold fs-4"><span id="c_qty"></span></div>
                                    <div>Consigntment</div>
                                </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary">
                                    <div class="font-weight-bold fs-4"><span id="cc_value"></span></div>
                                    <div>C/C Value</div>
                                </span>
                                    <span class="btn-sm btn-primary">
                                    <div class="font-weight-bold fs-4"><span id="c_value"></span></div>
                                    <div>Con Value</div>
                                </span>
                                </div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Stats Widget 11-->
                    </div>
                </div>
                <!--end::Row-->
            </div>
            <!--end::Stats-->
        </div>
        <!--end::Body-->
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b">
                            <!--begin::Header-->
                            <div class="card-header h-auto align-items-center justify-content-between">
                                <!--begin::Dropdown-->
                                <a class="btn btn-primary mt-2" id="export_btn">Export Template</a>
                                <!--end::Dropdown-->
                            </div>
                            <div class="card-body table-responsive">
                                <input type="search" class="form-control  col-6" id="stock_search" placeholder="Cari stok"/><br/>
                                <table class="table table-hover table-checkable" id="Stocktb">
                                    <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">BIN</th>
                                        <th class="text-dark">BRAND</th>
                                        <th class="text-dark">ARTIKEL</th>
                                        <th class="text-dark">WARNA</th>
                                        <th class="text-dark">SIZE</th>
                                        <th class="text-dark">Sub Kategori</th>
                                        <th class="text-dark">Stok</th>
                                        <th class="text-dark">Harga Beli</th>
                                        <th class="text-dark">Harga Jual</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b">
                            <!--begin::Header-->
                            <div class="card-body row d-flex">
                                <div class="col-4">
                                    <a class="btn btn-success mt-2" id="import_btn">Import Template</a>
                                    <input type="search" class="form-control form-control-sm col-6 mt-2" id="ma_search" placeholder="Cari kode"/>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover table-checkable" id="MassAdjustmenttb">
                                    <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Kode</th>
                                        <th class="text-dark">Store</th>
                                        <th class="text-dark">Dibuat Oleh</th>
                                        <th class="text-dark">Approval</th>
                                        <th class="text-dark">Eksekutor</th>
                                        <th class="text-dark">Editor</th>
                                        <th class="text-dark">Dibuat</th>
                                        <th class="text-dark">Diupdate</th>
                                        <th class="text-dark">Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--end::Card-->
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b d-none" id="mad_panel">
                            <!--begin::Header-->
                            <div class="card-body row">
                                <div class="col-4">
                                    <a class="btn btn-primary mt-2"><span data-id="" id="ma_code">MADJxxxxxx</span></a>
                                    <input type="search" class="form-control form-control-sm col-12 mt-2" id="mad_search" placeholder="Cari artikel"/>
                                </div>
                                <div class="col-4">
                                    Approval
                                    <div class="row">
                                        <input class="form-control col-8" placeholder="Approval" data-id="" type="text" id="approval_label" readonly/> <a class="btn btn-sm btn-success col-2" style="background:#007bff;" id="approval_btn">Approve</a>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <a class="btn btn-success ml-auto" style="float:right; background:#007bff;" id="execution_btn">Eksekusi Penyesuaian</a>
                                    <a class="btn btn-primary ml-auto mr-2" style="float:right;" id="export_mad_btn">Export</a>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover table-checkable" id="MassAdjustmentDetailtb">
                                    <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">BIN</th>
                                        <th class="text-dark">BRAND</th>
                                        <th class="text-dark">ARTIKEL</th>
                                        <th class="text-dark">WARNA</th>
                                        <th class="text-dark">SIZE</th>
                                        <th class="text-dark">Sub Kategori</th>
                                        <th class="text-dark">HB</th>
                                        <th class="text-dark">HJ</th>
                                        <th class="text-dark">Qty System</th>
                                        <th class="text-dark">Qty SO</th>
                                        <th class="text-dark">Type</th>
                                        <th class="text-dark">Diff</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
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
    <?php echo $__env->make('app.mass_adjustment.mass_adjustment_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('app.mass_adjustment.mass_adjustment_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.structure', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/jezpro.id/public_html/resources/views/app/mass_adjustment/mass_adjustment.blade.php ENDPATH**/ ?>