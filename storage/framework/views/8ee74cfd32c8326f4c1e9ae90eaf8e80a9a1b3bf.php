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
                        <input type="file" class="form-control" name="pc_template" id="pc_template" required/>
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
<div class="modal fade" id="ProductCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_product_location">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Lokasi Simpan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kode Lokasi*</label>
                        <input type="text" class="form-control" id="pl_code" name="pl_code" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama Lokasi*</label>
                        <input type="text" class="form-control" id="pl_name" name="pl_name" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Deskripsi</label>
                        <input type="text" class="form-control" id="pl_description" name="pl_description"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Default Penerimaan</label>
                        <select class="form-control" id="pl_default" name="pl_default">
                            <option value="0" selected>Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_product_location_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_product_location_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/product_location/product_location_modal.blade.php ENDPATH**/ ?>