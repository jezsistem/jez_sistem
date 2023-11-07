<!-- Modal-->
<div class="modal fade" id="WebCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_web_config" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel"><span id="config_name_label"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama</label>
                        <input type="text" class="form-control" id="config_name" name="config_name" readonly/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nilai</label>
                        <input type="text" class="form-control" id="config_value" name="config_value" required />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <!-- <button type="button" class="btn btn-danger font-weight-bold" id="delete_web_config_btn" style="display:none;">Hapus</button> -->
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_web_config_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->