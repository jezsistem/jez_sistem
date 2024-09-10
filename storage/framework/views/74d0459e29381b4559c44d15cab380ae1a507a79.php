<!-- Modal-->
<div class="modal fade" id="ImageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="f_article">
        <?php echo csrf_field(); ?>
        <div class="modal-content">
            <div class="modal-header bg-light">
                <input type="hidden" id="img_pid" value=""/>
                <input type="hidden" id="_main_image" value=""/>
                <input type="hidden" id="_detail_image" value=""/>
                <input type="hidden" id="_chart_image" value=""/>
                <h5 class="modal-title text-dark" id="exampleModalLabel">[Gambar] <span id="image_label"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-1 pb-1">
                    <label class="text-dark" for="exampleTextarea">Gambar Utama <small>* klik gambar jika ingin menghapus</small></label>
                    <div class="col-12" style="margin-bottom:10px; border-radius:12px; -webkit-box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15); box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15);">
                        <input style="height:100px;" class="form-control" type="file" id="p_main_image" name="p_main_image"/>
                    </div>
                    <div class="col-12" style="padding:10px; background:#EFF2EB; margin-bottom:10px; border-radius:12px; -webkit-box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15); box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15);" id="p_main_image_preview"></div>
                </div>
                <div class="form-group mb-1 pb-1">
                    <label class="text-dark" for="exampleTextarea">Gambar Detail <small>* klik gambar jika ingin menghapus</small></label>
                    <div class="col-12" style="margin-bottom:10px; border-radius:12px; -webkit-box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15); box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15);">
                        <input style="height:100px;" class="form-control" type="file" id="p_image" name="p_image[]" multiple/>
                    </div>
                    <div class="col-12" style="padding:10px; background:#EFF2EB; margin-bottom:10px; border-radius:12px; -webkit-box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15); box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15);" id="p_image_preview"></div>
                </div>

                <div class="form-group mb-1 pb-1">
                    <label class="text-dark" for="exampleTextarea">Size Chart <small>* klik gambar jika ingin menghapus</small></label>
                    <div class="col-12" style="margin-bottom:10px; border-radius:12px; -webkit-box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15); box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15);">
                        <input style="height:100px;" class="form-control" type="file" id="p_size_chart" name="p_size_chart"/>
                    </div>
                    <div class="col-12" style="padding:10px; background:#EFF2EB; margin-bottom:10px; border-radius:12px; -webkit-box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15); box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15);" id="p_size_chart_preview"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_p_image_btn">Simpan</button>
            </div>
        </div>
        </form>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="DescriptionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">[Deskripsi] <span id="description_label"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-lg-12">
                        <input type="text" id="pid" value=""/>
                        <textarea class="form-control" name="p_description" id="p_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-12">
                        <label for="exampleTextarea">Script Video Embed</label>
                        <textarea class="form-control" name="p_video" id="p_video" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-dark font-weight-bold" id="save_p_description_btn">Simpan</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/web_article/web_article_modal.blade.php ENDPATH**/ ?>