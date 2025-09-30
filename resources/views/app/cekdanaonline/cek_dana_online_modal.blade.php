<!-- Modal-->
<div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Import Template</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Store<span class="text-danger">*</span></label>
                            <select name="st_id" id="st_id_form" class="form-control">
                                <option value="">-- Pilih Store --</option>
                                @foreach ($data['st_id'] as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Platform<span class="text-danger">*</span></label>
                            <select name="platform_name" id="platform_name_form" class="form-control">
                                <option value="">-- Pilih Platform --</option>
                                <option value="Tiktok">TikTok</option>
                                <option value="Shopee">Shopee</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Pilih template
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
    <div class="modal-dialog modal-lg" role="document">
       <div class="modal-content">
          <div class="modal-header bg-light">
             <h5 class="modal-title text-dark" id="exampleModalLabel">Detail Cek Dana Online</h5>
             <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i aria-hidden="true" class="ki ki-close"></i>
             </button>
          </div>
          <div class="modal-body">
             <div class="card-body table-responsive">
                <table class="table table-hover">
                    <tbody>
                       <tr>
                          <td><strong>Store</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Platform</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Order Number</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Tanggal Trx Jezpro</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Tanggal Dana Cair</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Net sales Jezpro</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Revenue MP</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Selisih</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Seller voucher discount</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Presentase Seller Voucher</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Affiliate commission</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Marketplace commission fee</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Service fee</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Dynamic Commission</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Voucher Xtra Service Fee</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Bonus cashback service fee</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Total Fees</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Presentase Fee</strong></td>
                          <td></td>
                       </tr>
                       <tr>
                          <td><strong>Total settlement amount</strong></td>
                          <td></td>
                       </tr>
                    </tbody>
                </table>
             </div>
          </div>
          <div class="modal-footer">
             <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
          </div>
       </div>
    </div>
</div>


<!-- Modal-->
<div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Tambah Item Pesanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="add_item_to_id" value=""/>
                <div class="form-group mb-1 pb-1">
                    <label for="exampleTextarea">Nomer Pesanan</label>
                    <input type="text" class="form-control" id="no_pesanan" name="no_pesanan" required disabled/>
                </div>
                <div class="form-group mb-1 pb-1">
                    <label for="exampleTextarea">Berat*</label>
                    <input type="number" class="form-control" id="pssc_weight" name="pssc_weight" required/>
                </div>
                <div class="form-group mb-1 pb-1">
                    <label for="exampleTextarea">Deskripsi</label>
                    <input type="text" class="form-control" id="pssc_description" name="pssc_description"/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold">Tutup</button>
                <button type="button" class="btn btn-dark font-weight-bold">Tambah</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Tambah Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="f_customer">
                <div class="modal-body">
                    <input type="hidden" id="add_item_to_id" value=""/>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nomor Telp</label>
                        <input type="number" class="form-control" id="nomor_telp" name="nomor_telp" required/>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama Customer</label>
                        <input type="text" class="form-control" id="nama_customer" name="nama_customer" required
                               disabled/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold">Tutup</button>
                    <button type="button" class="btn btn-dark font-weight-bold">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<style>
    /* Global table styling */
.table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Arial', sans-serif;
  font-size: 12px;
  color: #333;
  text-align: left;
  margin-bottom: 20px;
}

/* Table header styling */
.table thead {
  background-color: #fff0f4;
  color: #333;
}

.table thead th {
  padding: 12px 15px;
  border-bottom: 2px solid #000000;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-size: 14px;
  /* font-weight: bold; */
}

/* Table body styling */
.table tbody tr {
  border-bottom: 1px solid #f0f0f0;
}

.table tbody tr:nth-child(even) {
  background-color: #fff0f4;
}

.table tbody tr:hover {
  color: rgb(255, 0, 0);
  transition: all 0.3s ease;
}

/* Table cell padding */
.table td {
  padding: 10px 15px;
  border-bottom: 1px solid #FFEDD3;
}

/* Final column adjustments */
.table tbody tr td:last-child {
  font-weight: bold;
}

/* Hover effect on the table rows */
.table-hover tbody tr:hover td {
  color: rgb(255, 0, 0);
}

.text-dark {
  color: #333 !important;
}

/* Responsive design */
@media screen and (max-width: 768px) {
  .table {
    font-size: 14px;
  }
}

</style>