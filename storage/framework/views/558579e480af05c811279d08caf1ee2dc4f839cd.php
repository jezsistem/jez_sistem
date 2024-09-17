
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
                            <input type="search" class="form-control  col-6" id="wbc_search" placeholder="Cari konfirmasi"/><br/>
                            <select class="form-control col-6" id="status_filter">
                                <option value="">- Filter Status -</option>
                                <option value="0">Menunggu Konfirmasi</option>
                                <option value="1">Diterima</option>
                                <option value="2">Ditolak</option>
                            </select><br/>
                            <table class="table table-hover table-checkable" id="Wbctb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Tanggal</th>
                                        <th class="text-dark">Invoice</th>
                                        <th class="text-dark">Nama</th>
                                        <th class="text-dark">Gambar</th>
                                        <th class="text-dark" style="white-space:nowrap;">Rek Tujuan</th>
                                        <th class="text-dark">IP</th>
                                        <th class="text-dark">Dibaca</th>
                                        <th class="text-dark">Terakhir</th>
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
<?php echo $__env->make('app.web_confirmation.web_confirmation_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app.web_confirmation.web_confirmation_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.structure', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/web_confirmation/web_confirmation.blade.php ENDPATH**/ ?>