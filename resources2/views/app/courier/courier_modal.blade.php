<!-- Modal-->
<div class="modal fade" id="CourierModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_courier">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <input type="hidden" name="_image" id="_image" value="" />
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Kurir Pengiriman</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kurir *</label>
                        <input type="text" class="form-control" id="cr_name" name="cr_name" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Deskripsi</label>
                        <input type="text" class="form-control" id="cr_description" name="cr_description"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_courier_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-primary font-weight-bold" id="save_courier_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->