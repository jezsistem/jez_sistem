<!-- Modal-->
<div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_import" enctype="multipart/form-data">
            @csrf
            <div class="modal-header bg-success">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Update Stok Marketplace</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group">
                        <label>Posisi Kolom Kode (Memanjang Kesamping, Dimulai dari A)
                        <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="column" id="column" required/>
                    </div>
                    <div class="form-group">
                        <label>Posisi Baris Kode (Memanjang Kesamping, Dimulai dari A)
                        <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="row" id="row" required/>
                    </div>
                    <div class="form-group">
                        <label>Ambil Stok Dari
                        <span class="text-danger">*</span></label>
                        <select class="form-control mt-2 bg-primary text-white" name="st_id" id="st_id" required>
                            <option value='all'>Pilih Store</option>
                            @foreach ($data['st_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Pilih template marketplace
                        <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="template" id="template" required/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Eksekusi</button>
            </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal-->
<div class="modal fade" id="ImportTemplateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_import_template" enctype="multipart/form-data">
            @csrf
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Synchronize Code</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group">
                        <label>Posisi Kolom Kode dan Judul Kolom<br/>(Memanjang Kesamping, Dimulai dari A)
                        <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="code_column" id="code_column" required/>
                        <input type="text" class="form-control mt-2" placeholder="Judul Kolom" name="code_title" id="code_title" required/>
                    </div>
                    <div class="form-group">
                        <label>Posisi Kolom Nama Artikel <br/>(Memanjang Kesamping, Dimulai dari A)
                        <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="article_column" id="article_column" required/>
                    </div>
                    <div class="form-group">
                        <label>Posisi Kolom Variasi Artikel <br/>(Memanjang Kesamping, Dimulai dari A)
                        <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="variation_column" id="variation_column" required/>
                    </div>
                    <div class="form-group">
                        <label>Pilih template marketplace
                        <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="template" id="template" required/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="import_template_data_btn">Eksekusi</button>
            </div>
            </form>
        </div>
    </div>
</div>
