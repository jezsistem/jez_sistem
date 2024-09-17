<!-- Modal-->
<div class="modal fade" id="ProductCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_product_category">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Kategori Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kategori Produk*</label>
                        <input type="text" class="form-control" id="pc_name" name="pc_name" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Deskripsi</label>
                        <input type="text" class="form-control" id="pc_description" name="pc_description"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="PickupListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="width:100%; max-width:1400px;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Pickup List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <!--begin: Datatable-->
                    <input type="search" class="form-control bg-light-primary" id="pick_data_search" placeholder="Cari artikel"/><br/>
                    <table class="table table-hover table-checkable" id="PickupListtb">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">Artikel</th>
                                <th class="text-dark">BIN</th>
                                <th class="text-dark">Tanggal</th>
                                <th class="text-dark">User</th>
                                <th class="text-dark">Status</th>
                                <th class="text-dark"></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <!--end: Datatable-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="AgingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Aging</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <!--begin: Datatable-->
                    <input type="search" class="form-control  col-6" id="aging_search" placeholder="Cari brand nama warna size" style="border:1px solid black; padding:20px;"/><br/>
                    <table class="table table-hover table-checkable" id="Agingtb">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">No</th>
                                <th class="text-dark">Store</th>
                                <th class="text-dark" style="white-space:nowrap;">Aging (PO)</th>
                                <th class="text-dark" style="white-space:nowrap;">Aging (TF/ADJ)</th>
                                <th class="text-dark">Kategori</th>
                                <th class="text-dark">SubKategori</th>
                                <th class="text-dark">SubSubKategori</th>
                                <th class="text-dark">Brand</th>
                                <th class="text-dark">Artikel</th>
                                <th class="text-dark">Warna</th>
                                <th class="text-dark">Ukuran</th>
                                <th class="text-dark" style="white-space:nowrap;">Total Stok</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <!--end: Datatable-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal --><?php /**PATH /home/jezpro.id/public_html/resources/views/app/stock_data/stock_data_modal.blade.php ENDPATH**/ ?>