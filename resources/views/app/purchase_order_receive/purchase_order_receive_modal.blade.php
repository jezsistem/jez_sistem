<!-- Modal-->
<div class="modal fade" id="PurchaseOrderModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document" style="width:100%; max-width:1300px;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Penerimaan PO #<span id="po_invoice_label"></span></h5>
            </div>
            <form id="f_po">
            <input type="hidden" id="_mode" name="_mode"/>
            <input type="hidden" id="_po_id" name="_po_id"/>
            <div class="modal-body">
                <!--begin::Row-->
                <div class="row">
                    <div class="col-4">
                        <label>Store</label>
                        <select class="form-control" id="st_id" name="st_id" required disabled>
                            <option value="">- Pilih Store -</option>
                            @foreach ($data['st_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="st_id_parent"></div>
                    </div>
                    <div class="col-4">
                        <label>Supplier</label>
                        <select class="form-control" id="ps_id" name="ps_id" required disabled>
                            <option value="">- Pilih Supplier -</option>
                            @foreach ($data['ps_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="ps_id_parent"></div>
                    </div>
                    <div class="col-4">
                        <label>Deskripsi</label>
                        <textarea class="form-control" name="po_description" id="po_description" rows="3" readonly></textarea>
                    </div>
                    <div class="col-4">
                        <label>Tipe Stok * otomatis dari master PO jika diisi oleh tim terkait</label>
                        <select class="form-control" id="stkt_id" name="stkt_id" required>
                            <option value="">- Pilih Tipe Stok -</option>
                            @foreach ($data['stkt_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="stkt_id_parent"></div>
                    </div>
                    <div class="col-4">
                        <label>Pajak</label>
                        <select class="form-control" id="tax_id" name="tax_id" required>
                            <option value="">- Pajak -</option>
                            @foreach ($data['tax_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <div id="tax_id_parent"></div>
                    </div>
                    <div class="col-4">
                        <label>Tanggal Terima</label>
                        <input type="date" id="receive_date" class="form-control" value=""/>
                    </div>
                    <div class="col-4 mt-4">
                        <label class="badge badge-primary">Invoice</label>
                        <input type="text" id="receive_invoice" class="form-control" value="" placeholder="INVxx"/>
                    </div>
                    <div class="col-4 mt-4">
                        <label class="badge badge-primary">Tanggal Invoice</label>
                        <input type="date" id="invoice_date" class="form-control" value=""/>
                    </div>
                </div>
                <!--end::Row-->
                <!--begin::Row-->
                <div class="row mt-2" >
                    <div class="col-12" id="purchase_order_detail_content"></div>
                </div>
                <!--end::Row-->
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_purchase_order_btn">Tutup</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="PurchaseOrderReceiveDetailModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">History Terima <span id="purchase_article_label"></span></h5>
            </div>

            <div class="modal-body table-responsive">
                <input type="hidden" id="poad_id"/><br/>
                <table class="table table-hover" id="Poadstb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Tanggal</th>
                            <th class="text-dark">Tipe</th>
                            <th class="text-dark">Pajak</th>
                            <th class="text-dark" style="white-space:nowrap;">Qty Terima</th>
                            <th class="text-dark">Disc</th>
                            <th class="text-dark">ExDisc</th>
                            <th class="text-dark">HPP</th>
                            <th class="text-dark">Total</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="EditPoadsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="overflow-y: auto;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_poads">
            @csrf
            <input type="hidden" name="_poads_id" id="_poads_id" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Revisi Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">tanggal</label>
                        <input type="text" class="form-control" id="created_at" name="created_at" readonly/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Tipe Stok</label>
                        <select class="form-control" id="stkt_id_edit" name="stkt_id_edit" required>
                            <option value="">- Pilih -</option>
                            @foreach ($data['stkt_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Qty Terima</label>
                        <input type="text" class="form-control" id="poads_qty" name="poads_qty" required/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Discount</label>
                        <input type="text" class="form-control" id="poads_discount" name="poads_discount"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Extra Discount</label>
                        <input type="text" class="form-control" id="poads_extra_discount" name="poads_extra_discount"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">HPP</label>
                        <input type="text" class="form-control" id="poads_purchase_price" name="poads_purchase_price"/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Total</label>
                        <input type="text" class="form-control" id="poads_total_price" name="poads_total_price"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <!--<button type="button" class="btn btn-danger font-weight-bold" id="delete_poads_btn" style="display:none;">Hapus</button>-->
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_poads_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="PoReportModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Laporan Penerimaan</h5>
            </div>

            <div class="modal-body table-responsive">
                <a class="btn-sm btn-primary float-right" id="excel_report">Excel</a><br/>
                <table class="table table-hover" id="PoReporttb">
                    <thead class="text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark" style="white-space:nowrap;">Tanggal PO</th>
                            <th class="text-dark" style="white-space:nowrap;">Nomor PO</th>
                            <th class="text-dark">Store</th>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">Artikel</th>
                            <th class="text-dark">Warna</th>
                            <th class="text-dark">Size</th>
                            <th class="text-dark">Order</th>
                            <th class="text-dark">Terima</th>
                            <th class="text-dark">Harga Beli</th>
                            <th class="text-dark">Total Beli</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
