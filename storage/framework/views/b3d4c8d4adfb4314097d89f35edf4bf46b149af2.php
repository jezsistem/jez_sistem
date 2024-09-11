<!-- Modal-->
<div class="modal fade" id="GraphModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Grafik <small><i>* silahkan pilih status grafik pada opsi dibawah</i></small></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <select class="form-control col-12 bg-primary text-white" id="chart_filter" name="chart_filter">
                    <option value="sales">Terjual</option>
                    <option value="offline">Waiting Offline</option>
                    <option value="online">Waiting Online</option>
                    <option value="instock">Instock Lihat - lihat</option>
                    <option value="sales_instock">Instock Refund Exchange</option>
                </select>
                <div id="chart"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="UserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel"><span id="article_label"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th class="text-dark">Invoice</th>
                            <th class="text-dark"><span id="invoice_label"></span></th>
                        </tr>
                        <tr>
                            <th class="text-dark">Picker</th>
                            <th class="text-dark"><span id="picker_label"></span></th>
                        </tr>
                        <tr>
                            <th class="text-dark">Cashier</th>
                            <th class="text-dark"><span id="cashier_label"></span></th>
                        </tr>
                        <tr>
                            <th class="text-dark">Customer</th>
                            <th class="text-dark"><span id="customer_label"></span></th>
                        </tr>
                        <tr>
                            <th class="text-dark">Helper</th>
                            <th class="text-dark"><span id="helper_label"></span></th>
                        </tr>
                        <tr>
                            <th class="text-dark">Packer</th>
                            <th class="text-dark"><span id="packer_label"></span></th>
                        </tr>
                        <tr>
                            <th class="text-dark">Status</th>
                            <th class="text-dark"><span id="status_label"></span></th>
                        </tr>
                        <tr>
                            <th class="text-dark">Invoice Note</th>
                            <th class="text-dark"><span id="invoice_note_label"></span></th>
                        </tr>
                        <tr>
                            <th class="text-dark">Refund/Exchange Note</th>
                            <th class="text-dark"><span id="refund_exchange_note_label"></span></th>
                        </tr>
                        <tr>
                            <th class="text-dark">Pergerakan Terakhir</th>
                            <th class="text-dark"><span id="last_updated_label"></span></th>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <?php if($data['user']->g_name == 'administrator'): ?>
                <button type="button" class="btn btn-danger font-weight-bold" data-id="" id="delete_btn">Hapus</button>
                <?php endif; ?>
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
<?php /**PATH /home/sistem.jez.co.id/public_html/resources/views/app/stock_tracking/stock_tracking_modal.blade.php ENDPATH**/ ?>