<!-- Modal-->
<div class="modal fade" id="WebCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_web_category" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <input type="hidden" name="_image" id="_image" value="" />
            <input type="hidden" name="_banner" id="_banner" value="" />
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Gambar</label>
                        <input type="file" class="form-control" id="cs_image" name="cs_image" accept="image/*" onchange="loadFile(event)" />
                        <center><img id="imagePreview" style="width:40%; padding-top:10px;"/></center>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Banner</label>
                        <input type="file" class="form-control" id="cs_banner" name="cs_banner" accept="image/*" onchange="loadBanner(event)" />
                        <center><img id="bannerPreview" style="width:40%; padding-top:10px;"/></center>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Sub Kategori</label>
                        <select class="form-control" id="psc_id" name="psc_id" required>
                            <option value="">- Sub Kategori -</option>
                            @foreach ($data['psc_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Sub Sub Kategori</label>
                        <input type="text" class="form-control" id="cs_sub_category" name="cs_sub_category"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Judul Kategori</label>
                        <input type="text" class="form-control" id="cs_title" name="cs_title" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Slug</label>
                        <input type="text" class="form-control" id="cs_slug" name="cs_slug" required />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_web_category_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-primary font-weight-bold" id="save_web_category_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->