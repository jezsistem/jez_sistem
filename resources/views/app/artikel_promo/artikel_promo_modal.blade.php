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
                        <div class="form-group">
                            <label>Download Template
                                <span class="text-danger">*</span></label>
                            <a href="{{ asset('upload/template/artikel_promo_template.xlsx') }}"
                               class="btn btn-xs btn-primary">Download</a>
                        </div>
                        <div class="form-group">
                            <label>Pilih template yang sudah di download dan diisi
                                <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="artikel_promo_template" id="artikel_promo_template"
                                   required="required"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                            data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /Modal -->
