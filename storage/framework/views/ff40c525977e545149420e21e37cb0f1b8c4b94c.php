<!-- Modal-->
<div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <label>Download Template
                        <span class="text-danger">*</span></label>
                        <a href="<?php echo e(asset('upload/template/supplier_template.xlsx')); ?>" class="btn btn-xs btn-primary">Download</a>
                    </div>
                    <div class="form-group">
                        <label>Pilih template yang sudah di download dan diisi
                        <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="cust_template" id="cust_template" required/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="TaxModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_tax">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Pajak</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kode Pajak *</label>
                        <input type="text" class="form-control" id="tx_code" name="tx_code" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama Pajak *</label>
                        <input type="text" class="form-control" id="tx_name" name="tx_name" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Akun Pembelian *</label>
                        <select class="form-control" id="a_id_purchase" name="a_id_purchase" required>
                            <option value="">- Pilih -</option>
                            <?php $__currentLoopData = $data['a_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div id="a_id_purchase_parent"></div>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Akun Penjualan *</label>
                        <select class="form-control" id="a_id_sell" name="a_id_sell" required>
                            <option value="">- Pilih -</option>
                            <?php $__currentLoopData = $data['a_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div id="a_id_sell_parent"></div>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Presentase NPWP *</label>
                        <input type="number" class="form-control" id="tx_npwp" name="tx_npwp" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Presentase Non NPWP</label>
                        <input type="number" class="form-control" id="tx_non_npwp" name="tx_non_npwp"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_tax_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_tax_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/tax/tax_modal.blade.php ENDPATH**/ ?>