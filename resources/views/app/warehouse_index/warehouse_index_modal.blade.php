<!-- Modal-->
<div class="modal fade" id="WarehouseIndexModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_warehouse_index">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Jenis Akun</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kode Warehouse<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="w_code" name="w_code" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="st_id">Store<span class="text-danger">*</span></label>
                        <select class="form-control" id="st_id" name="st_id" required>
                            <option value="">Pilih Store</option>
                            @foreach($data['stores'] as $store)
                                <option value="{{ $store->id }}">{{ $store->st_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_warehouse_index_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_warehouse_index_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->