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
<div class="modal fade" id="TransaksiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
<div class="modal fade" id="ProductSupplierModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_product_supplier">
                @csrf
                <input type="hidden" name="_id" id="_id" value=""/>
                <input type="hidden" name="_mode" id="_mode" value=""/>
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Nama Supplier <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ps_name" name="ps_name" required/>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">PKP</label>
                            <select class="form-control" id="ps_pkp" name="ps_pkp">
                                <option value="0" selected>Tidak</option>
                                <option value="1" selected>Ya</option>
                            </select>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Tempo</label>
                            <input type="number" class="form-control" id="ps_due_day" name="ps_due_day"/>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Email</label>
                            <input type="email" class="form-control" id="ps_email" name="ps_email"/>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">No Telp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ps_phone" name="ps_phone" required/>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Alamat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ps_address" name="ps_address" required/>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Rekening</label>
                            <input type="text" class="form-control" id="ps_rekening" name="ps_rekening"/>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">NPWP</label>
                            <input type="text" class="form-control" id="ps_npwp" name="ps_npwp"/>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Deskripsi</label>
                            <input type="text" class="form-control" id="ps_description" name="ps_description"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <button type="button" class="btn btn-danger font-weight-bold" id="delete_product_supplier_btn"
                            style="display:none;">Hapus
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="save_product_supplier_btn">Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->