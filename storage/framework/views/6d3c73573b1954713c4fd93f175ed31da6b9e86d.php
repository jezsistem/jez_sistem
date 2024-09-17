<!-- Modal-->
<div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_import" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Import Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Pilih template yang sudah diisi dari hasil export template
                                <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="template" id="template" required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- Modal-->

<div class="modal fade" id="ImportScanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_import" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Import Data Scan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <!--begin::List Widget 9-->
                            <!--begin::Header-->
                            <h3 class="card-title align-items-start flex-column">
                                <span class="font-weight-bolder btn-sm btn-primary text-white">Filter Template</span>
                            </h3>
                            <select class="form-control bg-primary text-white" id="st_filter">
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
                            <div id="bin_panel"></div>
                            <br/>
                            <div class="row" id="bin_filter_panel">

                            </div>
                            <!--end::Timeline-->
                            <!--end: Card Body-->

                            <!--end: List Widget 9-->
                            <label>Masukkan file csv data scan
                                <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="template" id="template" required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal Filter tanggal-->
<div class="modal fade" id="TanggalModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_import" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Filter Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Pilih range tanggal
                                <span class="text-danger">*</span></label>
                            <input value="" name="tanggal" id="tanggalrange" type="text" class="form-control"
                                   placeholder="Periode Tanggal">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Tampilkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Export-->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_export" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">
                        <Export></Export>
                        Data
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Pilih Range Tanggal
                                <span class="text-danger">*</span></label>
                            
                            
                            <input value="" name="tanggal" id="tanggalrange" type="text" class="form-control"
                                   placeholder="Periode Tanggal">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\jez\jez_sistem\resources\views/app/mass_adjustment/mass_adjustment_modal.blade.php ENDPATH**/ ?>