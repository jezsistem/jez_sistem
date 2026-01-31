<!-- Modal-->
<div class="modal fade" id="PurchaseOrderModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
    data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <div class="modal-dialog modal-xl" role="document" style="width:100%; max-width:1300px;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Purchase Order (PO) #<span
                        id="po_invoice_label"></span></h5>
                <!--begin::Button Import-->
                <div class="mr-2">
                    <div class="dropdown dropdown-inline mr-2">
                        <a type="button" class="btn btn-light-primary font-weight-bolder" id="ExportArticleData">
                            <span class="svg-icon svg-icon-md">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
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
                            </span>Export</a>
                    </div>
                    <div class="dropdown dropdown-inline mr-2">
                        <a type="button" class="btn btn-light-primary font-weight-bolder" id="ImportModalBtn"
                            aria-haspopup="true" aria-expanded="false">
                            <span class="svg-icon svg-icon-md">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
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
                            </span>Import</a>
                    </div>
                    <div class="dropdown dropdown-inline mr-10">
                        <a type="button" class="btn btn-light-info font-weight-bolder" id="ChangeLogBtn">
                            <span class="svg-icon svg-icon-md">
                                <!--begin::Svg Icon | path:assets/media/svg/icons/General/Notifications2.svg-->
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path
                                            d="M13.2070325,4 C13.0721672,4.47683179 13,4.97998812 13,5.5 C13,8.53756612 15.4624339,11 18.5,11 C19.0200119,11 19.5231682,10.9278328 20,10.7929675 L20,17 C20,18.6568542 18.6568542,20 17,20 L7,20 C5.34314575,20 4,18.6568542 4,17 L4,7 C4,5.34314575 5.34314575,4 7,4 L13.2070325,4 Z"
                                            fill="#000000" />
                                        <circle fill="#000000" opacity="0.3" cx="18.5" cy="5.5" r="2.5" />
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>Change Log</a>
                    </div>
                    <button type="button" class="close close_modal_po" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                    <!--begin::Dropdown Menu-->
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                        <!--begin::Navigation-->
                        <ul class="navi flex-column navi-hover py-2">
                            <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
                                Bentuk File :</li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-icon">
                                        <i class="la la-copy"></i>
                                    </span>
                                    <span id="purchase_order_excel_btn"></span>
                                </a>
                            </li>
                        </ul>
                        <!--end::Navigation-->
                    </div>
                    <!--end::Dropdown Menu-->
                </div>
                <!--end::Dropdown-->
            </div>
            <form id="f_po">
                <input type="hidden" id="_mode" name="_mode" />
                <input type="hidden" id="_po_id" name="_po_id" />
                <div class="modal-body">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-4">
                            <label>Store</label>
                            <select class="form-control" id="st_id" name="st_id" required>
                                <option value="">- Pilih Store -</option>
                                @foreach ($data['st_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="st_id_parent"></div>
                        </div>
                        <div class="col-4">
                            <label>Supplier</label>
                            <select class="form-control" id="ps_id" name="ps_id" required>
                                <option value="">- Pilih Supplier -</option>
                                @foreach ($data['ps_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="ps_id_parent"></div>
                        </div>
                        <div class="col-4">
                            <label>Deskripsi / Catatan</label>
                            <textarea class="form-control" placeholder="Deskripsi / Catatan" name="po_description" id="po_description"
                                rows="3"></textarea>
                        </div>
                        <div class="col-4">
                            <label>Tipe Stok</label>
                            <select class="form-control" id="stkt_id" name="stkt_id" required>
                                <option value="">- Pilih Tipe Stok -</option>
                                @foreach ($data['stkt_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="stkt_id_parent"></div>
                        </div>
                        <div class="col-4">
                            <label>Ongkos Kirim</label>
                            <input type="number" class="form-control" placeholder="Ongkos Kirim"
                                name="shipping_cost" id="shipping_cost" required />
                        </div>
                        <div class="col-2 mt-2">
                            <label>Gambar Invoice</label>
                            <div class="mr-2">
                                <div class="dropdown dropdown-inline mr-2">
                                    <a type="button" class="btn btn-light-primary font-weight-bolder"
                                        id="UploadImageInvoiceBtn" aria-haspopup="true" aria-expanded="false">
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
                                        </span>Upload Gambar</a>
                                </div>
                                <!--begin::Dropdown Menu-->
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <!--begin::Navigation-->
                                    <ul class="navi flex-column navi-hover py-2">
                                        <li
                                            class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
                                            Bentuk File :</li>
                                        <li class="navi-item">
                                            <a href="#" class="navi-link">
                                                <span class="navi-icon">
                                                    <i class="la la-copy"></i>
                                                </span>
                                                <span id="purchase_order_excel_btn"></span>
                                            </a>
                                        </li>
                                    </ul>
                                    <!--end::Navigation-->
                                </div>
                                <!--end::Dropdown Menu-->
                            </div>
                        </div>
                        <div class="col-2 mt-2">
                            <label>Gambar Transfer</label>
                            <div class="mr-2">
                                <div class="dropdown dropdown-inline mr-2">
                                    <a type="button" class="btn btn-light-primary font-weight-bolder"
                                        id="UploadImageTransferBtn" aria-haspopup="true" aria-expanded="false">
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
                                        </span>Upload Gambar</a>
                                </div>
                                <!--begin::Dropdown Menu-->
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <!--begin::Navigation-->
                                    <ul class="navi flex-column navi-hover py-2">
                                        <li
                                            class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
                                            Bentuk File :</li>
                                        <li class="navi-item">
                                            <a href="#" class="navi-link">
                                                <span class="navi-icon">
                                                    <i class="la la-copy"></i>
                                                </span>
                                                <span id="purchase_order_excel_btn"></span>
                                            </a>
                                        </li>
                                    </ul>
                                    <!--end::Navigation-->
                                </div>
                                <!--end::Dropdown Menu-->
                            </div>
                        </div>
                        <div class="col-4 mt-5">
                            <label>Pajak</label>
                            <select class="form-control" id="tax_id" name="tax_id" required>
                                <option value="">- Pajak -</option>
                                @foreach ($data['tax_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="tax_id_parent"></div>
                        </div>

                        <div class="col-4 mt-5">
                            <label>Data Perusahaan</label>
                            <select class="form-control" id="dp_id" name="dp_id" required>
                                <option value="">- Data Perusahaan -</option>
                                @foreach ($data['dp_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="dp_id_parent"></div>
                        </div>
                        <div class="col-2 mt-5">
                            <label>Payment</label>
                            <select class="form-control" id="acc_id" name="acc_id" required>
                                <option value="">- Payment -</option>
                                @foreach ($data['acc_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="acc_id_parent"></div>
                        </div>
                        <div class="col-2 mt-5">
                            <label>Bank General</label>
                            <select class="form-control" id="bank_general" name="bank_general">
                                <option value="">- Bank General -</option>
                                <option value="BCA 002">BCA 002</option>
                                <option value="BCA 004">BCA 004</option>
                                <option value="BCA 005">BCA 005</option>
                            </select>
                        </div>
                        <div class="col-4 mt-5">
                            <label>Dispute</label>
                            <input type="text" class="form-control" name="dispute" id="dispute" disabled />
                        </div>
                        <div class="col-4 mt-5">
                            <label>Pre Order</label>
                            <select class="form-control" id="pro_id" name="pro_id">
                                <option value="">- Pre Order -</option>
                                @foreach ($data['pro_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="pro_id_parent"></div>
                        </div>
                        <div class="col-4 mt-5">
                            <div class="row">
                                <div class="col-6">
                                    <label for="pay_date">Tanggal Bayar</label>
                                    <input type="date" id="pay_date" class="form-control"
                                        max="{{ date('Y-m-d') }}" />
                                </div>
                                <div class="col-6">
                                    <label for="due_date">Tanggal Jatuh Tempo</label>
                                    <input type="date" id="due_date" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="col-4 mt-5">
                            <label>Status Dispute</label>
                            <select class="form-control" name="status_dispute" id="status_dispute" required>
                                <option value="">- Pilih Status -</option>
                                <option value="1">Progress</option>
                                <option value="0">Closed</option>
                            </select>
                        </div>
                        <div class="col-4 mt-5">
                            <label>Dispute</label>
                            <textarea class="form-control" placeholder="Deskripsi / Catatan" name="dispute_description" id="dispute_description"
                                rows="3"></textarea>
                        </div>
                        {{-- <div class="col-4 mt-5"></div> --}}
                        <div class="col-4 mt-3 d-flex flex-column">
                            <label class="badge badge-primary">Bukti Gambar Invoice dan Paket </label>
                            <div class="row justify-content-start">
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
                                <a class="input-group col-5" type="button" id="BuktitfImagesBtn"
                                    aria-haspopup="true" aria-expanded="false">
                                    <label class="input-group-text" for="buktitfImage">
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
                                        Bukti Transfer
                                    </label>
                                </a>
                                <a class="input-group col-5 mt-2" type="button" id="FinanceAttachmentBtn"
                                    aria-haspopup="true" aria-expanded="false">
                                    <label class="input-group-text" for="financeAttachment">
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
                                        Lampiran Finance
                                    </label>
                                </a>
                            </div>
                        </div>
                        <div class="col-4 mt-5 ">
                            <label>Klaim Lebih/Piutang</label>
                            <select class="form-control" name="is_receivable" id="is_receivable">
                                <option value="">- Pilih -</option>
                                <option value="0">Tidak</option>
                                <option value="1">Ya</option>
                            </select>
                        </div>
                        <div class="col-4 mt-5 ">
                            <label>Nominal Klaim</label>
                            <input type="number" class="form-control " placeholder="Nominal Klaim"
                                name="claim_amount" id="claim_amount" min="0" />
                        </div>
                        <div class="col-4 mt-5 " title="(Nominal Klaim + Nominal Payment - Total PO)">
                            <label>Sisa Payment</label>
                            <input type="number" class="form-control " placeholder="Sisa Payment"
                                id="remaining_payment" readonly />
                        </div>
                        <div class="col-4 mt-5 without_item_input">
                            <label>Total Pembelian</label>
                            <input type="number" class="form-control " placeholder="Total Pembelian"
                                name="total_purchase" id="total_purchase" min="0" />
                        </div>
                        <div class="col-4 mt-5 without_item_input">
                            <label>Total Quantity</label>
                            <input type="number" class="form-control " placeholder="Total Quantity"
                                name="total_qty" id="total_qty" min="0" />
                        </div>
                        <div class="col-4 mt-5 without_item_input">
                            <label>Nominal Payment</label>
                            <input type="number" class="form-control " placeholder="Nominal Payment"
                                name="payment_amount" id="payment_amount" min="0" />
                        </div>
                        <div class="col-4 mt-5 without_item_input">
                            <label>Nominal Adjustment</label>
                            <input type="number" class="form-control " placeholder="Nominal Adjustment"
                                name="adjustment_amount" id="adjustment_amount" />
                        </div>
                    </div>

                    <!--end::Row-->
                    <!--begin::Row-->
                    <div class="row mt-4" id="detail_po">
                        <!--begin::Button-->
                        <div class="col-2 mb-2 mt-4">
                            <a href="#" class="btn-sm btn-primary font-weight-bolder" id="add_product_btn">
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
                                </span>Tambah Produk</a>
                        </div>
                        <div class="col-12" id="purchase_order_detail_content"></div>
                    </div>
                    <!--end::Row-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-danger font-weight-bold" style="margin-right: 20px"
                        id="cancel_purchase_order_btn">Hapus PO</button>
                    <button type="button" class="btn btn-dark font-weight-bold close_modal_po"
                        id="save_purchase_order_btn" disabled>Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<div class="modal fade" id="AddProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Tambah Produk</h5>
            </div>
            <div class="modal-body">
                <div class="card-header flex-wrap py-1">
                    <div class="form-group row">
                        <div class="col-lg-4 pt-1">
                            <select class="form-control" id="br_id_filter_item" name="br_id_filter_item" required>
                                <option value="">- Brand/All -</option>
                                @foreach ($data['br_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="br_id_filter_parent_item"></div>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <select class="form-control" id="mc_id_filter_item" name="mc_id_filter_item" required>
                                <option value="">- Warna/All -</option>
                                @foreach ($data['mc_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="mc_id_filter_parent_item"></div>
                        </div>
                        <div class="col-lg-4 pt-1">
                            <select class="form-control" id="psc_id_filter_item" name="psc_id_filter_item" required>
                                <option value="">- Sub Kategori -</option>
                                @foreach ($data['psc_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div id="psc_id_filter_parent_item"></div>
                        </div>
                        <!-- <div class="col-lg-4 pt-1">
                            <select class="form-control" id="sz_id_filter_item" name="sz_id_filter_item" required>
                                <option value="">- Size/All -</option>
                                @foreach ($data['sz_id'] as $key => $value)
<option value="{{ $key }}">{{ $value }}</option>
@endforeach
                            </select>
                            <div id="sz_id_filter_parent_item"></div>
                        </div> -->
                    </div>
                </div>
                <div class="table-responsive">
                    <!--begin: Datatable-->
                    <center><input type="search" class="form-control  col-6" id="product_search"
                            placeholder="Cari nama produk / warna / brand / supplier" /></center><br />
                    <table class="table table-hover table-checkable" id="Producttb">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">No</th>
                                <th style="white-space: nowrap;" class="text-dark">Nama</th>
                                <th style="white-space: nowrap;" class="text-dark">Article ID</th>
                                <th style="white-space: nowrap;" class="text-dark">Warna</th>
                                <th style="white-space: nowrap;" class="text-dark">Brand</th>
                                <th style="white-space: nowrap;" class="text-dark">Size</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <!--end: Datatable-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark font-weight-bold" id="add_item_btn">Selesai</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Modal-->
<form id="f_import" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Import Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Download Template
                                <span class="text-danger">*</span></label>
                            <a href="{{ asset('upload/template/po_pembelian_template.xlsx') }}"
                                class="btn btn-xs btn-primary">Download</a>
                        </div>
                        <div class="form-group">
                            <label>Pilih template yang sudah diisi data</label>
                            <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="importFile" id="importFile" required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /Modal -->

<!-- Modal-->
<form id="f_upload_invoice_image" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="UploadImageInvoiceModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Upload Gambar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Pilih Gambar Invoice</label>
                            <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="imageInvoices[]" id="imageInvoices"
                                multiple required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold"
                        id="upload_image_invoice_btn">Upload</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /Modal -->

<!-- Modal-->
<form id="f_upload_transfer_image" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="UploadImageTransferModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="exampleModalLabel">Upload Gambar</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Pilih Gambar Transfer</label>
                            <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="imageTransfers[]" id="imageTransfers"
                                multiple required />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold"
                        id="upload_image_transfer_btn">Upload</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /Modal -->
<!-- Modal-->
<div class="modal fade" id="InvoiceImagesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Invoice Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="container">
                        <table id="InvoiceImagesTb" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                    data-dismiss="modal">Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal-->

<!-- Modal Bukti TF-->
<div class="modal fade" id="BuktitfImagesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Bukti Transfer Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="container">
                        <table id="BuktitfImagesTb" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" id="close_import_btn"
                    data-dismiss="modal">Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal Bukti TF-->

<!-- Modal Change Log-->
<div class="modal fade" id="ChangeLogModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 90%;">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Purchase Order Change Log</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="PurchaseOrderLogTb">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">No</th>
                                <th class="text-dark">Tanggal</th>
                                <th class="text-dark">User</th>
                                <th class="text-dark">Tipe</th>
                                <th class="text-dark">Item</th>
                                <th class="text-dark">Kolom</th>
                                <th class="text-dark">Sebelum</th>
                                <th class="text-dark">Sesudah</th>
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
            </div>
        </div>
    </div>
</div>
<!-- /Modal Change Log-->

<!-- Modal Finance Attachment-->
<div class="modal fade" id="FinanceAttachmentModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Lampiran Finance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="container mb-4">
                        <!-- Add Attachment Button -->
                        <button type="button" class="btn btn-primary" id="AddFinanceAttachmentBtn"
                            data-toggle="modal" data-target="#AddFinanceAttachmentInputModal">
                            Tambah Lampiran
                        </button>
                    </div>
                    <div class="container">
                        <table id="FinanceAttachmentTb" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>File</th>
                                    <th>Catatan</th>
                                    <th>Tanggal Upload</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold"
                    id="close_finance_attachment_btn" data-dismiss="modal">Tutup
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal Finance Attachment-->

<!-- Modal Add Finance Attachment Input -->
<form id="f_add_finance_attachment" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="AddFinanceAttachmentInputModal" tabindex="-1" role="dialog"
        aria-labelledby="AddFinanceAttachmentInputModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-dark" id="AddFinanceAttachmentInputModalLabel">Tambah Lampiran Finance
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="po_id_finance_attachment" id="po_id_finance_attachment" />
                    <div class="form-group">
                        <label>Pilih File Lampiran</label>
                        <span class="text-danger">*</span>
                        <input type="file" class="form-control" name="finance_attachment_file"
                            id="finance_attachment_file" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi / Catatan</label>
                        <textarea class="form-control" name="finance_attachment_description" id="finance_attachment_description"
                            rows="3" placeholder="Deskripsi / Catatan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold"
                        id="save_finance_attachment_btn">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /Modal Add Finance Attachment Input -->
