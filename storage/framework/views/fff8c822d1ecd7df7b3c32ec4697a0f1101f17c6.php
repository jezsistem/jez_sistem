<!-- Modal-->
<div class="modal fade" id="WaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_whatsapp" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Pesan Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Tipe</label>
                        <select class="form-control" id="wa_type" name="wa_type" required>
                            <option value="">- Pilih Tipe -</option>
                            <option value="people">Perorang</option>
                            <option value="all">Semua Customer</option>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">No WA</label>
                        <input type="text" class="form-control" id="wa_phone" name="wa_phone" />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Pesan</label>
                        <input type="text" class="form-control" id="wa_message" name="wa_message" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_whatsapp_btn">Kirim</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/whatsapp/whatsapp_modal.blade.php ENDPATH**/ ?>