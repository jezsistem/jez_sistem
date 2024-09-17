<!-- Modal-->
<div class="modal fade" id="AdjustmentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="f_adjustment">
            <input type="hidden" id="_pl_id" value=""/>
            <?php echo csrf_field(); ?>
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Adjustment [<span id="adjustment_label"></span>]</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-header flex-wrap py-3">
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
                        </span>Tambah Artikel</a>
                        <!--end::Button-->
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <input type="search" class="form-control  col-6" id="article_search" placeholder="Cari nama artikel / warna / brand"/>
                    <table class="table table-hover table-checkable" id="Articletb">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">No</th>
                                <th class="text-dark">Article</th>
                                <th class="text-dark">Size Stock Barcode</th>
                                <th class="text-dark" style="white-space: nowrap;">Qty SO</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_adjustment_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="AddArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="f_article">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="pst_id_hidden" id="pst_id_hidden" value=""/>
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Tambah Artikel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-1 pb-1">
                    <label for="exampleTextarea">Artikel <span class="text-danger">*</span></label>
                    <input type="text" class="form-control border-dark col-12" id="product_name_input" placeholder="nama size warna brand">
                    <div id="itemList"></div>
                </div>
                <div class="form-group mb-1 pb-1">
                    <label for="exampleTextarea">Qty <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="pls_qty" name="pls_qty" required />
                </div>
                <div class="form-group mb-1 pb-1">
                    <label for="exampleTextarea">Note <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="article_note" name="article_note" required />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_add_article_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH C:\laragon\www\jez_sistem\resources\views/app/adjustment/adjustment_modal.blade.php ENDPATH**/ ?>