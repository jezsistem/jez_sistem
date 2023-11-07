<!-- Modal-->
<div class="modal fade" id="BINCustom" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">BIN Terkait Custom</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
            @if ($data['sa_custom'] == '1')
                @if (!empty($data['bin_custom']))
                @foreach ($data['bin_custom'] as $row)
                    <a class="btn btn-sm btn-primary bin_custom mr-2 mb-2" data-id="{{ $row->id }}">{{ $row->pl_code }}</a>
                @endforeach
                @endif
            @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="NotFoundBarcode" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="f_not_found">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Barcode Tidak Ditemukan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-1 pb-1">
                    <label for="exampleTextarea">Barcode</label>
                    <input type="text" class="form-control" id="not_found_barcode" name="not_found_barcode" value="" readonly/>
                </div>
                <div class="form-group mb-1 pb-1">
                    <label for="exampleTextarea btn-inventory">Tentukan Artikel Untuk Barcode Ini</label>
                    <input type="text" class="form-control" id="not_found_article" name="not_found_article" value=""/>
                    <input type="hidden" id="not_found_pst_id" name="not_found_pst_id"/>
                    <div id="notFoundList"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="save_barcode">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->
