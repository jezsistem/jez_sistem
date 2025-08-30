<!-- Modal-->
<div class="modal fade" id="UserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_user">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Data User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Store <span class="text-danger">*</span></label>
                        <select class="form-control" id="st_id" name="st_id" required>
                            <option value="">- Pilih -</option>
                            @foreach ($data['st_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="st_id_parent"></div>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="u_name" name="u_name" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">No Telp <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="u_phone" name="u_phone" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kode <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="isi jika sales offline, bisa huruf dan angka" id="u_secret_code" name="u_secret_code"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="u_email" name="u_email" required/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Password</label>
                        <input type="password" class="form-control" id="u_password" name="u_password"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">NIP</label>
                        <input type="text" class="form-control" id="u_nip" name="u_nip" />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">KTP</label>
                        <input type="number" class="form-control" id="u_ktp" name="u_ktp" />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Alamat</label>
                        <input type="text" class="form-control" id="u_address" name="u_address"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_user_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_user_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->