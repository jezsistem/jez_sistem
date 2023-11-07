<!-- Modal-->
<div class="modal fade" id="RSModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_data">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">{{ $data['subtitle'] }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama *</label>
                        <input type="text" class="form-control" id="cust_name" name="cust_name" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">No Telp *</label>
                        <input type="number" class="form-control" id="cust_phone" name="cust_phone" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Email</label>
                        <input type="email" class="form-control" id="cust_email" name="cust_email"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Username</label>
                        <input type="text" class="form-control" id="cust_username" name="cust_username"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Password</label>
                        <input type="password" class="form-control" id="cust_password" name="cust_password"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Level *</label>
                        <select class="form-control" id="rl_id" name="rl_id">
                            <option value="">- Pilih -</option>
                            @foreach ($data['level'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Additional Discount *</label>
                        <select class="form-control" id="rad_id" name="rad_id">
                            <option value="">- Pilih -</option>
                            @foreach ($data['add_disc'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Fitur Reseller *</label>
                        <select class="form-control" id="cust_token_active" name="cust_token_active" required>
                            <option value="0">Nonaktif</option>
                            <option value="1">Aktif</option>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Sudah Pernah Belanja ?</label>
                        <input type="number" class="form-control" id="cust_total" name="cust_total"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Provinsi *</label>
                        <select class="form-control" id="cust_province" name="cust_province" required>
                            <option value="">- Pilih -</option>
                            @foreach ($data['cust_province'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="cust_province_parent"></div>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kota *</label>
                        <select class="form-control" id="cust_city" name="cust_city" required>
                            <option value="">- Pilih -</option>
                        </select>
                        <div id="cust_city_parent"></div>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kecamatan *</label>
                        <select class="form-control" id="cust_subdistrict" name="cust_subdistrict" required>
                            <option value="">- Pilih -</option>
                        </select>
                        <div id="cust_subdistrict_parent"></div>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Alamat</label>
                        <input type="text" class="form-control" id="cust_address" name="cust_address" required/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->
