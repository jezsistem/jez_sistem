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
            <div class="form-group" style="padding-top:22px;">
                <select class="form-control" id="st_id_filter" name="st_id_filter" required>
                    <option value="">- Storage -</option>
                    <?php $__currentLoopData = $data['st_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($key == $data['user']->st_id): ?>
                        <option value="<?php echo e($key); ?>" selected><?php echo e($value); ?></option>
                        <?php else: ?>
                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <div id="st_id_filter_parent"></div>
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
                        <div class="card-header flex-wrap py-3">
                            <!--begin::Button-->
                            <select class="form-control" id="std_id" name="std_id" required>
                                <option value="">- Divisi -</option>
                                <?php $__currentLoopData = $data['std_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div id="std_id_parent"></div>
                            <!--end::Button-->
                            <!--begin::Button-->
                            <select class="form-control col-md-3 mr-2" id="status_filter"> 
                                <option value="">- Semua -</option>
                                <option value="DONE">DONE</option>
                                <option value="IN PROGRESS">IN PROGRESS</option>
                                <option value="WAITING FOR CONFIRMATION">WAITING FOR CONFIRMATION</option>
                            </select>
                            <!--end::Button-->
                        </div>
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <input type="search" class="form-control  col-6" id="invoice_tracking_search" placeholder="Cari invoice / nomor resi / customer"/><br/>
                            <table class="table table-hover table-checkable" id="CrossOrdertb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Invoice</th>
                                        <th class="text-dark">Kasir</th>
                                        <th class="text-dark">Customer</th>
                                        <th class="text-dark">Store Tujuan</th>
                                        <th class="text-dark">Kasir Tujuan</th>
                                        <th class="text-dark">Tanggal</th>
                                        <th class="text-dark">Item</th>
                                        <th class="text-dark">Total</th>
                                        <th class="text-dark">Reject</th>
                                        <th class="text-dark">Status</th>
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
<?php echo $__env->make('app.cross_order.cross_order_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app.cross_order.cross_order_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.structure', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/cross_order/cross_order.blade.php ENDPATH**/ ?>