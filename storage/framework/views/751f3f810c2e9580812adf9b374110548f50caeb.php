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
                <a class="btn btn-sm btn-primary" id="check_hb_hj" style="cursor:pointer;">
                    Cek HB HJ
                </a>
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
                        <div class="card-header h-auto align-items-center justify-content-between">
                            <!--begin::Title-->
                            <input type="hidden" id="sales_date" value=""/>
                            <div class="card-title py-5">
                                <select class="form-control" id="autorefresh"> 
                                    <option value="">Statik</option>
                                    <option value="auto">Autorefresh</option>
                                </select>
                            </div>
                            <div class="card-title py-5">
                                <select class="form-control" id="stt_id" name="stt_id">
                                    <option value="">- Divisi -</option>
                                    <?php $__currentLoopData = $data['stt_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="card-title py-5">
                                <select class="form-control" id="st_id_filter" name="st_id_filter" required>
                                    <option value="">- Storage -</option>
                                    <?php $__currentLoopData = $data['st_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="card-title py-5">
                                <select class="form-control" id="dp_id" name="dp_id" required>
                                    <option value="">- Status -</option>
                                    <option value="DP">DP</option>
                                </select>
                            </div>
                            <div class="alert alert-custom alert-white alert-shadow fade show gutter-b bg-primary" role="alert">
                                <a href="#" class="btn btn-date-info font-weight-bold mr-2 col-12" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Filter Tanggal" data-placement="left">
                                    <span class="text-muted font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                                    <span class="text-primary font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                                </a>
                            </div>
                            <div class="alert alert-custom alert-white alert-shadow fade show gutter-b bg-primary" role="alert">
                                <a href="#" class="btn btn-sm btn-success font-weight-bold mr-2 col-12" id="sales_summary_btn" style="color:black;">
                                    Summary Penjualan
                                </a>
                            </div>
                            <!--end::Title-->
                        </div>
                        
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <input type="search" class="form-control  col-6" id="invoice_report_search" placeholder="Cari invoice / customer / user / divisi"/><br/>
                            <a class="btn btn-primary ml-auto mr-2" data-type="invoice" id="export_btn">Export Excel</a><br/>
                            <center><h3 class="btn-sm btn-primary col-3">By Invoice</h3></center>
                            <table class="table table-hover table-checkable" id="InvoiceReporttb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Tanggal</th>
                                        <th class="text-dark">Store</th>
                                        <th class="text-dark">Invoice</th>
                                        <th class="text-dark">Customer</th>
                                        <th class="text-dark" style="white-space:nowrap;">Cross Order</th>
                                        <th class="text-dark">User</th>
                                        <th class="text-dark">Divisi</th>
                                        <th class="text-dark" style="white-space:nowrap;">Item Qty</th>
                                        <th class="text-dark" style="white-space:nowrap;">Item Value</th>
                                        <th class="text-dark">Ongkir</th>
                                        <th class="text-dark" style="white-space:nowrap;">Kode Unik</th>
                                        <th class="text-dark" style="white-space:nowrap;">Biaya Admin</th>
                                        <th class="text-dark" style="white-space:nowrap;">Diskon Penjual</th>
                                        <th class="text-dark" style="white-space:nowrap;">Biaya Lain</th>
                                        <th class="text-dark">Nameset</th>
                                        <th class="text-dark" style="white-space:nowrap;">Value - Admin</th>
                                        <th class="text-dark" style="white-space:nowrap;">Total Semua</th>
                                        <th class="text-dark" style="white-space:nowrap;">Tipe Bayar 1</th>
                                        <th class="text-dark" style="white-space:nowrap;">Jumlah Bayar 1</th>
                                        <th class="text-dark" style="white-space:nowrap;">Kartu 1</th>
                                        <th class="text-dark" style="white-space:nowrap;">Ref 1</th>
                                        <th class="text-dark" style="white-space:nowrap;">Tipe Bayar 2</th>
                                        <th class="text-dark" style="white-space:nowrap;">Jumlah Bayar 2</th>
                                        <th class="text-dark" style="white-space:nowrap;">Kartu 2</th>
                                        <th class="text-dark" style="white-space:nowrap;">Ref 2</th>
                                        <th class="text-dark" style="white-space:nowrap;">Pelunasan</th>
                                        <th class="text-dark" style="white-space:nowrap;">Tanggal Pelunasan</th>
                                        <th class="text-dark">Status</th>
                                        <th class="text-dark">Note</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <!--end: Datatable-->
                        </div>
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <input type="hidden" id="pt_id_filter" value=""/>
                            <div class="row">
                            <input type="search" class="form-control  col-6" id="article_report_search" placeholder="Cari artikel"/>
                            <a class="btn btn-primary ml-auto mr-2" data-type="article" id="export_btn">Export Excel</a>
                            <span id="pt_id_filter_label" class=""> </span><br/>
                            </div><br/>
                            <center><h3 class="btn-sm btn-primary col-3">By Artikel</h3></center>
                            <table class="table table-hover table-checkable" id="ArticleReporttb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Tanggal</th>
                                        <th class="text-dark">Invoice</th>
                                        <th class="text-dark">Brand</th>
                                        <th class="text-dark">Artikel</th>
                                        <th class="text-dark">Kategori</th>
                                        <th class="text-dark" style="white-space:nowrap;">Sub Kategori</th>
                                        <th class="text-dark" style="white-space:nowrap;">Sub Sub Kategori</th>
                                        <th class="text-dark">Warna</th>
                                        <th class="text-dark">Size</th>
                                        <th class="text-dark">Qty</th>
                                        <th class="text-dark">Bandrol</th>
                                        <th class="text-dark" style="white-space:nowrap;">Harga Jual</th>
                                        <th class="text-dark" style="white-space:nowrap;">Total Harga</th>
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
<?php echo $__env->make('app.report.sales_report.sales_report_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app.report.sales_report.sales_report_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('app.structure', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/report/sales_report/sales_report.blade.php ENDPATH**/ ?>