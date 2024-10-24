<!-- Modal-->
<div class="modal fade" id="TopdealsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_topdeals">
            @csrf
            <input type="hidden" name="_id" id="_id" value="" />
            <input type="hidden" name="_mode" id="_mode" value="" />
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Topdeal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama Topdeals</label>
                        <input type="text" class="form-control" id="td_name" name="td_name" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Tanggal Berakhir</label>
                        <input type="date" class="form-control" id="td_due_date" name="td_due_date" required>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Jam Berakhir</label>
                        <input type="text" class="form-control" id="td_due_time" name="td_due_time" required>
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Status</label>
                        <select class="form-control" id="td_status" name="td_status" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger font-weight-bold" id="delete_topdeals_btn" style="display:none;">Hapus</button>
                <button type="submit" class="btn btn-primary font-weight-bold" id="save_topdeals_btn">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="ArticleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Topdeals Artikel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-header flex-wrap py-3">
                    <div class="card-toolbar">
                        <a href="#" class="btn btn-primary font-weight-bolder" id="add_topdeals_article_btn">
                        <span class="svg-icon svg-icon-md">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <circle fill="#000000" cx="9" cy="15" r="6" />
                                    <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>Data Baru</a>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <input type="hidden" id="_td_id" value=""/>
                    <table class="table table-hover table-checkable" id="TopdealsArticletb">
                        <thead class="bg-primary text-light">
                            <th class="text-light">No</th>
                            <th class="text-light">Brand</th>
                            <th class="text-light">Artikel</th>
                            <th class="text-light">Warna</th>
                            <th class="text-light"></th>
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
<div class="modal fade" id="ArticleListModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light" id="exampleModalLabel">Data Artikel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <input type="search" class="form-control form-control-sm col-6" id="article_list_search" placeholder="Cari article"/><br/>
                    <table class="table table-hover table-checkable" id="ArticleListtb">
                        <thead class="bg-primary text-light">
                            <th class="text-light">No</th>
                            <th class="text-light">Brand</th>
                            <th class="text-light">Artikel</th>
                            <th class="text-light">Warna</th>
                            <th class="text-light"></th>
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