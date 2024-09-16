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
                <div class="col-lg-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header h-auto">
                            <!--begin::Title-->
                            <div class="card-title py-5">
                                <h3 class="card-label">By Manual</h3>
                            </div>
                            <!--end::Title-->
                        </div>
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <div class="row">
                                <div class="form-group mb-1 pb-1 col-4">
                                    <!-- <input type="hidden" id="_pc_id" value="" /> -->
                                    <select class="form-control" id="st_id_start" name="st_id_start" required>
                                        <option value="">- Store Awal -</option>
                                        <?php $__currentLoopData = $data['st_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div id="st_id_start_parent"></div>
                                </div>
                                <div class="form-group mb-1 pb-1 col-4">
                                    <!-- <input type="hidden" id="_pc_id" value="" /> -->
                                    <select class="form-control" id="st_id_end" name="st_id_end" required>
                                        <option value="">- Store Tujuan -</option>
                                        <?php $__currentLoopData = $data['st_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div id="st_id_end_parent"></div>
                                </div>
                                <div class="form-group mb-1 pb-1 col-4">
                                    <a class="btn btn-primary col-12" id="transfer_btn" style="white-space:nowrap;">Transfer</a>
                                </div>
                            </div>
                            <div class="form-group mb-1 pb-1">
                                <select class="form-control" id="pl_id" name="pl_id" required>
                                    <option value="">- BIN Untuk Ditransfer -</option>
                                </select>
                                <div id="pl_id_parent"></div>
                            </div>
                            <!--end: Datatable-->
                            <div class="row">
                                <div class="form-group mb-1 pb-1 col-8">
                                    <input type="search" class="form-control" id="article_search" placeholder="Cari artikel / brand / warna"/>
                                </div>
                                <div class="form-group mb-1 pb-1 col-2">
                                    <a type="button" class="btn btn-light-primary font-weight-bolder" id="ImportModalBtn" aria-haspopup="true" aria-expanded="false">
                                    <span class="svg-icon svg-icon-md">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24" />
                                                <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
                                                <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>Import</a>
                                </div>

                                <div class="form-group mb-1 pb-1 col-2">
                                    <a type="button" class="btn btn-light-primary font-weight-bolder" id="CancelBtn" aria-haspopup="true" aria-expanded="false">
                                    <span class="svg-icon svg-icon-md">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 30 30">
                                            <path d="M15,3C8.373,3,3,8.373,3,15c0,6.627,5.373,12,12,12s12-5.373,12-12C27,8.373,21.627,3,15,3z M16.414,15 c0,0,3.139,3.139,3.293,3.293c0.391,0.391,0.391,1.024,0,1.414c-0.391,0.391-1.024,0.391-1.414,0C18.139,19.554,15,16.414,15,16.414 s-3.139,3.139-3.293,3.293c-0.391,0.391-1.024,0.391-1.414,0c-0.391-0.391-0.391-1.024,0-1.414C10.446,18.139,13.586,15,13.586,15 s-3.139-3.139-3.293-3.293c-0.391-0.391-0.391-1.024,0-1.414c0.391-0.391,1.024-0.391,1.414,0C11.861,10.446,15,13.586,15,13.586 s3.139-3.139,3.293-3.293c0.391-0.391,1.024-0.391,1.414,0c0.391,0.391,0.391,1.024,0,1.414C19.554,11.861,16.414,15,16.414,15z"></path>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>Cancel</a>
                                </div>
                            </div>

                            <table class="table table-hover table-checkable pr-4" id="TransferBintb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Brand</th>
                                        <th class="text-dark">Artikel</th>
                                        <th class="text-dark">[Size] [Qty]</th>
                                        <th class="text-dark">Transfer</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
                <div class="col-lg-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <div class="row">
                                <div class="form-group mb-1 pb-1 col-4">
                                    <a class="btn btn-primary col-12" id="stf_code" style="white-space:nowrap;"></a>
                                </div>
                                <div class="form-group mb-1 pb-1 col-6 row ml-auto">
                                    <a class="btn btn-danger col-4" id="transfer_cancel_btn" style="white-space:nowrap;">DELETE</a>
                                    <a class="btn btn-warning col-4" id="transfer_draft_btn" style="white-space:nowrap;">DRAFT</a>
                                    <a class="btn btn-success col-4" id="transfer_done_btn" style="white-space:nowrap;">DONE</a>
                                </div>
                            </div>
                            <!--end: Datatable-->
                            <div class="row col-12">
                            <input type="search" class="form-control form-control-sm col-12" id="in_transfer_search" placeholder="Cari artikel / brand / warna"/>
                            </div>
                            <table class="table table-hover table-checkable pr-4" id="InTransferBintb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">Artikel</th>
                                        <th class="text-dark">BIN</th>
                                        <th class="text-dark">Qty</th>
                                        <th class="text-dark">Asal</th>
                                        <th class="text-dark">Tujuan</th>
                                        <th class="text-dark"></th>
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

            <div class="row">
                <div class="col-lg-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <!--begin::Header-->
                        <div class="card-header h-auto">
                            <!--begin::Title-->
                            <div class="card-title py-5">
                                <h3 class="card-label">History Transfer</h3>
                            </div>
                            <!--end::Title-->
                        </div>
                        <!--end::Header-->
                        <div class="card-body table-responsive">
                            <div class="row d-flex justify-content-between">
                            <select class="form-control col-5" id="status_filter" name="status_filter" required>
                                <option value="">- Status -</option>
                                <option value="1">In Progress</option>
                                <option value="2">Done</option>
                                <option value="3">Draft</option>
                            </select>
                            <input type="search" class="form-control form-control-sm col-5" id="history_search" placeholder="Cari user / artikel"/>
                            </div>
                            <table class="table table-hover table-checkable" id="TransferHistorytb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Kode</th>
                                        <th class="text-dark">User</th>
                                        <th class="text-dark" style="white-space: nowrap;">Qty</th>
                                        <th class="text-dark" style="white-space: nowrap;">Store Awal</th>
                                        <th class="text-dark" style="white-space: nowrap;">Store Tujuan</th>
                                        <th class="text-dark">Penerima</th>
                                        <th class="text-dark" style="white-space: nowrap;">Tanggal</th>
                                        <th class="text-dark">Status</th>
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
<?php echo $__env->make('app.stock_transfer.stock_transfer_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app.stock_transfer.stock_transfer_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.structure', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/stock_transfer/stock_transfer.blade.php ENDPATH**/ ?>