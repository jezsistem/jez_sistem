<!-- Modal-->
<div class="modal fade" id="ShippingNumberModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_shipping_number" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_cust_id" id="_cust_id" value="" />
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Shipping Number</h5>
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
                <button type="submit" class="btn btn-primary font-weight-bold" id="save_shipping_number_btn">Simpan</button>
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
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">History</h5>
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
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Periksa Invoice Terkait</h5>
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
