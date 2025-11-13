<div class="modal fade" id="addStorageAreaModal" tabindex="-1" aria-labelledby="addStorageAreaModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStorageAreaModalLabel">Tambah Area Penyimpanan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="addStorageAreaForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{--<div class="modal fade" id="pickHistoryModal" tabindex="-1" role="dialog">--}}
{{--    <div class="modal-dialog modal-dialog-centered" role="document">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h5 class="modal-title">Pick History</h5>--}}
{{--                <button type="button" class="close" data-dismiss="modal">&times;</button>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}
{{--                <p><strong>Request By:</strong> <span id="requestBy">-</span></p>--}}
{{--                <p><strong>Pick By:</strong> <span id="pickBy">-</span></p>--}}
{{--                <p><strong>Packing By:</strong> <span id="packingBy">-</span></p>--}}
{{--                <p><strong>Pick Time:</strong> <span id="pickTime">-</span></p>--}}
{{--                <p><strong>Pack Time:</strong> <span id="packTime">-</span></p>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}


<div class="modal fade" id="pickHistoryModal" tabindex="-1" role="dialog" aria-labelledby="pickHistoryModalLabel"
     aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Pick History Timeline</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="timeline" id="pickHistoryTimeline"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>

        </div>
    </div>
</div>




<style>
    .timeline {
        position: relative;
        margin: 20px 0;
        padding-left: 40px;
        border-left: 3px solid #007bff;
    }
    .timeline-item {
        margin-bottom: 30px;
        position: relative;
    }
    .timeline-item::before {
        content: "";
        position: absolute;
        left: -19px;
        top: 0;
        width: 15px;
        height: 15px;
        background-color: #007bff;
        border-radius: 50%;
    }
    .timeline-item .title {
        font-weight: bold;
        color: #007bff;
    }
    .timeline-item .name {
        font-size: 16px;
        color: #333;
    }
    .timeline-item .time {
        font-size: 14px;
        color: #666;
    }
</style>

<div class="modal fade" id="detailStorageAreaModal" tabindex="-1" aria-labelledby="detailStorageAreaModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <input type="hidden" id="storage_area_id" name="storage_area_id">
            <div class="modal-header">
                <h5 class="modal-title" id="detail_name"></h5>
                <div>
                    <button type="button" class="btn btn-dark me-2 mr-3" id="editStorageAreaBtn">Edit Area
                        Penyimpanan
                    </button>
                    <button type="button" class="btn btn-danger me-2 mr-10" id="deleteStorageAreaBtn">Hapus Area
                        Penyimpanan
                    </button>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="search" class="form-label">List Bin Pada Area Ini</label>
                    <div id="bin_list" class="border min-h-50px"></div>
                </div>
                <div class="mb-3">
                    <label for="search" class="form-label">List Bin Yang Akan Ditambahkan</label>
                    <div id="bin_list_temp" class="border min-h-50px"></div>
                    <button type="button" class="btn btn-primary mt-2" id="add_bin_to_area">Save Changes</button>
                </div>
                <div class="mb-3">
                    <label for="search" class="form-label">List Bin Belum Setup</label>
                    <div id="bin_list_no_area"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editStorageAreaModal" tabindex="-1" aria-labelledby="editStorageAreaModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStorageAreaModalLabel">Edit Area Penyimpanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="editStorageAreaForm">
                @csrf
                <input type="hidden" id="edit_storage_area_id" name="storage_area_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="edit_name" name="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_description" name="edit_description" rows="3"
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update Area</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Online Items Modal -->
<div class="modal fade" id="OnlineItemsModal" tabindex="-1" aria-labelledby="OnlineItemsModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="OnlineItemsModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body table-responsive">
                <input type="search" class="form-control" id="scan_out_search" placeholder="Cari brand artikel"/>
                <table class="table table-hover table-checkable table-striped" id="online_items_table">
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        aria-label="Close">Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="binModal" tabindex="-1" role="dialog" aria-labelledby="binModalLabel"
     aria-hidden="true" data-backdrop="static" data-keyboard="false">
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
                        <div id="reader_scan_bin_out" class="rounded" style="max-width: 500px; min-width:300px">
                        </div>
                        <div id="result"></div>
                    </div>
                </div>

                <span>SKU : </span><span id="sku_selected"></span>
                <input type="hidden" id="sku_send">
                {{--                <input type="text" id="" value=""> --}}
                {{--                <input type="text" id="" value=""> --}}
                {{--                <input type="text" id="" value=""> --}}
                {{--                <input type="text" id="" value=""> --}}
                <input type="search" class="form-control mt-3" id="bin_out_search"
                       placeholder="Cari nama bin"/><br>
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
                    {{--                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button> --}}
                    <button type="button" class="btn btn-dark font-weight-bold close_scanner"
                            id="close_scan_out_modal" data-dismiss="modal">Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="waitingReceiptModal" tabindex="-1" role="dialog"
     aria-labelledby="waitingReceiptModallLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white" id="waitingReceiptModallLabel">
                    <span class="trx_number_title_wr" id="trx_number_title_wr">No TRX</span> -
                    <span class="to_id_waiting_receipt" id="to_id_waiting_receipt">ID</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body p-0">
                <!-- Waiting Receipt Messages Container -->
                <div class="container-fluid p-4">
                    <div class="mb-5 mt-5 d-flex justify-content-between align-items-center">
                        <div>
                            <button type="button" class="btn btn-info me-2" id="printResiBtn">
                                <i class="fas fa-print"></i> Print Resi
                            </button>
                            <button type="button" class="btn btn-warning" id="printNotaBtn">
                                <i class="fas fa-print"></i> Print Nota
                            </button>
                            <input type="hidden" id="resi_number">
                        </div>
                        <button type="button" class="btn btn-success" id="continuePackingBtn" data-to_id=""
                                data-order_number="" disabled>
                            Lanjut Packing
                        </button>
                    </div>
                    <div class="table-responsive mt-5">
                        <table class="table table-bordered" id="waitingReceiptTable">
                            <thead class="">
                            <tr>
                                <th>No</th>
                                <th>Artikel</th>
                                <th>SKU</th>
                                <th>Qty</th>
                                <th>Platform Price</th>
                                <th>Jez Price</th>
                                <th>Seller Discount</th>
                                <th>Final Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Data will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal Scan Packing --}}

