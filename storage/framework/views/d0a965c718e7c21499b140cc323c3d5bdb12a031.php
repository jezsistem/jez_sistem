<!-- Modal-->
<div class="modal fade" id="BrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_bank" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <input type="hidden" name="_image" id="_image" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Akun Bank</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama Bank (jgn ubah yang sudah ada, kecuali buat baru)</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama Penerima</label>
                        <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nomor Rekening</label>
                        <input type="text" class="form-control" id="bank_number" name="bank_number"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Logo Bank</label>
                        <input class="form-control"  type="file" name="bank_image" id="bank_image" accept="image/*" onchange="loadFile(event)">
                        <center><img id="imagePreview" style="width:40%; padding-top:10px;"/></center>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_bank_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_bank_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/bank/bank_modal.blade.php ENDPATH**/ ?>