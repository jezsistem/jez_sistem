<!-- Modal-->
<div class="modal fade" id="dataStokModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Data Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="search" class="form-control" id="stock_data_search"
                       placeholder="Ketik 3 huruf pertama nama artikel atau SKU Atau scan"
                       style="border:1px solid black; padding:20px; background:#efefef;"/><br/>
                <table class="table table-hover table-checkable table-responsive" id="StockDatatb">
                    <thead class="bg-light text-dark">
                    <tr>
                        <th class="text-dark">Data Stok Tersedia</th>
                        <th class="text-dark">Variant</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="InputCodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="f_access">
                <input type="hidden" name="_type" id="_type" value=""/>
                <div class="modal-header bg-dark">
                    <h5 class="modal-title text-light" id="exampleModalLabel">Input Kode</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group mb-1 pb-10">
                            <label for="exampleTextarea">Input Kode Akses Anda</label>
                            <input type="password" class="form-control" id="u_secret_code" name="u_secret_code"
                                   value="{{ $data['user']->u_secret_code }}" autocomplete="off" required/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal
                    </button>
                    <button type="submit" class="btn bg-dark font-weight-bold">Enter</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="f_article">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title text-light" id="exampleModalLabel">Data Artikel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <input type="search" class="form-control form-control-sm" id="stock_data_search"
                               placeholder="Cari produk / warna / brand / size"
                               style="border:1px solid black; padding:20px;"/><br/>
                        <table class="table table-hover table-checkable" id="HelperStockDatatb">
                            <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">Artikel</th>
                                <th class="text-dark"></th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ShowArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Detail Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="article_detail_content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="PackingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-light" id="exampleModalLabel">Tipe Aktifitas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ScannerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel"><span id="scanned-QR"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container" id="QR-Code">
                    <div class="navbar-form">
                        <div class="form-group mb-1 pb-1">
                            <span class="invoice_list"></span>
                        </div>
                        <select class="form-control" id="camera-select"></select>
                        <div class="form-group">
                            <span style="display:none;">
                            <button title="Decode Image" class="btn btn-default btn-sm" id="decode-img" type="hidden"
                                    data-toggle="tooltip"><span class="glyphicon glyphicon-upload"></span></button>
                            <button title="Image shoot" class="btn btn-info btn-sm disabled" id="grab-img" type="hidden"
                                    data-toggle="tooltip"><span class="glyphicon glyphicon-picture"></span></button>
                            </span>
                            <center>
                                <button title="Play" class="btn btn-success btn-lg d-none" id="play" type="button"
                                        data-toggle="tooltip"><i class="fas fa-play"></i></button>
                                <button title="Pause" class="btn btn-warning btn-lg d-none" id="pause" type="button"
                                        data-toggle="tooltip"><i class="fas fa-pause"></i></button>
                                <button title="Stop streams" class="btn btn-danger btn-lg d-none" id="stop"
                                        type="button" data-toggle="tooltip"><i class="fas fa-stop"></i></button>
                            </center>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="well" style="position: relative;display: inline-block; border:1px solid black;">
                                <canvas width="320" height="240" id="webcodecam-canvas"></canvas>
                                <div class="scanner-laser laser-rightBottom" style="opacity: 0.5;"></div>
                                <div class="scanner-laser laser-rightTop" style="opacity: 0.5;"></div>
                                <div class="scanner-laser laser-leftBottom" style="opacity: 0.5;"></div>
                                <div class="scanner-laser laser-leftTop" style="opacity: 0.5;"></div>
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <div class="thumbnail" id="result">
                                <div class="well" style="overflow: hidden;">
                                    <img width="320" height="240" id="scanned-img" src="">
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
                <br/>
                <input type="text" id="scanned-result" class="form-control"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="OrderListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Produk No Pesanan <span
                            id="invoice_number"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="orderListItem"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Selesai
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="InModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Barang Masuk</h5>
            </div>
            <div class="modal-body table-responsive">
                <select class="form-control form-control-sm bg-info text-white" id="waiting_filter_masuk">
                    <option value='WAITING OFFLINE'>Waiting Offline</option>
                    <option value='WAITING ONLINE'>Waiting Online</option>
                    <option value='REFUND'>Refund</option>
                    <option value='EXCHANGE'>Exchange</option>
                </select><br/>
                <input type="search" class="form-control" id="in_search" placeholder="Cari brand artikel"/>
                <table class="table table-hover table-checkable table-striped" id="Intb">
                    <thead class="bg-dark text-light">
                    <tr>
                        <th class="text-dark">Artikel</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="in_modal_finish">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="OutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Barang Keluar</h5>
            </div>
            <div class="modal-body table-responsive">
                <div id="pl_id_out_parent"></div>
                <input type="search" class="form-control" id="out_search" placeholder="Cari brand artikel"/>
                <table class="table table-hover table-checkable table-striped" id="Outtb">
                    <thead class="bg-dark text-light">
                    <tr>
                        <th class="text-dark">Artikel</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="out_modal_finish">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="TransferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <input type="hidden" id="_transfer_mode"/>
                <input type="hidden" id="transfer_invoice_label" value=""/>
                <h5 class="modal-title text-light" id="exampleModalLabel">Transfer</h5>
            </div>
            <div class="modal-body">
                <div class="form-group mb-1 pb-1">
                    <span class="transfer_invoice"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="transfer_modal_finish" data-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="TransferDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Transfer <span
                            id="transfer_invoice_modal_label"></span></h5>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <input type="search" class="form-control form-control-sm col-12" id="transfer_search"
                           placeholder="Brand-Artikel-Warna-Size"/><br/>
                    <table class="table table-hover table-checkable" id="TransferListtb">
                        <thead class="bg-dark text-light">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Artikel</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="transfer_detail_modal_finish" data-dismiss="modal">Selesai
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="TakeCrossOrderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Ambil Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="cross_invoice_content"></div>
                <div class="table-responsive">
                    <table class="table table-hover table-checkable d-none" id="TakeCrossOrdertb">
                        <thead class="bg-inventory text-light">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Artikel</th>
                            <th class="text-dark">Qty</th>
                            <th class="text-dark">BIN</th>
                            <th class="text-dark">Tindakan</th>
                        </tr>
                        </thead>
                        <tbody>

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
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ScanOutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Barang Keluar</h5>
            </div>
            <div class="modal-body table-responsive">

                <input type="search" class="form-control" id="scan_out_search" placeholder="Cari brand artikel"/>
                <table class="table table-hover table-checkable table-striped" id="ScanOuttb">
                    <thead class="bg-dark text-light">
                    <tr>
                        <th class="text-dark">Artikel</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="scan_out_modal_finish">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ScanInModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Scan Barang Masuk</h5>
            </div>
            <div class="modal-body table-responsive">
                <div class="row">
                    <div class="col-md-9">
                        <select class="form-control form-control-sm bg-info text-white" id="waiting_filter">
                            <option value='WAITING OFFLINE'>Waiting Offline</option>
                            <option value='WAITING ONLINE'>Waiting Online</option>
                            <option value='WAITING FOR CHECKOUT'>Waiting For Checkout</option>
                            <option value='REFUND'>Refund</option>
                            <option value='EXCHANGE'>Exchange</option>
                        </select>
                        <input type="hidden" id="filter_status" value="">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary btn-sm" id="btn_filter">Submit</button>
                    </div>
                </div>
                <br>
                <div class="d-flex justify-content-center">
                    <div>
                        <div id="reader_scan_in" class="rounded"></div>
                        <div id="result"></div>
                    </div>
                </div>
                <input type="search" class="form-control" id="scan_in_search" placeholder="Cari brand artikel"/>
                <table class="table table-hover table-checkable table-striped" id="ScanIntb">
                    <thead class="bg-dark text-light">
                    <tr>
                        <th class="text-dark">Artikel</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="scan_in_modal_finish">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="ScanInRefundModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Scan Barang Masuk Refund</h5>
            </div>
            <div class="modal-body table-responsive">
                <div class="row">
                    <div class="col-md-9">
                        <select class="form-control form-control-sm bg-info text-white" id="waiting_refund_filter">
                            <option value='REFUND'>Refund</option>
                        </select>
                        <input type="hidden" id="filter_status" value="">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary btn-sm" id="btn_filter">Submit</button>
                    </div>
                </div>
                <br>
                <div class="d-flex justify-content-center">
                    <div>
                        <div id="reader_scan_in_refund" class="rounded"></div>
                        <div id="result"></div>
                    </div>
                </div>
                <input type="search" class="form-control" id="scan_in_refund_search" placeholder="Cari brand artikel"/>
                <table class="table table-hover table-checkable table-striped" id="ScanInRefundtb">
                    <thead class="bg-dark text-light">
                    <tr>
                        <th class="text-dark">Artikel</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="scan_in_refund_modal_finish">Selesai
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="KeepOnModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Scan SKU Keep Online</h5>
            </div>
            <div class="modal-body table-responsive">
                <select class="form-control form-control-sm bg-info text-white" id="waiting_filter_online">
                    <option value='WAITING ONLINE'>Waiting Online</option>
                </select><br/>
                <input type="search" class="form-control" id="scan_in_search" placeholder="Scan SKU" autofocus
                       autocomplete="off"/>
                <table class="table table-hover table-checkable table-striped" id="ScanInOnlinetb">
                    <thead class="bg-dark text-light">
                    <tr>
                        <th class="text-dark">Artikel</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="scan_in_modal_finish">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ScanTransferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <input type="hidden" id="_scan_transfer_mode"/>
                <input type="hidden" id="scan_transfer_invoice_label" value=""/>
                <h5 class="modal-title text-light" id="ScanExampleModalLabel">Transfer</h5>
            </div>
            <div class="modal-body">
                <div class="form-group mb-1 pb-1">
                    <span class="scan_transfer_invoice"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="scan_transfer_modal_finish" data-dismiss="modal">Selesai
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ScanTransferDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Transfer <span
                            id="scan_transfer_invoice_modal_label"></span></h5>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <input type="search" class="form-control form-control-sm col-12" id="scan_transfer_search"
                           placeholder="Brand-Artikel-Warna-Size"/><br/>
                    <table class="table table-hover table-checkable" id="ScanTransferListtb">
                        <thead class="bg-dark text-light">
                        <tr>
                            <th class="text-dark">No</th>
                            <th class="text-dark">Artikel</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="scan_transfer_detail_modal_finish" data-dismiss="modal">
                    Selesai
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<style>
    .modal-backdrop.blur {
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px); /* untuk Safari */
        background-color: rgba(0, 0, 0, 0.3);
        transition: backdrop-filter 0.3s ease;
    }
