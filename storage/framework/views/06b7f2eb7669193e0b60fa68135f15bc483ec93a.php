
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
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <input type="search" class="form-control  col-6" id="wt_search" placeholder="Cari transaksi"/><br/>
                            <select class="form-control col-6" id="status_filter">
                                <option value="">- Filter Status -</option>
                                <option value="PAID">PAID</option>
                                <option value="UNPAID">UNPAID</option>
                                <option value="CANCEL">CANCEL</option>
                                <option value="SHIPPING NUMBER">SHIPPING NUMBER</option>
                                <option value="IN DELIVERY">IN DELIVERY</option>
                            </select><br/>
                            <select class="form-control col-6" id="payment_filter">
                                <option value="">- Filter Tipe Pembayaran -</option>
                                <option value="Bank BCA">Bank BCA</option>
                                <option value="Bank BRI">Bank BRI</option>
                                <option value="Midtrans">Midtrans</option>
                            </select><br/>
                            <table class="table table-hover table-checkable" id="Wttb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Tanggal</th>
                                        <th class="text-dark">Batas(Jam)</th>
                                        <th class="text-dark">Invoice</th>
                                        <th class="text-dark">Customer</th>
                                        <th class="text-dark">Total</th>
                                        <th class="text-dark">Kode Unik</th>
                                        <th class="text-dark">Kurir</th>
                                        <th class="text-dark">Ongkir</th>
                                        <th class="text-dark">Resi</th>
                                        <th class="text-dark">Item</th>
                                        <th class="text-dark">Metode</th>
                                        <th class="text-dark">Status</th>
                                        <th class="text-dark">Note</th>
                                        <th class="text-dark">FN</th>
                                        <th class="text-dark">R1</th>
                                        <th class="text-dark">R2</th>
                                        <th class="text-dark">R3</th>
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
<?php echo $__env->make('app.web_transaction.web_transaction_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app.web_transaction.web_transaction_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.structure', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/web_transaction/web_transaction.blade.php ENDPATH**/ ?>