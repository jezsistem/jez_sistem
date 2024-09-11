<!-- Modal-->
<div class="modal fade" id="WebConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_wbc">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Konfirmasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Bukti Transfer</label>
                        <img id="imgPreview" style="width:100%;"/>
                    </div>
                    <input type="hidden" id="pos_invoice" name="pos_invoice" value=""/>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Status Konfirmasi</label>
                        <select class="form-control" id="cf_status" name="cf_status" required>
                            <option value="">Pilih</option>
                            <option value="1">Terima</option>
                            <option value="2">Tolak</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_wbc_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_wbc_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->
<?php /**PATH /home/jezpro.id/public_html/resources/views/app/web_confirmation/web_confirmation_modal.blade.php ENDPATH**/ ?>