</style>

<div class="modal fade" id="binModal" tabindex="-1" role="dialog" aria-labelledby="binModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih BIN <span id="product_name"></span>-<span id="plst_id"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="pl_id_out_parent"></div>
                <br>
                <div class="d-flex justify-content-center">
                    <div>
                        <div id="reader_scan_bin_out" class="rounded" style="max-width: 500px;"></div>
                        <div id="result"></div>
                    </div>
                </div>

                <span style="margin-top: 20px;">Bin Set : </span><br>
                <span>SKU : </span><span id="sku_selected"></span>
                <input type="hidden" id="sku_send">
{{--                <input type="text" id="" value="">--}}
{{--                <input type="text" id="" value="">--}}
{{--                <input type="text" id="" value="">--}}
{{--                <input type="text" id="" value="">--}}
                <input type="search" class="form-control mt-3" id="bin_out_search" placeholder="Cari nama bin"/><br>
                <table class="table table-bordered" id="binTable">
                    <thead>
                    <tr>
                        <th>BIN</th>
                        <th>QTY</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Data BIN akan diisi di sini -->
                    </tbody>
                </table>
                <div class="text-right mt-3">
                    {{--                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>--}}
                    <button type="button" class="btn btn-dark font-weight-bold" data-dismiss="modal">Selesai</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="PickOnModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-light" id="exampleModalLabel">Scan SKU Pick Online</h5>
            </div>
            <br>
            <div class="d-flex justify-content-center">
                <div>
                    <div id="reader_pick_online" class="rounded mr-2 ml-2"></div>
                    <div id="result"></div>
                </div>
            </div>
            <div class="modal-body table-responsive">
                <select class="form-control form-control-sm bg-info text-white" id="waiting_filter_online">
                    <option value='WAITING ONLINE'>Waiting Online</option>
                </select><br/>
                <input type="search" class="form-control" id="scan_pick_on_search" placeholder="Scan SKU" autofocus
                       autocomplete="off"/>
                <table class="table table-hover table-checkable table-striped" id="ScanInOnlinetb">
                    <thead class="bg-dark text-light">
                    <tr>
                        <th class="text-dark">Artikel</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="scan_in_modal_finish">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="TakeTransferItemModal" tabindex="-1" role="dialog" aria-labelledby="TakeTransferItemModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <input type="hidden" id="_stfd_id"/>
        <div class="modal-content">
            <div class="modal-header bg-dark">
            <h5 class="modal-title text-light" id="TakeTransferItemModalLabel">Ambil Barang Transfer</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                <i aria-hidden="true" class="ki ki-close"></i>
            </button>
            </div>
            <div class="modal-body">
            <!-- Info section: Bin, SKU, Qty requested -->
            <div class="alert alert-danger mb-3" id="take_transfer_info">
                <strong>Item:</strong> <span id="take_transfer_p_name_info">-</span>
            </div>
            <div class="alert alert-info mb-3" id="take_transfer_info">
                <strong>Ambil dari BIN:</strong> <span id="take_transfer_bin_info">-</span> &nbsp;|&nbsp;
                <strong>SKU:</strong> <span id="take_transfer_sku_info">-</span> &nbsp;|&nbsp;
                <strong>Qty Diminta:</strong> <span id="take_transfer_qty_info">-</span>
            </div>
            <div class="d-flex justify-content-center">
                <div>
                    <div id="reader_take_transfer" class="rounded"></div>
                    <div id="result"></div>
                </div>
            </div>
            <form id="take_transfer_item_form" class="form-inline mb-3 mt-3">
                <div class="form-group mr-2 mb-2">
                <input type="text" class="form-control" id="take_transfer_bin" placeholder="BIN" autocomplete="off" required>
                </div>
                <div class="form-group mr-2 mb-2">
                <input type="text" class="form-control" id="take_transfer_barcode" placeholder="Barcode" autocomplete="off" required>
                </div>
                <input type="hidden" id="take_transfer_quantity" value=1>
                <button type="submit" class="btn btn-dark mb-2" id="btn_submit_scan_item_transfer">Submit</button>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered" id="TakeTransferItemTable">
                <thead class="bg-light text-dark">
                    <tr>
                    <th>BIN</th>
                    <th>SKU</th>
                    <th>Qty</th>
                    <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be appended here -->
                </tbody>
                </table>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success font-weight-bold" id="btn_take_transfer_item">Ambil</button>
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>