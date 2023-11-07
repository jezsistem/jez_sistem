<!-- Modal-->
<div class="modal fade" id="StockTransferDataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Terima Transfer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <center><input type="search" class="form-control  col-6" id="stock_transfer_receive_search" placeholder="Cari artikel"/></center>
                <a class="btn btn-primary float-right" style="margin-bottom:15px;" id="accept_qty_btn">Terima</a>
                <input type="hidden" id="stf_id" value=""/>
                <input type="hidden" id="stf_code_label" value=""/>
                <input type="hidden" id="st_id_end" value=""/>
                <table class="table table-hover table-checkable pr-4" id="StockTransferDataAccepttb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">Artikel</th>
                            <th class="text-dark">Qty</th>
                            <th class="text-dark">Diterima</th>
                            <th class="text-dark">Terima</th>
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