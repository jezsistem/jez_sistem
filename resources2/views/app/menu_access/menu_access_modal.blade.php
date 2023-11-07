<!-- Modal-->
<div class="modal fade" id="MMModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_menu_access">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">{{ $data['subtitle'] }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">{{ $data['subtitle'] }} *</label>
                        <input type="text" class="form-control" id="ma_title" name="ma_title" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Main Menu *</label>
                        <select class="form-control" id="mt_id" name="mt_id" required>
                            <option value="">- Pilih -</option>
                            @foreach ($data['mt_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Slug Menu *</label>
                        <input type="text" class="form-control" id="ma_slug" name="ma_slug" required/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Urutan Menu *</label>
                        <input type="number" class="form-control" id="ma_sort" name="ma_sort" required/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_menu_access_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-primary font-weight-bold" id="save_menu_access_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->
