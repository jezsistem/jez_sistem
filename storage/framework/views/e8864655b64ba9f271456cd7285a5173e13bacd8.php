<!-- Modal-->
<div class="modal fade" id="TargetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_target">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Target Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Periode</label>
                        <input type="month" class="form-control" id="tr_date" name="tr_date" required/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_target_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_target_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="SubTargetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Data Target</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="target_date" value=""/>
                <input type="hidden" id="tr_id" value=""/>
                <a style="margin-bottom:10px;" class="btn btn-sm btn-light-primary font-weight-bolder float-right mr-2" id="ImportModalBtn" aria-haspopup="true" aria-expanded="false">Import Data</a>
                <a style="margin-bottom:10px;" class="btn btn-sm btn-primary float-right mr-2" id="add_target_detail_btn">Tambah Data</a>
                <table class="table table-hover table-checkable" id="TargetDetailtb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Store</th>
                            <th class="text-dark">Divisi</th>
                            <th class="text-dark">Target</th>
                            <th class="text-dark">Tercapai</th>
                            <th class="text-dark">%</th>
                            <th class="text-dark" style="white-space: nowrap;">[Penjualan] [Item Terjual]</th>
                            <th class="text-dark"></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="SubSubTargetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Data Target</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Store</label>
                        <select class="form-control" id="st_id" name="st_id" required>
                            <option value="">- Pilih Store -</option>
                            <?php $__currentLoopData = $data['st_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Divisi</label>
                        <select class="form-control" id="stt_id" name="stt_id" required>
                            <option value="">- Pilih Divisi -</option>
                            <?php $__currentLoopData = $data['stt_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <div id="target_amount"> </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-dark font-weight-bold" id="save_target_detail_btn">Simpan</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<form id="f_import" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Import Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Store</label>
                            <select class="form-control" id="import_st_id" name="import_st_id" required>
                                <option value="">- Pilih Store -</option>
                                <?php $__currentLoopData = $data['st_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Divisi</label>
                            <select class="form-control" id="import_stt_id" name="import_stt_id" required>
                                <option value="">- Pilih Divisi -</option>
                                <?php $__currentLoopData = $data['stt_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pilih template yang sudah diisi data</label>
                            <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="importFile" id="importFile" accept=".csv" required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /Modal --><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/target/target_modal.blade.php ENDPATH**/ ?>