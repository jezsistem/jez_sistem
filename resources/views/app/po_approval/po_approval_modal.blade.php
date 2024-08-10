<!-- Modal-->
<div class="modal fade" id="ApproveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="width: 100%; max-width: 1300px;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">#<span id="invoice_label"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                {{-- show total harga beli and notes --}}
                <div class="row">
                    <div class="col-4">
                        <label class="badge badge-primary">Total</label >
                        <label class="badge badge-secondari" id="total_approval_price"></label>
                    </div>
                        
                </div>
                <form id="f_po" enctype="multipart/form-data">
                    <input type="hidden" id="_mode" name="_mode" />
                    <input type="hidden" id="_po_id" name="_po_id" />
                    <div class="modal-body">
                        <!--begin::Row-->
                        <div class="row">
                            <div class="col-4">
                                <label>Store</label>
                                <select class="form-control" id="st_id" name="st_id" required disabled>
                                    <option value="">- Pilih Store -</option>
                                    @foreach ($data['st_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="st_id_parent"></div>
                            </div>
                            <div class="col-4">
                                <label>Supplier</label>
                                <select class="form-control" id="ps_id" name="ps_id" required disabled>
                                    <option value="">- Pilih Supplier -</option>
                                    @foreach ($data['ps_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="ps_id_parent"></div>
                            </div>
                            <div class="col-4">
                                <label>Deskripsi</label>
                                <textarea class="form-control" name="po_description" id="po_description" rows="3"></textarea>
                            </div>
                            <div class="col-4">
                                <label>Tipe Stok * otomatis dari master PO jika diisi oleh tim terkait</label>
                                <select class="form-control" id="stkt_id" name="stkt_id" required disabled>
                                    <option value="">- Pilih Tipe Stok -</option>
                                    @foreach ($data['stkt_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="stkt_id_parent"></div>
                            </div>
                            <div class="col-4">
                                <label>Pajak</label>
                                <select class="form-control" id="tax_id" name="tax_id" required disabled>
                                    <option value="">- Pajak -</option>
                                    @foreach ($data['tax_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <div id="tax_id_parent"></div>
                            </div>
                            <div class="col-4">
                                <label>Tanggal Terima</label>
                                <input type="date" id="receive_date" class="form-control" value="" />
                            </div>
                            <div class="col-4 mt-4">
                                <label class="badge badge-primary">Invoice</label>
                                <input type="text" id="receive_invoice" class="form-control" value=""
                                       placeholder="INVxx" />
                            </div>
                            <div class="col-4 mt-4">
                                <label class="badge badge-primary">Tanggal Invoice</label>
                                <input type="date" id="invoice_date" class="form-control" value="" />
                            </div>
                            <div class="col-4 mt-4 d-flex flex-column">
                                <label class="badge badge-primary">Bukti Gambar Invoice dan Paket</label>
                                <div class="row  justify-content-between">
                                    <a class="input-group col-5" type="button" id="InvoiceImagesBtn"
                                       aria-haspopup="true" aria-expanded="false">
                                        <label class="input-group-text" for="invoiceImage">
                                        <span class="svg-icon svg-icon-md">
                                            <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                 height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none"
                                                   fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path
                                                            d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                            fill="#000000" opacity="0.3" />
                                                    <path
                                                            d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                            fill="#000000" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                            Invoice
                                        </label>
                                    </a>
                                    <a class="input-group col-5" type="button" id="SuratJalanBtn" aria-haspopup="true"
                                       aria-expanded="false">
                                        <label class="input-group-text" for="suratJalan">
                                        <span class="svg-icon svg-icon-md">
                                            <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                 height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none"
                                                   fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <path
                                                            d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                            fill="#000000" opacity="0.3" />
                                                    <path
                                                            d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                            fill="#000000" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                            Surat Jalan
                                        </label>
                                    </a>
                                    <div class="mt-2">
                                        <a class="input-group col-5" type="button" id="SuratJalanImageBtn"
                                           aria-haspopup="true" aria-expanded="false">
                                            <label class="input-group-text" for="suratJalan">
                                            <span class="svg-icon svg-icon-md">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                     height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                       fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                                d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                                fill="#000000" opacity="0.3" />
                                                        <path
                                                                d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                                fill="#000000" />
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span>
                                                Gambar Surat Jalan
                                            </label>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <label>Ongkos Kirim</label>
                                <br />
                                <label> * diisi setelah mengisi kolom terima </label>
                                <input type="number" id="shipping_cost" class="form-control" name="shipping_cost"
                                       onchange="updateCogs()" required />
                            </div>

                        </div>
                        <br>
                        <!--end::Row-->
                        <!--begin::Row-->
                        <table class="table table-responsive table-hover" id="APDtb">
                            <thead class="bg-primary">
                            <tr>
                                <th class="text-white">No</th>
                                <th class="text-white">Tanggal Terima</th>
                                <th class="text-white">Invoice</th>
                                <th class="text-white">SKU</th>
                                <th class="text-white">Brand</th>
                                <th class="text-white">Artikel</th>
                                <th class="text-white">Warna</th>
                                <th class="text-white">Size</th>
                                <th class="text-white">Tipe</th>
                                <th class="text-white">Qty Terima</th>
                                <th class="text-white">In Stock</th>
                                <th class="text-white">Harga Beli</th>
                                <th class="text-white">Total</th>
                                <th class="text-white"></th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <!--end::Row-->
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-dark font-weight-bold" id="save_purchase_order_btn">Tutup
                        </button>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold"
                    data-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-dark font-weight-bold" id="approve_btn">Approve</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->
