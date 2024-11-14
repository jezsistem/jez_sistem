<!-- Modal-->
<div class="modal fade" id="TargetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_target">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-light" id="exampleModalLabel">Target Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Periode</label>
                            <input type="month" class="form-control" id="tr_date" name="tr_date" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger font-weight-bold" id="delete_target_btn"
                        style="display:none;">Hapus</button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="save_target_btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="SubTargetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Data Target</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="target_date" value="" />
                <input type="hidden" id="tr_id" value="" />
                <a style="margin-bottom:10px;" class="btn btn-sm btn-primary float-right"
                    id="add_target_detail_btn">Tambah Data</a>
                <table class="table table-hover table-checkable" id="TargetDetailtb">
                    <thead class="bg-primary text-light">
                        <tr>
                            <th class="text-light">No</th>
                            <th class="text-light">Store</th>
                            <th class="text-light">Divisi</th>
                            <th class="text-light">Target</th>
                            <th class="text-light">Tercapai</th>
                            <th class="text-light">%</th>
                            <th class="text-light" style="white-space: nowrap;">[Penjualan] [Item Terjual]</th>
                            <th class="text-light"></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold"
                    data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

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
                                    </ul>
                                    <!--end::Navigation-->
                                </div>
                                <!--end::Dropdown Menu-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Pilih template yang sudah di download dan diisi
                                <span class="text-danger">*</span></label>
                            <input type="hidden" class="form-control" value="{{ \Illuminate\Support\Facades\Auth::user()->st_id }}" name="st_id_form" id="st_id_form" disabled>
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
<!-- /Modal -->
