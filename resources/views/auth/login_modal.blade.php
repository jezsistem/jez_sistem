<!-- Modal-->
<div class="modal fade" id="InputCodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id ="f_access">
            <input type="hidden" name="_type" id="_type" value="" />
            <div class="modal-header btn-inventory">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Input Kode</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-10">
                        <label for="exampleTextarea">Input Kode Akses Anda</label>
                        <input type="password" class="form-control" id="u_secret_code" name="u_secret_code" autocomplete="off" required/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-inventory font-weight-bold">Enter</button>
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
            <div class="modal-header btn-inventory">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Cari Artikel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-10">
                        <label for="exampleTextarea">Input Nama Artikel</label>
                        <input type="text" class="form-control" id="article_name" name="article_name" required/>
                        <div id="articleList"></div>
                    </div>
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
            <div class="modal-header btn-inventory">
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
<div class="modal fade" id="TrackingTypeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <input type="hidden" name="_sc_code" id="_sc_code" value="" />
            <div class="modal-header btn-inventory">
                <h5 class="modal-title text-dark" id="exampleModalLabel">AKTIFITAS BARANG</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-10">
                        <a class="btn btn-lg btn-inventory form-control" id="in_btn">MASUK</a>
                    </div>
                    <div class="form-group mb-1 pb-10">
                        <a class="btn btn-lg btn-inventory form-control" id="out_btn">KELUAR</a>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <a class="btn btn-lg btn-inventory form-control" id="invoice_btn">BY INVOICE</a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Batal</button>
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
            <div class="modal-header btn-inventory">
                <h5 class="modal-title text-dark" id="exampleModalLabel"><span id="scanned-QR"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="container" id="QR-Code">
                    <div class="navbar-form">
                        <div class="form-group mb-1 pb-1">
							<select class="form-control" id="invoice" name="invoice">
								<option value="">PILIH JIKA SCANNER ERROR</option>
								@foreach ($data['invoice'] as $key => $value)
									<option value="{{ $key }}">{{ $value }}</option>
								@endforeach
							</select>
							<div id="invoice_parent"></div>
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
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header btn-inventory">
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
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header btn-inventory">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Barang Masuk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                    <table class="table table-hover table-checkable" id="Intb">
                        <thead class="btn btn-inventory text-light">
                            <tr>
                                <th class="text-dark">No</th>
                                <th class="text-dark">Produk</th>
                                <th class="text-dark">Status</th>
                                <th class="text-dark">Qty</th>
                                <th class="text-dark">BIN Tujuan</th>
                                <th class="text-dark">Tindakan</th>
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
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header btn-inventory">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Barang Keluar</h5>
            </div>
            <div class="modal-body">
                <!-- <select class="form-control" id="pl_id_out" name="pl_id_out" required>
                    <option value="">- Kode Bin -</option>
                    <option value="pickup">PICKUP OFFLINE</option>
                    @foreach ($data['pl_id'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select> -->
                <div id="pl_id_out_parent"></div>
                <div class="card-body table-responsive" id="out_content">
                    <table class="table table-hover table-checkable" id="Outtb">
                        <thead class="btn btn-inventory text-light">
                            <tr>
                                <th class="text-dark">No</th>
                                <th class="text-dark">Produk</th>
                                <th class="text-dark">BIN</th>
                                <th class="text-dark">Status</th>
                                <th class="text-dark">Qty</th>
                                <th class="text-dark">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="out_modal_finish">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->