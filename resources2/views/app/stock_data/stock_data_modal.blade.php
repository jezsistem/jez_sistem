<!-- Modal-->
<div class="modal fade" id="ProductCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_product_category">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Kategori Produk</h5>
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
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Pickup List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <!--begin: Datatable-->
                    <input type="search" class="form-control bg-light-primary" id="pick_data_search" placeholder="Cari artikel"/><br/>
                    <table class="table table-hover table-checkable" id="PickupListtb">
                        <thead class="bg-primary text-light">
                            <tr>
                                <th class="text-light">Artikel</th>
                                <th class="text-light">BIN</th>
                                <th class="text-light">Tanggal</th>
                                <th class="text-light">User</th>
                                <th class="text-light">Status</th>
                                <th class="text-light"></th>
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
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Aging</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <!--begin: Datatable-->
                    <input type="search" class="form-control form-control-sm col-6" id="aging_search" placeholder="Cari brand nama warna size" style="border:1px solid black; padding:20px;"/><br/>
                    <table class="table table-hover table-checkable" id="Agingtb">
                        <thead class="bg-primary text-light">
                            <tr>
                                <th class="text-light">No</th>
                                <th class="text-light">Store</th>
                                <th class="text-light" style="white-space:nowrap;">Aging (PO)</th>
                                <th class="text-light" style="white-space:nowrap;">Aging (TF/ADJ)</th>
                                <th class="text-light">Kategori</th>
                                <th class="text-light">SubKategori</th>
                                <th class="text-light">SubSubKategori</th>
                                <th class="text-light">Brand</th>
                                <th class="text-light">Artikel</th>
                                <th class="text-light">Warna</th>
                                <th class="text-light">Ukuran</th>
                                <th class="text-light" style="white-space:nowrap;">Total Stok</th>
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