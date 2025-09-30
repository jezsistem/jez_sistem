<!-- Modal-->
<div class="modal fade" id="ShippingNumberModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_shipping_number" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_cust_id" id="_cust_id" value="" />
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Shipping Number</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Kurir</label>
                        <select class="form-control" id="courier" name="courier" required>
                            <option value="">- Pilih -</option>
                            <option value="jne">JNE</option>
                            <option value="pos">POS Indonesia</option>
                            <option value="jnt">JNT</option>
                            <option value="sicepat">SiCepat</option>
                            <option value="tiki">TIKI</option>
                            <option value="anteraja">Anter Aja</option>
                            <option value="wahana">WAHANA</option>
                            <option value="ninja">Ninja</option>
                            <option value="lion">Lion Parcel</option>
                            <option value="pcp">PCP</option>
                            <option value="jet">JET</option>
                            <option value="rex">REX Express</option>
                            <option value="sap">SAP Express</option>
                            <option value="jxe">JX Express</option>
                            <option value="rpx">RPX Express</option>
                            <option value="first">First Logistics</option>
                            <option value="ide">ID Express</option>
                            <option value="spx">Shopee Express</option>
                            <option value="kgx">KGX Express</option>
                        </select>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">No Resi</label>
                        <input type="text" class="form-control" id="pos_shipping_number" name="pos_shipping_number" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Gambar Terkait Resi</label>
                        <input class="form-control"  type="file" name="image" id="image" accept="image/*" onchange="loadFile(event)">
                        <img id="imagePreview" style="width:40%; padding-top:10px;"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_shipping_number_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="WaybillTrackingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="waybill_tracking"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="CheckInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Periksa Invoice Terkait</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="search" class="form-control" placeholder="Input invoice exchange / refund" id="complaint_invoice"/>
                <a class="btn btn-sm btn-success float-right" id="search_complaint_btn">Cari</a>
                <div id="invoice_result"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="DPPaymentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="f_payment_dp">
                @csrf
                <input type="hidden" name="_pt_id" id="_pt_id" value="" />
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="exampleModalLabel">
                        <i class="fas fa-money-bill-wave mr-2"></i>DP Payment
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body p-0">
                        <!-- Payment Summary Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-calculator mr-2"></i>Ringkasan Pembayaran
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted">Harga Total:</label>
                                            <div class="bg-light p-2 rounded">
                                                <span class="text-primary font-weight-bold h5" id="total_payment_real_price">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted">Total Pembayaran Pertama:</label>
                                            <div class="bg-light p-2 rounded">
                                                <span class="text-success font-weight-bold h5" id="first_payment_amount">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted">Sisa Pembayaran:</label>
                                            <div class="bg-warning p-2 rounded">
                                                <span class="text-dark font-weight-bold h5" id="difference_payment">Rp 0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted">DP Dilakukan:</label>
                                            <div class="bg-light p-2 rounded">
                                                <span class="text-info font-weight-bold" id="dp_date">-</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted">Method Pembayaran:</label>
                                            <div class="bg-light p-2 rounded">
                                                <span class="text-info font-weight-bold" id="dp_method">-</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-muted">Catatan:</label>
                                            <div class="bg-light p-2 rounded">
                                                <span class="text-info font-weight-bold" id="dp_notes">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form Section -->
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-credit-card mr-2"></i>Detail Pembayaran
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Payment Method <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-lg" id="payment_method" name="payment_method" required>
                                                <option value="">- Pilih Metode Pembayaran -</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Sub Payment</label>
                                            <select class="form-control form-control-lg" id="sub_payment" name="sub_payment" >
                                                <option value="">- Pilih Sub Payment -</option>
                                                <option value="3">On Us</option>
                                                <option value="4">Off Us</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Jumlah Pembayaran <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">Rp</span>
                                                </div>
                                                <input type="number" class="form-control form-control-lg" id="payment_dp" name="payment_dp" placeholder="0" required />
                                            </div>
                                            <small class="form-text text-muted">Masukkan jumlah pembayaran yang akan dibayar</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control form-control-lg" id="payment_dp_date" name="payment_dp_date" required />
                                            <small class="form-text text-muted">Pilih tanggal pembayaran</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Catatan</label>
                                            <textarea class="form-control" id="payment_notes" name="payment_notes" rows="3" placeholder="Masukkan catatan pembayaran (opsional)"></textarea>
                                            <small class="form-text text-muted">Catatan tambahan untuk pembayaran ini</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
                    <button type="submit" class="btn btn-primary font-weight-bold" id="save_payment_dp_btn">
                        <i class="fas fa-save mr-2"></i>Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->