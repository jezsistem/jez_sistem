
<!-- Modal-->
<div class="modal fade" id="ArtikelPromoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_artikel_promo">
                @csrf
                <input type="hidden" name="_id" id="_id" value="" />
                <input type="hidden" name="_mode" id="_mode" value="" />
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Artikel Promo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                    <div class="modal-body">
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Artikel Name*</label>
                            <input type="text" class="form-control" id="p_id" name="p_id" required />
                        </div>
                        <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Store <span class="text-danger">*</span></label>
{{--                        <select class="form-control" id="st_id" name="st_id" required>--}}
{{--                            <option value="">- Pilih -</option>--}}
{{--                            @foreach ($data['st_id'] as $key => $value)--}}
{{--                                <option value="{{ $key }}">{{ $value }}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
                        <div id="st_id_parent"></div>
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Start Date</label>
                            <input type="date" class="form-control" id="date_start" name="date_start" />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">End Date*</label>
                            <input type="date" class="form-control" id="date_end" name="date_end" required />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Promo Chategory*</label>
                            <input type="text" class="form-control" id="promo_cat" name="promo_cat" required />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Harga Promo*</label>
                            <input type="text" class="form-control" id="promo_price" name="promo_price" required />
                        </div>
                        <div class="form-group mb-1 pb-1">
                            <label for="exampleTextarea">Note Promo*</label>
                            <input type="text" class="form-control" id="promo_note" name="promo_note" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-danger font-weight-bold" id="delete_data_perusahaan_btn"
                        style="display:none;">Hapus</button>
                    <button type="submit" class="btn btn-dark font-weight-bold"
                        id="save_artikel_promo_btn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->
