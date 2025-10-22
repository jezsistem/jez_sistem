<!-- Modal-->
<div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
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
                            {{-- <a href="{{ asset('upload/template/data_supplier_template.xlsx') }}" class="btn btn-xs btn-primary">Download</a> --}}
                            <div class="dropdown dropdown-inline mr-2">
                                <button type="button" class="btn btn-light-danger font-weight-bolder dropdown-toggle"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="svg-icon svg-icon-md">
                                    </span>Download
                                </button>
                                <!--begin::Dropdown Menu-->
                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                    <!--begin::Navigation-->
                                    <ul class="navi flex-column navi-hover py-2">
                                        <li
                                            class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
                                            Template File :
                                        </li>
                                        <li class="navi-item" id="download_template_shopee">
                                            <a class="navi-link">
                                                <span class="navi-icon">
                                                    <i class="la la-download"></i>
                                                </span>
                                                <span>Shopee</span>
                                            </a>
                                        </li>
                                        <li class="navi-item">
                                            <a class="navi-link" id="download_template_tiktok">
                                                <span class="navi-icon">
                                                    <i class="la la-download"></i>
                                                </span>
                                                <span>Tiktok</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <!--end::Navigation-->
                                </div>
                                <!--end::Dropdown Menu-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Pilih template yang sudah di download dan diisi
                                <span class="text-danger">*</span></label>
                            <input type="hidden" class="form-control"
                                value="{{ \Illuminate\Support\Facades\Auth::user()->st_id }}" name="st_id_form"
                                id="st_id_form" disabled>
                            <input type="file" class="form-control" name="importFile" id="importFile" required />
                        </div>
                    </div>
                </div>



                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Tutup
                    </button>
                    <button type="submit" class="btn btn-dark font-weight-bold" id="import_data_btn">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal-->
<div class="modal fade" id="ImportResiModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="splitForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import & Split Resi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="pdf_file">Pilih File PDF Resi</label>
                            <input type="file" class="form-control" id="pdf_file" name="pdf_file" accept=".pdf"
                                required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="uploadBtn" class="btn btn-primary mt-3">Upload & Split</button>
                        <button type="button" class="btn btn-info mt-3" id="historyBtn">Upload History</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Riwayat -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="historyModalLabel">📜 Riwayat Upload Split Resi</h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body" id="historyContent">
                <div class="text-center p-4">
                    <div class="spinner-border text-info"></div>
                    <p class="mt-2">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay"
    style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    z-index:1050;
    text-align:center;
    color:white;
">
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
        <div class="spinner-border text-light" style="width:3rem; height:3rem;" role="status"></div>
        <p class="mt-3 font-weight-bold">Sedang memproses... mohon tunggu</p>
    </div>
</div>




<!-- Modal-->
<div class="modal fade" id="DetailModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
    data-backdrop="static" data-keyboard="false" style="overflow-y: auto;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Detail Item Pesanan #<span
                        id="num_order"></span></h5>
                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body table-responsive">
                    <!--begin: Datatable-->
                    <input type="hidden" id="to_id" value="" />
                    <input type="hidden" id="status_pesanan" value="" />

                    <button class="btn btn-light-primary font-weight-bolder" id="print_invoice">
                            <span class="svg-icon svg-icon-md">
                                <!--begin::Svg Icon | Print Icon-->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24"
                                     version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M6,9 L18,9 C19.1045695,9 20,9.8954305 20,11 L20,17 C20,18.1045695 19.1045695,19 18,19 L6,19 C4.8954305,19 4,18.1045695 4,17 L4,11 C4,9.8954305 4.8954305,9 6,9 Z"
                                              fill="#000000"/>
                                        <path d="M8,2 L16,2 C17.1045695,2 18,2.8954305 18,4 L18,8 L6,8 L6,4 C6,2.8954305 6.8954305,2 8,2 Z M10,6 L14,6 C14.5522847,6 15,5.55228475 15,5 C15,4.44771525 14.5522847,4 14,4 L10,4 C9.44771525,4 9,4.44771525 9,5 C9,5.55228475 9.44771525,6 10,6 Z"
                                              fill="#000000" opacity="0.3"/>
                                    </g>
                                </svg>
                                <!--end::Svg Icon-->
                            </span>
                        Print
                    </button>

                    <table class="table table-hover to mt-3" id="Detailtb">
                        <div class="alert-danger running-text"
                            style="padding: 25px; border-radius:10px; margin-top:10px; margin-bottom:10px;"
                            role="alert">
                            <h3> Important!! Harap perhatikan status pick sebelum cetak nota 🐈🐈</h3>
                        </div>
                        <button type="button" class="btn btn-success font-weight-bold mb-3" id="add_new_item_btn">
                            <i class="fas fa-plus"></i> Tambah Item
                        </button>
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">No</th>
                                <th class="text-dark">Artikel</th>
                                <th class="text-dark">SKU</th>
                                <th class="text-dark">SKU MP</th>
                                <th class="text-dark">Qty</th>
                                <th class="text-dark">Platform Price</th>
                                <th class="text-dark">Jez Price</th>
                                <th class="text-dark">Diff Price</th>
                                <th class="text-dark">Discount Seller</th>
                                <th class="text-dark">N.S Before Admin</th>
                                <th class="text-dark">Final Price</th>
                                <th class="text-dark">Warehouse</th>
                                <th class="text-dark">Status Pick</th>
                                <th class="text-dark">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <!--end: Datatable-->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold close-modal"
                    data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal-->
