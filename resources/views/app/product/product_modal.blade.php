<!-- Modal-->
<div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <a href="{{ asset('upload/template/artikel_template_import.xlsx') }}" class="btn btn-xs btn-primary">Download</a>
                    </div>
                    <div class="form-group">
                        <label>Pilih template yang sudah di download dan diisi
                        <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="p_template" id="p_template" required/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="ProductModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="f_product">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <input type="hidden" name="_sz_barcode" id="_sz_barcode" value="" />
            <input type="hidden" name="_sz_id" id="_sz_id" value="" />
            <input type="hidden" name="_sz_sell_price" id="_sz_sell_price" value="" />
            <input type="hidden" name="_current_pc_id" id="_current_pc_id" value="" />
            <input type="hidden" name="_current_psc_id" id="_current_psc_id" value="" />
            <input type="hidden" name="_current_pssc_id" id="_current_pssc_id" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Artikel </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4 pt-1">
                            <label>Kode Artikel</label>
                            <input type="text" name="article_id" id="article_id" class="form-control" placeholder="Kode Artikel"/>
                        </div>
                        <div class="col-lg-4 pt-1 float-right ml-auto">
                            <div id="product_qr"></div>
                        </div>
                    </div>
                    <div class="form-group row" id="pcpscpssc_edit" style="display:none;">
                        <div class="col-lg-4 pt-1">
                            <label>Kategori <span class="text-danger">*</span></label>
                            <select class="form-control" id="_pc_id" name="_pc_id" required>
                                <option value="">- Pilih Kategori -</option>
                                @foreach ($data['pc_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="_pc_id_parent"></div>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <label>Sub Kategori <span class="text-danger">*</span></label>
                            <select class="form-control" id="_psc_id" name="_psc_id" required>
                                <option value="">- Pilih Sub Kategori -</option>
                                @foreach ($data['psc_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="_psc_id_parent"></div>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <label>Sub-Sub Kategori <span class="text-danger">*</span></label>
                            <select class="form-control" id="_pssc_id" name="_pssc_id" required>
                                <option value="">- Pilih Sub-Sub Kategori -</option>
                                @foreach ($data['pssc_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="_pssc_id_parent"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4 pt-1">
                            <label>Nama Artikel <span class="text-danger">*</span></label>
                            <input type="text" name="p_name" id="p_name" class="form-control" placeholder="Nama artikel" required/>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <label>Brand <span class="text-danger">*</span></label>
                            <select class="form-control" id="br_id" name="br_id" required>
                                <option value="">- Pilih Brand -</option>
                                @foreach ($data['br_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="br_id_parent"></div>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <label>Supplier <span class="text-danger">*</span></label>
                            <select class="form-control" id="ps_id" name="ps_id" required>
                                <option value="">- Pilih Supplier -</option>
                                @foreach ($data['ps_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="ps_id_parent"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4 pt-1">
                            <label>Satuan Artikel <span class="text-danger">*</span></label>
                            <select class="form-control" id="pu_id" name="pu_id" required>
                                <option value="">- Pilih Satuan -</option>
                                @foreach ($data['pu_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="pu_id_parent"></div>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <label>Gender <span class="text-danger">*</span></label>
                            <select class="form-control" id="gn_id" name="gn_id" required>
                                <option value="">- Pilih Gender -</option>
                                @foreach ($data['gn_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="gn_id_parent"></div>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <label>Season </label>
                            <select class="form-control" id="ss_id" name="ss_id" required>
                                <option value="">- Pilih Season -</option>
                                @foreach ($data['ss_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="ss_id_parent"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4 pt-1">
                            <label>Warna Utama <span class="text-danger">*</span></label>
                            <select class="form-control" id="mc_id" name="mc_id" required>
                                <option value="">- Pilih Warna -</option>
                                @foreach ($data['mc_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="mc_id_parent"></div>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <label>Warna Artikel <span class="text-danger">*</span></label>
                            <input type="text" name="p_color" id="p_color" class="form-control" placeholder="Warna artikel" required/>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <label>Aging</label>
                            <input type="month" name="p_aging" id="p_aging" class="form-control" placeholder="Aging / Usia Artikel"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4 pt-1">
                            <label>Berat Items </label>
                            <input type="number" name="p_weight" id="p_weight" class="form-control" placeholder="gram"/>
                        </div>

                        <div class="col-lg-4 pt-1">
                            <label>Size Schema </label>
                            <select class="form-control" id="sz_schema_modal_id" name="sz_schema_modal_id">
                                <option value="">- Pilih Size Schema -</option>
                                @foreach ($data['sz_schema_id'] as $key => $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="sz_schema_modal_id_parent"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-4 pt-1">
                            <label>Harga Banderol <span class="text-danger">*</span></label>
                            <input type="text" name="p_price_tag" id="p_price_tag" class="form-control decimal" placeholder="Rp." required/>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <label>Harga Beli <span class="text-danger">*</span></label>
                            <input type="text" name="p_purchase_price" id="p_purchase_price" class="form-control decimal" placeholder="Rp." required/>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <label>Harga Jual <span class="text-danger">*</span></label>
                            <input type="text" name="p_sell_price" id="p_sell_price" class="form-control decimal" placeholder="Rp." required/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 pt-1">
                            <label>Size Artikel: <span id="barcode_running_label" style="display:none;">(Size | Barcode | Running Code | Harga Banderol | Harga Jual | Harga Beli)</span> <a class="btn btn-sm btn-primary" onclick="return activateColumn()">undisabled</a></label>
                            <div id="reload_size"> </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12 pt-1">
                        <label for="exampleTextarea">Deskripsi Artikel</label>
                        <textarea class="form-control" name="p_description" id="p_description" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_product_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_product_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ProductDetailModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Detail Artikel <span id="product_name_label"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="productDetailContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="ProductBarcodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Lengkapi Barcode</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <input type="search" class="form-control  col-6" id="p_search" placeholder="Cari artikel"/><br/>
                <table class="table table-hover table-checkable" id="Ptb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">Artikel</th>
                            <th class="text-dark">Warna</th>
                            <th class="text-dark">Size</th>
                            <th class="text-dark">Barcode</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->
