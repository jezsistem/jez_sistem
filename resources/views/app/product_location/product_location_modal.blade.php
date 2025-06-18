<!-- Modal-->
<div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
                            <a href="{{ asset('upload/template/supplier_template.xlsx') }}"
                                class="btn btn-xs btn-primary">Download</a>
                        </div>
                        <div class="form-group">
                            <label>Pilih template yang sudah di download dan diisi
                                <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="pc_template" id="pc_template" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="ProductLocationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_product_location">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Lokasi Simpan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Kode Lokasi*</label>
                            <input type="text" class="form-control" id="pl_code" name="pl_code" required />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Nama Lokasi*</label>
                            <input type="text" class="form-control" id="pl_name" name="pl_name" required />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Nama Kota (eg: MALANG)</label>
                            <input type="text" class="form-control" id="pl_description" name="pl_description" />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Default Penerimaan</label>
                            <select class="form-control" id="pl_default" name="pl_default">
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Default Refund</label>
                            <select class="form-control" id="pl_default_refund" name="pl_default_refund">
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">BIN Freeze</label>
                            <select class="form-control" id="pl_freeze" name="pl_freeze">
                                <option value="0" selected>No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Kapasitas Bin (pcs/pairs)</label>
                            <input type="number" class="form-control" id="pl_capacity" name="pl_capacity" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger font-weight-bold" id="delete_product_location_btn"
                        style="display:none;">Hapus</button>
                    <button type="submit" class="btn btn-dark font-weight-bold"
                        id="save_product_location_btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->
