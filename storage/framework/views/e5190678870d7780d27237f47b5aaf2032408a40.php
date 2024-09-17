<!-- Modal-->
<div class="modal fade" id="ApproveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="width: 100%; max-width: 1300px;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">#<span id="invoice_label"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                
                <div class="row">
                    <div class="col-4">
                        <label class="badge badge-primary">Total</label >
                        <label class="badge badge-secondari" id="total_approval_price"></label>
                    </div>
                        
                </div>
                <table class="table table-hover" id="APDtb">
                    <thead class="bg-primary">
                        <tr>
                            <th class="text-white">No</th>
                            <th class="text-white">Tanggal Terima</th>
                            <th class="text-white">Invoice</th>
                            <th class="text-white">SKU</th>
                            <th class="text-white">Brand</th>
                            <th class="text-white">Artikel</th>
                            <th class="text-white">Warna</th>
                            <th class="text-white">Size</th>
                            <th class="text-white">Tipe</th>
                            <th class="text-white">Qty Terima</th>
                            <th class="text-white">In Stock</th>
                            <th class="text-white">Harga Beli</th>
                            <th class="text-white">Total</th>
                            <th class="text-white"></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold"
                    data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="approve_btn">Bayar</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
<?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/purchase_order_receive_cod/purchase_order_receive_cod_modal.blade.php ENDPATH**/ ?>