<div class="modal fade" id="scanPackingModal" tabindex="-1" role="dialog" aria-labelledby="scanPackingModalLabel"
     aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan Packing: <span id="order_number_scan_packing"></span> - <span
                            id="plst_id_scan_packing"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="pl_id_out_parent"></div>
                <br>
                <div class="d-flex justify-content-center">
                    <div>
                        <div id="reader_scan_resi" class="rounded" style="max-width: 500px; min-width:300px">
                        </div>
                        <div id="result"></div>
                    </div>
                </div>
                <p class="mt-8">No Resi : </span><span id="resi_number_holder"></p>
                <input type="search" class="form-control mt-3" id="scan_packing_result"
                       placeholder="Hasil Scan"/><br>
                <div class="text-right mt-3">
                    {{--                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button> --}}
                    <button type="button" class="btn btn-dark font-weight-bold close_scanner"
                            id="close_scan_packing_modal_btn">Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal-->
<div class="modal fade" id="importManifestModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document"> <!-- Ubah ke modal-xl -->
        <div class="modal-content">

            <form id="f_upload_manifest" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Scan Manifest</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <!-- Tombol Add -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <a href="{{ route('add_delivery_recap') }}" class="btn btn-success btn-sm" target="_blank">
                                <i class="fas fa-plus"></i> Add Delivery Recap
                            </a>
                        </div>

                        <hr>

                        <!-- Tabel hasil import -->
                        <div class="table-responsive">
                            <table id="manifestTable" class="table table-bordered table-striped w-100">
                                <thead class="bg-light text-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Document Number</th>
                                    <th>Courier</th>
                                    <th>Number Phone</th>
                                    <th>Expeditions</th>
                                    <th>Qty Resi</th>
                                    <th>PIC</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="resiMasalModal" tabindex="-1" role="dialog" aria-labelledby="resiMasalModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">Pilih Resi untuk Print</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- 🔹 Tombol Select All -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <input type="checkbox" id="selectAllResi">
                        <label for="selectAllResi" class="ml-1 mb-0 font-weight-bold">Pilih Semua</label>
                    </div>

                </div>

                <div class="row" id="resiCardList">
                    {{-- Jika data dari controller --}}
                    @if(!empty($dataResi) && count($dataResi) > 0)
                        @foreach($dataResi as $item)
                            @php
                                $resi = $item->no_resi ?? '';
                                $nama_barang = $item->nama_barang ?? '';
                                $jumlah = $item->jumlah ?? 0;
                                $jumlah_barang =  $item->jumlah_barang ?? 0;
                            @endphp

                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm border-primary resi-card">
                                    <div class="row no-gutters align-items-center">
                                        <!-- Ganti gambar jadi angka besar -->
                                        <div class="col-md-4 d-flex align-items-center justify-content-center bg-light">
                                            <div class="text-center">
                                <span class="display-3 font-weight-bold text-primary">
                                    {{ $jumlah_barang }}
                                </span>
                                                <div class="text-muted">pcs</div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">{{ $resi }}</h5>
                                                    <input type="checkbox" class="resi-checkbox" value="{{ $resi }}">
                                                </div>
                                                <p class="card-text mt-2 mb-1">{{ $nama_barang }}</p>
                                                <small class="text-muted">Jumlah: {{ $jumlah }} pcs</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center text-muted py-5">
                            <p>Belum ada data resi tersedia.</p>
                        </div>
                    @endif
                </div>
                <button type="button" id="printSelected" class="btn btn-success btn-sm col-12" style="height: 5rem;">
                    Merge Resi Terpilih
                </button>


                <!-- Loading GIF -->
                <div id="loadingMerge" class="text-center mt-3" style="display: none;">
                    <img src="{{ asset('pos/jez.gif') }}" alt="Loading..." width="120">
                    <p class="text-muted mt-2">Menggabungkan file PDF, mohon tunggu...</p>
                </div>

                <!-- Preview hasil merge -->
                <div id="mergedPdfPreview" class="mt-3" style="display: none;">
                    <h6>Preview Hasil Merge:</h6>
                    <iframe id="mergedPdfFrame" style="width: 100%; height: 500px; border: 1px solid #ccc;"></iframe>

                    <button id="printMergedBtn" class="btn btn-primary mt-3 col-12" style="height: 4rem;">
                        Print Hasil Merge
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>




