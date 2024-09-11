<?php $__env->startSection('content'); ?>
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5 fs-3"><?php echo e($data['subtitle']); ?></h5>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-xxl-12">
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3 mt-4">
                            <form class="row col-sm-12 col-12 col-md-12 col-lg-12 col-xl-12 px-0" id="f_filter">
                            <div class="form-group col-sm-12 col-12 col-md-12 col-lg-6 col-xl-3">
                                <label class="form-label" for="exampleTextarea">STORE</label>
                                <div class="select-card-lg">
                                    <select class="form-control bg-light-primary" id="st_id" name="st_id" required>
                                        <option value="">-</option>
                                        <?php $__currentLoopData = $data['st_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 col-12 col-md-12 col-lg-6 col-xl-2 pr-0">
                                <label class="form-label" for="exampleTextarea">TIPE DATA</label>
                                <div class="select-card-lg">
                                    <select class="form-control bg-light-primary" id="data_filter" name="data_filter" required>
                                        <option value="">-</option>
                                        <option value="brand">Brand</option>
                                        <option value="article">Artikel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 col-12 col-md-12 col-lg-6 col-xl-2">
                                <label class="form-label" for="exampleTextarea">DATA ARTIKEL</label>
                                <div class="select-card-lg">
                                    <select class="form-control bg-light-primary" id="article_filter" name="article_filter">
                                        <option value="">-</option> 
                                        <option value="color">Warna</option>
                                        <option value="size">Size</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-12 col-12 col-md-12 col-lg-6 offset-xl-2 col-xl-3 pr-0 text-right">
                                <!-- <label class="form-label"  for="exampleTextarea">RANGE TANGGAL</label><br/> -->
                                <a class="btn btn-date-info font-weight-bold" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Select dashboard daterange" data-placement="left" style="cursor:pointer;">
                                        <span class="font-size-base" id="kt_dashboard_daterangepicker_title">Today</span>
                                        <span class="font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
                                        <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                                        </svg>
                                </a>
                            </div>
                            <div class="card-footer w-100 px-0 pt-6 pb-4">
                                <div class="col-sm-12 col-12 col-md-12 col-lg-6 col-xl-6 offset-xl-6 text-right pr-0">
                                    <button type="button" class="btn btn-light-primary font-weight-bold mr-2" id="export_btn">Export</button>
                                    <button type="submit" class="btn btn-dark font-weight-bold">Tampilkan</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <div class="card card-custom gutter-b">
                        <div class="card-body table-responsive">
                            <div id="table"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $__env->make('app._partials.js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app.asset_detail.asset_detail_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('app.structure', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/jezpro.id/public_html/resources/views/app/asset_detail/asset_detail.blade.php ENDPATH**/ ?>