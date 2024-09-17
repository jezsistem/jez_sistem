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
                        <a href="<?php echo e(asset('upload/template/data_supplier_template.xlsx')); ?>" class="btn btn-xs btn-primary">Download</a>
                    </div>
                    <div class="form-group">
                        <label>Pilih template yang sudah di download dan diisi
                        <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="ps_template" id="ps_template" required/>
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
<div class="modal fade" id="ProductSupplierModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_product_supplier">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Supplier</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama Supplier <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ps_name" name="ps_name" autocomplete="off" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">PKP</label>
                        <select class="form-control" id="ps_pkp" name="ps_pkp">
                            <option value="0" selected>Tidak</option>
                            <option value="1" selected>Ya</option>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Tempo</label>
                        <input type="number" class="form-control" id="ps_due_day" autocomplete="off" name="ps_due_day"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Email</label>
                        <input type="email" class="form-control" id="ps_email" autocomplete="off" name="ps_email" />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">No Telp</label>
                        <input type="text" class="form-control" id="ps_phone" autocomplete="off" name="ps_phone"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Alamat <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="ps_address" autocomplete="off" name="ps_address" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Rekening</label>
                        <input type="text" class="form-control" id="ps_rekening" autocomplete="off" name="ps_rekening"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">NPWP</label>
                        <input type="text" class="form-control" id="ps_npwp" autocomplete="off" name="ps_npwp"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Deskripsi</label>
                        <input type="text" class="form-control" id="ps_description" autocomplete="off" name="ps_description"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_product_supplier_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_product_supplier_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/product_supplier/product_supplier_modal.blade.php ENDPATH**/ ?>