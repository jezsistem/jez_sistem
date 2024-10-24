<!-- Modal-->
<div class="modal fade" id="WebSubCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_wsc" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <input type="hidden" name="_banner" id="_banner" value="" />
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Slug Sub Kategori</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Sub Kategori</label>
                        <input type="text" class="form-control" id="psc_name" name="psc_name" readonly/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Banner</label>
                        <input type="file" class="form-control" id="psc_banner" name="psc_banner" accept="image/*" onchange="loadBanner(event)" />
                        <center><img id="bannerPreview" style="width:40%; padding-top:10px;"/></center>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Slug</label>
                        <input type="text" class="form-control" id="psc_slug" name="psc_slug" required />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary font-weight-bold" id="save_wsc_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->