<!-- Modal-->
<div class="modal fade" id="AdjustmentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="f_adjustment">
                <input type="hidden" id="_pl_id" value="" />
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Adjustment [<span
                            id="adjustment_label"></span>]</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-header flex-wrap py-3">
                        <div class="card-toolbar">
                            <!--begin::Button-->
                            <a href="#" class="btn btn-dark font-weight-bolder" id="add_article_btn">
                                <span class="svg-icon svg-icon-md">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <circle fill="#000000" cx="9" cy="15" r="6" />
                                            <path
                                                d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z"
                                                fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>Tambah Artikel</a>
                            <!--end::Button-->
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <input type="search" class="form-control  col-6" id="article_search"
                            placeholder="Cari nama artikel / warna / brand" />
                        <table class="table table-hover table-checkable" id="Articletb">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th class="text-dark">No</th>
                                    <th class="text-dark">Article</th>
                                    <th class="text-dark">Size Stock Barcode</th>
                                    <th class="text-dark" style="white-space: nowrap;">Qty SO</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold"
                        id="save_adjustment_btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="AddArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="f_article">
                @csrf
                <input type="hidden" name="pst_id_hidden" id="pst_id_hidden" value="" />
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Tambah Artikel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Artikel <span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-dark col-12" id="product_name_input"
                            placeholder="nama size warna brand">
                        <div id="itemList"></div>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Qty <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pls_qty" name="pls_qty" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="article_note">Note <span class="text-danger">*</span></label>
                        <select class="form-control" id="article_note" name="article_note" required>
                            <option value="">-- Pilih Note Adjustment --</option>
                            <option value="STOCK OPNAME">STOCK OPNAME</option>
                            <option value="PARTIAL">PARTIAL</option>
                            <option value="REJECT">REJECT</option>
                            <option value="CACAT">CACAT</option>
                            <option value="PERBAIKAN">PERBAIKAN</option>
                            <option value="PROMOSI">PROMOSI</option>
                            <option value="OPERASIONAL">OPERASIONAL</option>
                            <option value="SSR">SSR</option>
                            <option value="RESELLER">RESELLER</option>
                            <option value="KESALAHAN SYSTEM">KESALAHAN SYSTEM</option>
                            <option value="CYCLE COUNT">CYCLE COUNT</option>
                            <option value="RETUR IN">RETUR IN</option>
                            <option value="RETUR OUT">RETUR OUT</option>
                            <option value="MARKETPLACE IN">MARKETPLACE IN</option>
                        </select>
                    </div>

                    <div class="form-group mb-1 pb-1 d-none" id="bukti_kesalahan_group">
                        <label>Bukti Kesalahan System <span class="text-danger">*</span></label>
                        <input type="file" name="proof_file" id="proof_file" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold"
                        id="save_add_article_btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ActionAdjustmentModal" tabindex="-1" role="dialog"
    aria-labelledby="approvalModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <input type="hidden" name="ba_id" id="ba_id" value="" />
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold" id="acctionModalLabel">
                    Detail Penyesuaian Stok
                </h5>
                <button type="button" id="close_modal_1" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close" id="icon_close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><strong>Kode:</strong> <span id="adj_code">-</span></span>
                        <span class="badge bg-warning text-dark">-</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div><strong>Dibuat oleh:</strong> <span id="adj_creator">-</span></div>
                    <div><strong>Tanggal:</strong> <span id="adj_create_date">-</span></div>
                </div>
                <div class="mb-3">
                    <div><strong>Disetujui oleh:</strong> <span id="adj_approval">-</span></div>
                    <div><strong>Tanggal:</strong> <span id="adj_approve_date">-</span></div>
                </div>
                <div class="mb-3">
                    <div><strong>Dieksekusi oleh:</strong> <span id="adj_execute">-</span></div>
                    <div><strong>Tanggal:</strong> <span id="adj_execute_date">-</span></div>
                </div>
                <table class="table table-bordered mb-5">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Kuantitas Awal</th>
                            <th class="text-center">Kuantitas Baru</th>
                            <th class="text-center">Penyesuaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="qty_awal" class="text-center">-</td>
                            <td id="qty_baru" class="text-center">-</td>
                            <td id="qty_adj" class="text-center">-</td>
                        </tr>
                    </tbody>
                </table>
                <div class="mb-5">
                    <h6 class="fw-bold">Detail Produk</h6>

                    <div class="mb-2">
                        <div><strong>Nama:</strong> <span id="product_name">-</span></div>
                        <div><strong>Brand:</strong> <span id="product_brand">-</span></div>
                        <div><strong>SKU:</strong> <span id="product_sku">-</span></div>
                        <div><strong>Warna:</strong> <span id="product_color"></span>
                        </div>
                        <div><strong>Size:</strong> <span id="product_size"></span></div>
                        <div><strong>COGS:</strong> <span id="cogs"></span></div>
                    </div>
                </div>
                <div class="mb-5">
                    <h6 class="fw-bold">Lokasi</h6>
                    <div><strong>Gudang:</strong> <span id="warehouse">-</span></div>
                    <div><strong>BIN:</strong> <span id="bin">-</span></div>
                </div>
                <div class="mb-5">
                    <h6 class="fw-bold">Catatan/Alasan dari Pembuat</h6>
                    <div id="adj_note">-</div>
                </div>
                <div class="mb-3 mt-5 justify-content-end d-none" id="btns_approval">
                    <button type="button" class="btn btn-outline-danger mx-2" id="btn_reject_adj">Tolak</button>
                    <button type="button" class="btn btn-success mx-2" id="btn_approve_adj">Setujui</button>
                    <button type="button" class="btn btn-secondary mx-2" data-dismiss="modal"
                        id="close_modal_approval">Tutup</button>
                </div>
                <div class="mb-3 mt-5 justify-content-end d-none" id="btns_execution">
                    <button type="button" class="btn btn-outline-danger mx-2" id="btn_cancel_adj">Batal</button>
                    <button type="button" class="btn btn-success mx-2" id="btn_exec_adj">Eksekusi</button>
                    <button type="button" class="btn btn-secondary mx-2" data-dismiss="modal"
                        id="close_modal_exec">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
