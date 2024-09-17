<!-- Modal-->
<div class="modal fade" id="WebCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_web_category" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <input type="hidden" name="_image" id="_image" value="" />
            <input type="hidden" name="_banner" id="_banner" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Kategori</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Judul Kategori</label>
                        <input type="text" class="form-control" id="cs_title" name="cs_title" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Slug</label>
                        <input type="text" class="form-control" id="cs_slug" name="cs_slug" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Sub Kategori</label>
                        <select class="form-control" id="psc_id" name="psc_id" required>
                            <option value="">- Sub Kategori -</option>
                            <?php $__currentLoopData = $data['psc_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Sub Sub Kategori</label>
                        <input type="text" class="form-control" id="cs_sub_category" name="cs_sub_category"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Gambar</label>
                        <small style="color: grey;">300x300px</small>
                        <input type="file" class="form-control" id="cs_image" name="cs_image" accept="image/*" onchange="loadFile(event)" />
                        <center><img id="imagePreview" style="width:40%; padding-top:10px;"/></center>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Banner</label>
                        <small style="color: grey;">2000x960px</small>
                        <input type="file" class="form-control" id="cs_banner" name="cs_banner" accept="image/*" onchange="loadBanner(event)" />
                        <center><img id="bannerPreview" style="width:40%; padding-top:10px;"/></center>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_web_category_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_web_category_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH C:\laragon\www\JEZ_S7\JEZ sistem\jez_sistem\resources\views/app/web_category/web_category_modal.blade.php ENDPATH**/ ?>