<div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Tambah Item Pesanan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="add_item_to_id" value="" />
                <div class="form-group mb-1 pb-1">
                    <label for="exampleTextarea">Nomer Pesanan</label>
                    <input type="text" class="form-control" id="no_pesanan" name="no_pesanan" required
                        disabled />
                </div>
                <div class="form-group mb-1 pb-1">
                    <label for="exampleTextarea">Berat*</label>
                    <input type="number" class="form-control" id="pssc_weight" name="pssc_weight" required />
                </div>
                <div class="form-group mb-1 pb-1">
                    <label for="exampleTextarea">Deskripsi</label>
                    <input type="text" class="form-control" id="pssc_description" name="pssc_description" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold">Tutup</button>
                <button type="button" class="btn btn-dark font-weight-bold">Tambah</button>
            </div>
        </div>
    </div>
</div>
<!-- /Modal -->


<!-- Modal-->
<div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="exampleModalLabel">Tambah Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="f_customer">
                <div class="modal-body">
                    <input type="hidden" id="add_item_to_id" value="" />
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nomor Telp</label>
                        <input type="number" class="form-control" id="nomor_telp" name="nomor_telp" required />
                    </div>
                    <div class="form-group mb-1 pb-1">
                        <label for="exampleTextarea">Nama Customer</label>
                        <input type="text" class="form-control" id="nama_customer" name="nama_customer" required
                            disabled />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold">Tutup</button>
                    <button type="button" class="btn btn-dark font-weight-bold">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal -->

<!-- Chat Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white" id="chatModalLabel">
                    Chat TRX : <span class="trx_number_title">No TRX</span>
                </h5>
                <button type="button" class="close text-danger" onclick="closeChat()" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body p-0">
                <!-- Chat Messages Container -->
                <div class="chat-container">
                    <div class="chat-messages p-3">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Chat Input -->
                <div class="input-group w-100">
                    <input type="text" class="form-control" id="text_input" placeholder="Type your message..."
                        onkeypress="if(event.keyCode==13){ sendChatMessage(); }">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button" onclick="sendChatMessage()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Item -->

<div class="modal fade" id="tambahItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="tambahItemModalLabel">Tambah Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="f_tambah_item">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="sku_input">SKU <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sku_input" name="sku" required
                            placeholder="Masukkan SKU" />
                    </div>
                    <div class="form-group mb-3">
                        <label for="item_sejenis_select">Item Sejenis pada TRX <span
                                class="text-danger">*</span></label>
                        <select class="form-control" id="item_sejenis_select" name="item_sejenis" required>
                            <option value="">Pilih Item Sejenis</option>
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="qty_input">Qty <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="qty_input" name="qty" required
                            min="1" step="1" placeholder="Masukkan Qty" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal Tambah Item -->

<!-- Modal Edit Item -->

<div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title text-dark" id="editItemModalLabel">Edit Qty Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <form id="f_edit_item">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <input type="hidden" name="edit_item_otd_id" id="edit_item_otd_id" value="">
                        <input type="hidden" name="edit_item_to_id" id="edit_item_to_id" value="">
                        <label for="edit_item_qty">Qty <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_item_qty" name="qty" required
                            min="1" step="1" placeholder="Masukkan Qty" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold"
                        data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-dark font-weight-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Modal Tambah Item -->

<style>
    .chat-container {
        height: 70vh;
        background-color: #f8f9fa;
    }

    .chat-messages {
        height: 100%;
        overflow-y: auto;
    }

    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #dee2e6;
        border-radius: 3px;
    }
</style>

<style>
    /* Global table styling */
    .table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Arial', sans-serif;
        font-size: 12px;
        color: #333;
        text-align: left;
        margin-bottom: 20px;
    }

    /* Table header styling */
    .table thead {
        background-color: #fff0f4;
        color: #333;
    }

    .table thead th {
        padding: 12px 15px;
        border-bottom: 2px solid #000000;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 14px;
        /* font-weight: bold; */
    }

    /* Table body styling */
    .table tbody tr {
        border-bottom: 1px solid #f0f0f0;
    }

    .table tbody tr:nth-child(even) {
        background-color: #fff0f4;
    }

    .table tbody tr:hover {
        color: rgb(255, 0, 0);
        transition: all 0.3s ease;
    }

    /* Table cell padding */
    .table td {
        padding: 10px 15px;
        border-bottom: 1px solid #FFEDD3;
    }

    /* Final column adjustments */
    .table tbody tr td:last-child {
        font-weight: bold;
    }

    /* Hover effect on the table rows */
    .table-hover tbody tr:hover td {
        color: rgb(255, 0, 0);
    }

    .text-dark {
        color: #333 !important;
    }

    /* Responsive design */
    @media screen and (max-width: 768px) {
        .table {
            font-size: 14px;
        }
    }
</style>
