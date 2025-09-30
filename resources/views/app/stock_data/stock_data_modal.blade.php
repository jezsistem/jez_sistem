<!-- Modal-->
<div class="modal fade" id="ProductCategoryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_product_category">
            @csrf
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
                                <th class="text-dark">Storage Area</th>
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
<div class="modal fade" id="WaitingListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="width:100%; max-width:1400px;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Waiting Offline List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <!--begin: Datatable-->
                    <input type="search" class="form-control bg-light-primary" id="waiting_data_search" placeholder="Cari artikel"/><br/>
                    <table class="table table-hover table-checkable" id="WaitingListtb">
                        <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">Artikel ID</th>
                            <th class="text-dark">Artikel</th>
                            <th class="text-dark">BIN</th>
                            <th class="text-dark">Tanggal</th>
                            <th class="text-dark">User</th>
                            <th class="text-dark">Status</th>
                            <th class="text-dark">Action</th>
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
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="ChangeDisplayModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_ganti_display">
            @csrf
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Ganti Display</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div id="reader_change_display" class="rounded" style="max-width: 400px;"></div>
                    <div id="result"></div>
                    <div class="form-group mt-3 mb-1 pb-1">
                        <label for="sku">SKU*</label>
                        <input type="text" class="form-control" id="sku_display" name="sku" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="nama_produk_display">Nama Produk</label>
                        <p class="form-control-plaintext" id="product_name">-</p>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="ukuran_display">Variant</label>
                        <p class="form-control-plaintext" id="product_variant">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary font-weight-bold">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Filter List Modal -->
<div class="modal fade" id="FilterListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="width:100%; max-width:1400px;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Filter List Product</h5>
                <button type="button" class="c  lose" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <!--begin: Datatable-->
                    <input type="search" class="form-control bg-light-primary" id="waiting_data_search" placeholder="Cari artikel"/><br/>
                    <table class="table table-hover table-checkable" id="FilterListtb">
                        <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">Artikel ID</th>
                            <th class="text-dark">SKU</th>
                            <th class="text-dark">SKU</th>
                            <th class="text-dark">Size</th>
                            <th class="text-dark">HB / HJ</th>
                            <th class="text-dark">Area</th>
                            <th class="text-dark">QTY</th>
                            <th class="text-dark">Action</th>
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