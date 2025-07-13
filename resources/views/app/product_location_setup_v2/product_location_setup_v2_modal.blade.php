<!-- Modal-->
<div class="modal fade" id="TargetModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_target">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Mutation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger font-weight-bold" id="delete_target_btn"
                        style="display:none;">Hapus</button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="save_target_btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<form id="f_import" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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
                        <p>
                            <b>
                                Pastikan Anda menggunakan template import yang hanya berisi dua kolom, yaitu kolom kode SKU dan Qty mutasi.<br>
                                Format file wajib <span class="text-danger">CSV</span>.<br>
                                <span class="text-primary">Ekspor file dari Excel dengan memilih <b>CSV (MS-DOS) (*.csv)</b> pada saat menyimpan.</span>
                            </b>
                        </p>
                        <a href="{{ asset('upload/template/impot_single_bin_mutasi_template.xlsx') }}" class="btn btn-xs btn-primary mb-3">
                            Download Template <i class="fas fa-file-download"></i>
                        </a>
                        <div class="form-group">
                            <label>Pilih template yang sudah diisi data <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="importFile" id="importFile" accept=".csv"
                                required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="MutationMultiBinModal" tabindex="-1" role="dialog" aria-labelledby="MutationMultiBinLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light d-flex justify-content-between align-items-center">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h5 class="modal-title text-dark" id="MutationMultiBinLabel">Mutation Multi Bin</h5>
                    <div class="d-flex align-items-center">
                        <a href="{{ asset('upload/template/mutation_multi_bin_template.xlsx') }}"
                            class="btn btn-xs btn-primary mr-5">
                            Download Template <i class="fas fa-file-download"></i>
                        </a>
                        <button type="button" class="btn btn-dark font-weight-bold mr-7" id="clearMutationMultiBinBtn">
                            Clear
                        </button>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <form id="mutationMultiBinForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group d-flex align-items-center">
                        <label for="mutationInput" class="mr-2">Import Excel</label>
                        <input type="file" class="form-control mr-2" id="mutationInput" name="mutationInput"
                            placeholder="Enter mutation data">
                        <button type="submit" class="btn btn-primary font-weight-bold"
                            id="addMutationBtn">Import</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered" id="mutationMultiBinTable">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th style="width: 200px;">SKU</th>
                                <th style="width: 200px;">BIN Awal</th>
                                <th style="width: 200px;">BIN Tujuan</th>
                                <th>Quantity Bin Awal (Current)</th>
                                <th>Quantity Mutasi</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rows will be dynamically added here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold"
                    data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-dark font-weight-bold"
                    id="saveMutationMultiBinBtn">Mutasi</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
