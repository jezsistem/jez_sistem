<!-- Modal-->
<div class="modal fade" id="CabangSummaryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Summary Penjualan Cabang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="cabang_content" class="table-responsive">
                    <h3>Omset <span id="cabang_omset_date"></span></h3>
                    <table class="table table-border table-striped">
                        <tbody>
                            <tr>
                                <td>Omset Global</td>
                                <td>Rp. <span id="cabang_omset"></span></td>
                            </tr>
                            <tr>
                                <td><b>Total Debit</b></td>
                                <td><b>Rp. <span id="cabang_total_debit"></span></b></td>
                            </tr>
                            <tr>
                                <td>* BCA</td>
                                <td>Rp. <span id="cabang_total_debit_bca"></span></td>
                            </tr>
                            <tr>
                                <td>* QR Code</td>
                                <td>Rp. <span id="cabang_total_qr"></span></td>
                            </tr>
                            <tr>
                                <td>* BRI</td>
                                <td>Rp. <span id="cabang_total_debit_bri"></span></td>
                            </tr>
                            <tr>
                                <td>* BNI</td>
                                <td>Rp. <span id="cabang_total_debit_bni"></span></td>
                            </tr>
                            <tr>
                                <td>* MANDIRI</td>
                                <td>Rp. <span id="cabang_total_debit_mandiri"></span></td>
                            </tr>
                            <tr>
                                <td>* M-Banking / Transfer</td>
                                <td>Rp. <span id="cabang_total_transfer"></span></td>
                            </tr>
                            <tr>
                                <td><b>Setoran</b></td>
                                <td><b>Rp. <span id="cabang_total_cash"></span></b></td>
                            </tr>
                            <tr id="cross_order_row">
                                <td><a class="btn btn-sm btn-info" id="cabang_cross_order_detail">Cross Order</a></td>
                                <td><b>Rp. <span id="cabang_cross_order"></span></b></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-border table-striped">
                        <tbody>
                            <tr>
                                <td>Diverifikasi</td>
                                <td>Disetujui</td>
                                <td>Diketahui</td>
                            </tr>
                            <tr>
                                <td>
                                    <pre>



                                    </pre>
                                </td>
                                <td>
                                    <pre>


                                    
                                    </pre>
                                </td>
                                <td>
                                    <pre>


                                    
                                    </pre>
                                </td>
                            </tr>
                            <tr>
                                <td>Finance</td>
                                <td>Head/Kasir</td>
                                <td>Head Sales and Marketing</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <a class="btn btn-success float-right" href="javascript:cabangPdf()">Download PDF</a>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="CrossModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Cross Order Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <a class="btn btn-primary ml-auto mr-2" data-type="cross" id="export_btn">Export Excel</a><br/>
                <table class="table table-hover table-checkable" id="CrossReporttb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Tanggal</th>
                            <th class="text-dark">Invoice</th>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">Artikel</th>
                            <th class="text-dark">Kategori</th>
                            <th class="text-dark" style="white-space:nowrap;">Sub Kategori</th>
                            <th class="text-dark" style="white-space:nowrap;">Sub Sub Kategori</th>
                            <th class="text-dark">Warna</th>
                            <th class="text-dark">Size</th>
                            <th class="text-dark">Qty</th>
                            <th class="text-dark">Bandrol</th>
                            <th class="text-dark" style="white-space:nowrap;">Harga Jual</th>
                            <th class="text-dark" style="white-space:nowrap;">Total Harga</th>
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
<div class="modal fade" id="HBHJModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Data Artikel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <input type="search" class="form-control" id="hbhj_search" placeholder="Cari artikel"/>
                <table class="table table-hover table-checkable" id="HBHJtb">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Brand</th>
                            <th class="text-dark">Artikel</th>
                            <th class="text-dark">Warna</th>
                            <th class="text-dark">Size</th>
                            <th class="text-dark">SubKategori</th>
                            <th class="text-dark">Harga Beli</th>
                            <th class="text-dark">Harga Jual</th>
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
<?php /**PATH /home/dev.jez.co.id/public_html/resources/views/app/report/sales_report/sales_report_modal.blade.php ENDPATH**/ ?>