<!-- Modal -->
{{--<div class="modal fade" id="resiMasalModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"--}}
{{--     aria-hidden="true">--}}
{{--    <div class="modal-dialog modal-xl" role="document">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header bg-primary text-white">--}}
{{--                <h5 class="modal-title">Pilih Resi untuk Dicetak</h5>--}}
{{--                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">--}}
{{--                    <span aria-hidden="true">×</span>--}}
{{--                </button>--}}
{{--            </div>--}}

{{--            <div class="modal-body">--}}

{{--                <!-- Card utama -->--}}
{{--                <div class="card shadow-sm">--}}
{{--                    <div class="card-body">--}}

{{--                        <!-- Tombol cetak -->--}}
{{--                        <div class="mb-3 text-right">--}}
{{--                            <button class="btn btn-success" id="printSelected">--}}
{{--                                <i class="fas fa-print"></i> Cetak Terpilih--}}
{{--                            </button>--}}
{{--                        </div>--}}

{{--                        <!-- Tabel daftar resi -->--}}
{{--                        <div class="table-responsive">--}}
{{--                            <table class="table table-bordered align-middle">--}}
{{--                                <thead class="thead-light">--}}
{{--                                <tr class="text-center">--}}
{{--                                    <th style="width: 50px;">#</th>--}}
{{--                                    <th>No Resi</th>--}}
{{--                                    <th>Nama Barang</th>--}}
{{--                                    <th style="width: 100px;">Jumlah</th>--}}
{{--                                </tr>--}}
{{--                                </thead>--}}
{{--                                <tbody>--}}
{{--                                @foreach($dataResi as $index => $resi)--}}
{{--                                    <tr>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <input type="checkbox" class="resi-checkbox" value="{{ $resi['no_resi'] }}">--}}
{{--                                        </td>--}}
{{--                                        <td>{{ $resi['no_resi'] }}</td>--}}
{{--                                        <td>{{ $resi['nama_barang'] }}</td>--}}
{{--                                        <td class="text-center">--}}
{{--                                            <img src="{{ asset('images/item-icon.png') }}" alt="Jumlah"--}}
{{--                                                 style="width: 24px; height: 24px; margin-right: 6px;">--}}
{{--                                            {{ $resi['jumlah'] }}--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                @endforeach--}}
{{--                                </tbody>--}}
{{--                            </table>--}}
{{--                        </div>--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--                <!-- End Card -->--}}

{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

@include('app.chat_modal.chat_modal')
