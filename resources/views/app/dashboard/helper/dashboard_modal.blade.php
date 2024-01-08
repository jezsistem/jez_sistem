<!-- Modal-->
<div class="modal fade" id="InputCodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id ="f_access">
            <input type="hidden" name="_type" id="_type" value="" />
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Input Kode</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-10">
                        <label for="exampleTextarea">Input Kode Akses Anda</label>
                        <input type="password" class="form-control" id="u_secret_code" name="u_secret_code" value="{{ $data['user']->u_secret_code }}" autocomplete="off" required/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn bg-dark font-weight-bold">Enter</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id ="f_article">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Data Artikel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                <input type="search" class="form-control form-control-sm" id="stock_data_search" placeholder="Cari produk / warna / brand / size" style="border:1px solid black; padding:20px;"/><br/>
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
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ShowArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Detail Produk</h5>
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
<div class="modal fade" id="PackingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Tipe Aktifitas</h5>
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
<div class="modal fade" id="ScannerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-dark" id="exampleModalLabel"><span id="scanned-QR"></span></h5>
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
                            <button title="Decode Image" class="btn btn-default btn-sm" id="decode-img" type="hidden" data-toggle="tooltip"><span class="glyphicon glyphicon-upload"></span></button>
                            <button title="Image shoot" class="btn btn-info btn-sm disabled" id="grab-img" type="hidden" data-toggle="tooltip"><span class="glyphicon glyphicon-picture"></span></button>
                            </span>
                            <center>
                            <button title="Play" class="btn btn-success btn-lg d-none" id="play" type="button" data-toggle="tooltip"><i class="fas fa-play"></i></button>
                            <button title="Pause" class="btn btn-warning btn-lg d-none" id="pause" type="button" data-toggle="tooltip"><i class="fas fa-pause"></i></button>
                            <button title="Stop streams" class="btn btn-danger btn-lg d-none" id="stop" type="button" data-toggle="tooltip"><i class="fas fa-stop"></i></button>
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
                </div><br/>
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
<div class="modal fade" id="OrderListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Produk No Pesanan <span id="invoice_number"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="orderListItem"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Selesai</button>
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
                <h5 class="modal-title text-dark" id="exampleModalLabel">Barang Masuk</h5>
            </div>
            <div class="modal-body table-responsive">
                <select class="form-control form-control-sm bg-info text-white" id="waiting_filter">
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
<div class="modal fade" id="OutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Barang Keluar</h5>
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
<div class="modal fade" id="TransferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <input type="hidden" id="_transfer_mode"/>
                <input type="hidden" id="transfer_invoice_label" value=""/>
                <h5 class="modal-title text-dark" id="exampleModalLabel">Transfer</h5>
            </div>
            <div class="modal-body">
                <div class="form-group mb-1 pb-1">
                    <span class="transfer_invoice"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="transfer_modal_finish">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="TransferDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Transfer <span id="transfer_invoice_modal_label"></span></h5>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <input type="search" class="form-control form-control-sm col-12" id="transfer_search" placeholder="Brand-Artikel-Warna-Size"/><br/>
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
                <button type="button" class="btn btn-dark font-weight-bold" id="transfer_detail_modal_finish">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="TakeCrossOrderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Ambil Barang</h5>
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
<div class="modal fade" id="ScanOutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Barang Keluar</h5>
            </div>
            <div class="modal-body table-responsive">
                <div id="pl_id_out_parent"></div>
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
<div class="modal fade" id="ScanInModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Barang Masuk</h5>
            </div>
            <div class="modal-body table-responsive">
                <select class="form-control form-control-sm bg-info text-white" id="waiting_filter">
                    <option value='WAITING OFFLINE'>Waiting Offline</option>
                    <option value='WAITING ONLINE'>Waiting Online</option>
                    <option value='REFUND'>Refund</option>
                    <option value='EXCHANGE'>Exchange</option>
                </select><br/>
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
<div class="modal fade" id="ScanTransferModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <input type="hidden" id="_scan_transfer_mode"/>
                <input type="hidden" id="scan_transfer_invoice_label" value=""/>
                <h5 class="modal-title text-dark" id="ScanExampleModalLabel">Transfer</h5>
            </div>
            <div class="modal-body">
                <div class="form-group mb-1 pb-1">
                    <span class="scan_transfer_invoice"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="scan_transfer_modal_finish">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ScanTransferDetailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Transfer <span id="scan_transfer_invoice_modal_label"></span></h5>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <input type="search" class="form-control form-control-sm col-12" id="scan_transfer_search" placeholder="Brand-Artikel-Warna-Size"/><br/>
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
                <button type="button" class="btn btn-dark font-weight-bold" id="scan_transfer_detail_modal_finish">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->