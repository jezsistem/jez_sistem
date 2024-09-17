<!-- Modal-->
<div class="modal fade" id="VoucherModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_voucher">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <input type="hidden" name="vc_pst_id" id="vc_pst_id" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Voucher</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1" id="vc_code_input">
                        <label for="exampleTextarea">Kode *</label>
                        <input type="text" class="form-control" id="vc_code" name="vc_code" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Discount *</label>
                        <input type="number" class="form-control" id="vc_discount" name="vc_discount" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Min Order *</label>
                        <input type="number" class="form-control" id="vc_min_order" name="vc_min_order" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Tipe *</label>
                        <select class="form-control" id="vc_type" name="vc_type" required>
                            <option value="">- Pilih -</option>
                            <option value="amount">Nominal</option>
                            <option value="percent">Persen</option>
                            <option value="gift">Gift</option>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Dapat digunakan berkali - kali *</label>
                        <select class="form-control" id="vc_reuse" name="vc_reuse" required>
                            <option value="">- Pilih -</option>
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                            <option value="2">1 Bulan Sekali</option>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Status *</label>
                        <select class="form-control" id="vc_status" name="vc_status" required>
                            <option value="">- Pilih -</option>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Apakah Cashback ? *</label>
                        <select class="form-control" id="vc_cashback" name="vc_cashback" required>
                            <option value="">- Pilih -</option>
                            <option value="0">Tidak</option>
                            <option value="1">Ya</option>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Platform *</label>
                        <select class="form-control" id="vc_platform" name="vc_platform" required>
                            <option value="">- Pilih -</option>
                            <option value="web">Website</option>
                            <option value="all">Semua</option>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Batas Berakhir *</label>
                        <input type="date" class="form-control" id="vc_due_date" name="vc_due_date" required />
                    </div>

                    <div class="form-group mb-1 pb-1 d-none" id="random_input">
                        <label for="exampleTextarea">Buat Banyak dan Random ?</label>
                        <input type="checkbox" id="is_random"/>
                        <input type="number" class="form-control d-none" id="vc_qty" name="vc_qty" placeholder="berapa banyak ?"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_voucher_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_voucher_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/voucher/voucher_modal.blade.php ENDPATH**/ ?>