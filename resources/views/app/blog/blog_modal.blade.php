<!-- Modal-->
<div class="modal fade" id="BcModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_bc">
            @csrf
            <input type="hidden" name="_bc_id" id="_bc_id" value="" />
            <input type="hidden" name="_bc_mode" id="_bc_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Kategori Blog</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kategori Blog</label>
                        <input type="text" class="form-control" id="bc_name" name="bc_name" required/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_bc_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="BccModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="f_bcc">
            @csrf
            <input type="hidden" name="_bcc_id" id="_bcc_id" value="" />
            <input type="hidden" name="_bcc_mode" id="_bcc_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Blog Konten</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kategori</label>
                        <select class="form-control" name="bc_id" id="bc_id" required>
                            <option value="">- Pilih Kategori Blog -</option>
                            @foreach ($data['bc_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Judul</label>
                        <input type="text" class="form-control" id="bcc_title" name="bcc_title" required/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Gambar Cover</label>
                        <input type="hidden" id="_bcc_image" value=""/>
                        <input type="file" class="form-control" id="bcc_image" name="bcc_image"/>
                        <div id="bcc_image_preview"></div>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Konten</label>
                        <textarea class="form-control" name="bcc_content" id="bcc_content" rows="30"></textarea>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kata Kunci</label>
                        <input type="text" class="form-control" id="bcc_keywords" name="bcc_keywords" required/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_bcc_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_bcc_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->