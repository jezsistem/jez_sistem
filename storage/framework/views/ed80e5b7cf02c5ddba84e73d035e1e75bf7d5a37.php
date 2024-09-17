<!-- Modal-->
<div class="modal fade" id="WbModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_wb" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <input type="hidden" name="_image" id="_image" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Banner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Judul</label>
                        <input type="text" class="form-control" id="bn_name" name="bn_name" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Slug</label>
                        <input type="text" class="form-control" id="bn_slug" name="bn_slug" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Gambar</label>
                        <input class="form-control"  type="file" name="bn_image" id="bn_image" accept="image/*" onchange="loadFile(event)">
                        <center><img id="imagePreview" style="width:40%; padding-top:10px;"/></center>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Is Child</label>
                        <select class="form-control" id="is_child" name="is_child" required>
                            <option value="">- Pilih -</option>
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Urutan</label>
                        <input type="number" class="form-control" id="bn_sort" name="bn_sort" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Filter</label>
                        <select class="form-control" id="bn_filter" name="bn_filter" required>
                            <option value="">- Pilih -</option>
                            <option value="0">Terbaru</option>
                            <option value="1">Terlaris</option>
                            <option value="2">Termurah</option>
                            <option value="3">Termahal</option>
                            <option value="4">Brand Lokal</option>
                            <option value="5">Topdeals</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_wb_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_wb_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="BrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="card-header flex-wrap py-3">
                <div class="card-title">
                    <h3 class="card-label">Brand</h3>
                </div>
                <div class="card-toolbar">
                    <!--begin::Button-->
                    <a href="#" class="btn btn-dark font-weight-bolder" id="add_brand_btn">
                    <span class="svg-icon svg-icon-md">
                        <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24" />
                                <circle fill="#000000" cx="9" cy="15" r="6" />
                                <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                            </g>
                        </svg>
                        <!--end::Svg Icon-->
                    </span>Data Baru</a>
                    <!--end::Button-->
                </div>
            </div>
            <div class="card-body table-responsive">
                <!--begin: Datatable-->
                <input type="hidden" id="bn_id" value=""/>
                <input type="search" class="form-control  col-6" id="brand_search" placeholder="Cari brand"/><br/>
                <table class="table table-hover table-checkable" id="Brandtb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">SubSub Kategori</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="BrandEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_brand" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_bb_id" id="_bb_id" value="" />
            <input type="hidden" name="_bb_mode" id="_bb_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Brand</label>
                        <select class="form-control" id="br_id" name="br_id" required>
                            <option value="">- Brand -</option>
                            <?php $__currentLoopData = $data['br_id']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>"><?php echo e($value); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_brand_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_brand_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="card-header flex-wrap py-3">
                <div class="card-title">
                    <h3 class="card-label">SubSub Kategori</h3>
                </div>
                <div class="card-toolbar">
                    <!--begin::Button-->
                    <a href="#" class="btn btn-dark font-weight-bolder" id="add_article_btn">
                    <span class="svg-icon svg-icon-md">
                        <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24" />
                                <circle fill="#000000" cx="9" cy="15" r="6" />
                                <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                            </g>
                        </svg>
                        <!--end::Svg Icon-->
                    </span>Data Baru</a>
                    <!--end::Button-->
                </div>
            </div>
            <div class="card-body table-responsive">
                <!--begin: Datatable-->
                <input type="hidden" id="bnb_id" value=""/>
                <input type="hidden" id="br_id" value=""/>
                <input type="search" class="form-control  col-6" id="article_search" placeholder="Cari article"/><br/>
                <table class="table table-hover table-checkable" id="Articletb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Nama</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <!--end: Datatable-->
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ArticleEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_article" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_bbd_id" id="_bbd_id" value="" />
            <input type="hidden" name="_bbd_mode" id="_bbd_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Article</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div id="article_reload"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_article_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_article_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/web_banner/web_banner_modal.blade.php ENDPATH**/ ?>