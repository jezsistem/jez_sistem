<!-- Modal-->
<div class="modal fade" id="PurchaseOrderModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document" style="width:100%; max-width:1300px;">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Purchase Order (PO) #<span id="po_invoice_label"></span></h5>
            </div>
            <form id="f_po">
            <input type="hidden" id="_mode" name="_mode"/>
            <input type="hidden" id="_po_id" name="_po_id"/>
            <div class="modal-body">
                <!--begin::Row-->
                <div class="row">
                    <div class="col-4">
                        <label>Store</label>
                        <select class="form-control" id="st_id" name="st_id" required>
                            <option value="">- Pilih Store -</option>
                            @foreach ($data['st_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="st_id_parent"></div>
                    </div>
                    <div class="col-4">
                        <label>Supplier</label>
                        <select class="form-control" id="ps_id" name="ps_id" required>
                            <option value="">- Pilih Supplier -</option>
                            @foreach ($data['ps_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="ps_id_parent"></div>
                    </div>
                    <div class="col-4">
                        <label>Deskripsi / Catatan</label>
                        <textarea class="form-control" placeholder="Deskripsi / Catatan" name="po_description" id="po_description" rows="3"></textarea>
                    </div>
                    <div class="col-4">
                        <label>Tipe Stok</label>
                        <select class="form-control" id="stkt_id" name="stkt_id" required>
                            <option value="">- Pilih Tipe Stok -</option>
                            @foreach ($data['stkt_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="stkt_id_parent"></div>
                    </div>
                </div>
                <!--end::Row-->
                <!--begin::Row-->
                <div class="row mt-4">
                    <!--begin::Button-->
                    <div class="col-2 mb-2 mt-4">
                    <a href="#" class="btn-sm btn-primary font-weight-bolder" id="add_product_btn">
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
                    </span>Tambah Produk</a>
                    </div>
                    <div class="col-12" id="purchase_order_detail_content"></div>
                </div>
                <!--end::Row-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-danger font-weight-bold" id="cancel_purchase_order_btn">Hapus PO</button>
                <button type="submit" class="btn btn-primary font-weight-bold" id="save_purchase_order_btn">Tutup</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="AddProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Tambah Produk</h5>
            </div>
            <div class="modal-body">
                <div class="card-header flex-wrap py-1">
                    <div class="form-group row">
                        <div class="col-lg-4 pt-1">
                            <select class="form-control" id="br_id_filter_item" name="br_id_filter_item" required>
                                <option value="">- Brand/All -</option>
                                @foreach ($data['br_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="br_id_filter_parent_item"></div>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <select class="form-control" id="mc_id_filter_item" name="mc_id_filter_item" required>
                                <option value="">- Warna/All -</option>
                                @foreach ($data['mc_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="mc_id_filter_parent_item"></div>
                        </div>
                        <!-- <div class="col-lg-4 pt-1">
                            <select class="form-control" id="sz_id_filter_item" name="sz_id_filter_item" required>
                                <option value="">- Size/All -</option>
                                @foreach ($data['sz_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="sz_id_filter_parent_item"></div>
                        </div> -->
                    </div>
                </div>
                <div class="table-responsive">
                    <!--begin: Datatable-->
                    <center><input type="search" class="form-control form-control-sm col-6" id="product_search" placeholder="Cari nama produk / warna / brand / supplier"/></center><br/>
                    <table class="table table-hover table-checkable" id="Producttb">
                        <thead class="bg-primary text-light">
                            <tr>
                                <th class="text-light">No</th>
                                <th style="white-space: nowrap;" class="text-light">Nama</th>
                                <th style="white-space: nowrap;" class="text-light">Warna</th>
                                <th style="white-space: nowrap;" class="text-light">Brand</th>
                                <th style="white-space: nowrap;" class="text-light">Size</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <!--end: Datatable-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary font-weight-bold" id="add_item_btn">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
