<!-- Modal-->
<div class="modal fade" id="PurchaseOrderModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <div class="modal-dialog modal-xl" role="document" style="width:100%; max-width:1300px;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Pre Order #<span id="po_invoice_label"></span></h5>
                <!--begin::Button Import-->
                <div class="mr-2">
                    <div class="dropdown dropdown-inline mr-2">
                        <a type="button" class="btn btn-light-primary font-weight-bolder" id="ExportArticleData">
                                <span class="svg-icon svg-icon-md">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
                                            <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>Export</a>
                    </div>
                    <div class="dropdown dropdown-inline mr-2">
                        <a type="button" class="btn btn-light-primary font-weight-bolder" id="ImportModalBtn" aria-haspopup="true" aria-expanded="false">
                                <span class="svg-icon svg-icon-md">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
                                            <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>Import</a>
                    </div>
                    <!--begin::Dropdown Menu-->
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                        <!--begin::Navigation-->
                        <ul class="navi flex-column navi-hover py-2">
                            <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">Bentuk File :</li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                                    <span class="navi-icon">
                                                        <i class="la la-copy"></i>
                                                    </span>
                                    <span id="purchase_order_excel_btn"></span>
                                </a>
                            </li>
                        </ul>
                        <!--end::Navigation-->
                    </div>
                    <!--end::Dropdown Menu-->
                </div>
                <!--end::Dropdown-->
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
                        <label>Brand</label>
                        <select class="form-control" id="br_id" name="br_id" required>
                            <option value="">- Pilih Brand -</option>
                            @foreach ($data['br_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="br_id_parent"></div>
                    </div>
                    <div class="col-4 mt-5">
                        <label>Season</label>
                        <select class="form-control" id="ss_id" name="ss_id" required>
                            <option value="">- Pilih Season -</option>
                            @foreach ($data['ss_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="ss_id_parent"></div>
                    </div>
                    <div class="col-4 mt-5">
                        <label>Jenis Pre Order</label>
                        <select class="form-control" id="preorder_type" name="preorder_type" required>
                            <option value="">- Pilih Type Pre order -</option>
                            <option value=0>REPEAT</option>
                            <option value=1>LAUNCHING</option>
                        </select>
                        <div id="preorder_type_parent"></div>
                    </div>
                    <div class="col-4 mt-4 d-flex flex-column">
                        <label class="badge badge-primary">File Pre Order</label>
                        <div class="row justify-content-start">
                            <a class="input-group col-5" type="button" id="UploadPOBtn" aria-haspopup="true" aria-expanded="false">
                                <label class="input-group-text" for="Uploadpo">
                                    <span class="svg-icon svg-icon-md">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-capslock-fill" viewBox="0 0 16 16">
                                            <path d="M7.27 1.047a1 1 0 0 1 1.46 0l6.345 6.77c.6.638.146 1.683-.73 1.683H11.5v1a1 1 0 0 1-1 1h-5a1 1 0 0 1-1-1v-1H1.654C.78 9.5.326 8.455.924 7.816zM4.5 13.5a1 1 0 0 1 1-1h5a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1h-5a1 1 0 0 1-1-1z"/>
                                          </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                    Upload FIle
                                </label>
                            </a>
                            <a class="input-group col-5" type="button" id="FilePreOrderBtn" aria-haspopup="true" aria-expanded="false">
                                <label class="input-group-text" for="Filepreorder">
                                    <span class="svg-icon svg-icon-md">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-collection-fill" viewBox="0 0 16 16">
                                            <path d="M0 13a1.5 1.5 0 0 0 1.5 1.5h13A1.5 1.5 0 0 0 16 13V6a1.5 1.5 0 0 0-1.5-1.5h-13A1.5 1.5 0 0 0 0 6zM2 3a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 0-1h-11A.5.5 0 0 0 2 3m2-2a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 0-1h-7A.5.5 0 0 0 4 1"/>
                                          </svg>
                                        <!--end::Svg Icon-->
                                    </span>
                                    File Pre Order
                                </label>
                            </a>
                        </div>
                    </div>
                    <div class="col-4 mt-5">
                        <label>Deskripsi / Catatan</label>
                        <textarea class="form-control" placeholder="Deskripsi / Catatan" name="preorder_description" id="preorder_description" rows="3"></textarea>
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
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_purchase_order_btn">Tutup</button>
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
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Tambah Produk</h5>
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
                        <div class="col-lg-4 pt-1">
                            <select class="form-control" id="psc_id_filter_item" name="psc_id_filter_item" required>
                                <option value="">- Sub Kategori -</option>
                                @foreach ($data['psc_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="psc_id_filter_parent_item"></div>
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
                    <center><input type="search" class="form-control  col-6" id="product_search" placeholder="Cari nama produk / warna / brand / supplier"/></center><br/>
                    <table class="table table-hover table-checkable" id="Producttb">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">No</th>
                                <th style="white-space: nowrap;" class="text-light">Nama</th>
                                <th style="white-space: nowrap;" class="text-light">Article ID</th>
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
                <button type="button" class="btn btn-dark font-weight-bold" id="add_item_btn">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<form id="f_import" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Import Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Download Template
                                <span class="text-danger">*</span></label>
                            <a href="{{ asset('upload/template/po_pembelian.xlsx') }}" class="btn btn-xs btn-primary">Download</a>
                        </div>
                        <div class="form-group">
                            <label>Pilih template yang sudah diisi data</label>
                            <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="importFile" id="importFile" accept=".csv" required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /Modal -->

<!-- Modal-->
<form id="f_upload_invoice_image" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="UploadImageInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Import Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Pilih Gambar Invoice</label>
                            <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="imageInvoices[]" id="imageInvoices" multiple required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="upload_image_invoice_btn">Upload</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /Modal -->

<!-- Modal-->
<form id="f_upload_preorder_file" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="UploadFilePreOrderModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Upload File Per Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Pilih File Pre Order</label>
                            <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="filepreorder[]" id="filepreorder"
                                multiple required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold"
                        id="upload_file_preorder_btn">Upload</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="FilePreOrderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">File PreOrder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="container">
                        <table id="FilePreOrderTb" class="table table-bordered">
                            <thead>
                            <tr>
                                <th>File PreOrder</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                        data-dismiss="modal">Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal-->