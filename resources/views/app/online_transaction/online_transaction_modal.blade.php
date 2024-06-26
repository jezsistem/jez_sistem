<!-- Modal-->
<div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
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
                            {{--                            <a href="{{ asset('upload/template/data_supplier_template.xlsx') }}" class="btn btn-xs btn-primary">Download</a>--}}
                            <div class="dropdown dropdown-inline mr-2">
                                <button type="button"
                                        class="btn btn-light-danger font-weight-bolder dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="svg-icon svg-icon-md">
                                    </span>Download
                                </button>
                                <!--begin::Dropdown Menu-->
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <!--begin::Navigation-->
                                    <ul class="navi flex-column navi-hover py-2">
                                        <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
                                            Template File :
                                        </li>
                                        <li class="navi-item" id="download_template_shopee">
                                            <a class="navi-link">
                                                    <span class="navi-icon">
                                                        <i class="la la-download"></i>
                                                    </span>
                                                <span>Shopee</span>
                                            </a>
                                        </li>
                                        <li class="navi-item">
                                            <a class="navi-link" id="download_template_tiktok">
                                                    <span class="navi-icon">
                                                        <i class="la la-download"></i>
                                                    </span>
                                                <span>Tiktok</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <!--end::Navigation-->
                                </div>
                                <!--end::Dropdown Menu-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Pilih template yang sudah di download dan diisi
                                <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="importFile" id="importFile" required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal-->
<div class="modal fade" id="DetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Detail Item Pesanan #<span id="num_order"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <!--begin: Datatable-->
                    <input type="hidden" id="to_id" value=""/>
                    <a href="#" class="btn btn-dark font-weight-bolder" id="add_product_sub_sub_category_btn">
                                <span class="svg-icon svg-icon-md">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                         width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <circle fill="#000000" cx="9" cy="15" r="6"/>
                                            <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z"
                                                  fill="#000000" opacity="0.3"/>
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>Tambah Item</a>
                    <table class="table table-hover" id="Detailtb">
                        <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Artikel</th>
                            <th class="text-dark">SKU</th>
                            <th class="text-dark">Qty</th>
                            <th class="text-dark">Platform Price</th>
                            <th class="text-dark">Jez Price</th>
                            <th class="text-dark">Diff Price</th>
                            <th class="text-dark">Discount Total</th>
                            <th class="text-dark">Final Price</th>
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
        </div>
    </div>
</div>
<!-- /Modal -->
