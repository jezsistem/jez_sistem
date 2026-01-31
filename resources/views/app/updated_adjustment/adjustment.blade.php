@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage bin adjustments</p>
        </div>
    </div>

    <!-- BIN Selector Section -->
    <div class="bg-blue-500 rounded-lg p-4">
        <input id="pl_id_hidden" type="hidden" value="" />
        <input id="_mode" type="hidden" value="" />
        <label class="block text-sm font-medium text-white mb-2">Pilih BIN</label>
        <select id="pl_id" 
                name="pl_id" 
                required
                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">- Pilih BIN -</option>
            @foreach ($data['pl_id'] as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
        <div id="pl_id_parent"></div>
    </div>

    <!-- History Adjustment Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="p-4 md:p-5 border-b border-gray-200">
            <div class="flex flex-wrap items-center gap-4 mb-4">
                <h3 class="text-lg font-semibold text-gray-900">History Adjustment</h3>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Store</label>
                    <select id="st_id_filter" 
                            name="st_id_filter"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">- Storage -</option>
                        @foreach ($data['st_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" 
                            id="status"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">- Status -</option>
                        @foreach(App\Models\BinAdjustment::getStatusOptions() as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="hidden" id="stock_report_date" value=""/>
                    <button type="button" 
                            id="kt_dashboard_daterangepicker"
                            class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition-colors text-left">
                        <span class="text-white font-medium mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                        <span class="text-white font-bold" id="kt_dashboard_daterangepicker_date"></span>
                    </button>
                </div>
            </div>
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="cft-standard-stroke cft-search text-gray-400"></i>
                    </div>
                    <input type="search" 
                           id="history_search"
                           class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Cari kode / nama artikel / warna / brand / size...">
                </div>
            </div>
        </div>
        <div class="p-4 md:p-5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="AdjustmentHistorytb">
                    <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                        <tr>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Code</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Store</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">BIN</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Creator</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Approval</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Eksekutor</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Brand</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Artikel</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">SKU</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Warna</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Size</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">COGS</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Updated</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Old Qty</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">New Qty</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Adjust</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Note</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Adjustment Modal -->
<div id="AdjustmentModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeAdjustmentModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_adjustment">
                <input type="hidden" id="_pl_id" value="" />
                @csrf
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Adjustment [<span id="adjustment_label"></span>]</h3>
                    <button type="button" 
                            onclick="closeAdjustmentModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 md:p-5">
                    <div class="mb-4">
                        <button onclick="openAddArticleModal()" 
                                id="add_article_btn"
                                class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition-colors flex items-center gap-2">
                            <i class="cft-standard-stroke cft-add"></i>
                            Tambah Artikel
                        </button>
                    </div>
                    <div class="mb-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="cft-standard-stroke cft-search text-gray-400"></i>
                            </div>
                            <input type="search" 
                                   id="article_search"
                                   class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Cari nama artikel / warna / brand...">
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="Articletb">
                            <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                                <tr>
                                    <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                                    <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Article</th>
                                    <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Size Stock Barcode</th>
                                    <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Qty SO</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Data will be loaded by DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeAdjustmentModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="save_adjustment_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Article Modal -->
<div id="AddArticleModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeAddArticleModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_article">
                @csrf
                <input type="hidden" name="pst_id_hidden" id="pst_id_hidden" value="" />
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Tambah Artikel</h3>
                    <button type="button" 
                            onclick="closeAddArticleModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 md:p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Artikel <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="product_name_input"
                               class="w-full px-4 py-2 border-2 border-gray-900 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="nama size warna brand">
                        <div id="itemList" class="mt-2"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Qty <span class="text-red-500">*</span></label>
                        <input type="text" 
                               id="pls_qty" 
                               name="pls_qty" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note <span class="text-red-500">*</span></label>
                        <select id="article_note" 
                                name="article_note" 
                                required
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                </div>
                <div class="flex items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeAddArticleModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="save_add_article_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Action Adjustment Modal -->
<div id="ActionAdjustmentModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeActionModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-3xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <input type="hidden" name="ba_id" id="ba_id" value="" />
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 class="text-lg font-semibold text-gray-900">Detail Penyesuaian Stok</h3>
                <button type="button" 
                        onclick="closeActionModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4">
                <div class="flex justify-between items-center">
                    <span><strong>Kode:</strong> <span id="adj_code">-</span></span>
                    <span class="px-3 py-1 bg-yellow-500 text-white rounded-lg text-sm" id="adj_status_badge">-</span>
                </div>
                <div>
                    <div><strong>Dibuat oleh:</strong> <span id="adj_creator">-</span></div>
                    <div><strong>Tanggal:</strong> <span id="adj_create_date">-</span></div>
                </div>
                <div>
                    <div><strong>Disetujui oleh:</strong> <span id="adj_approval">-</span></div>
                    <div><strong>Tanggal:</strong> <span id="adj_approve_date">-</span></div>
                </div>
                <div>
                    <div><strong>Dieksekusi oleh:</strong> <span id="adj_execute">-</span></div>
                    <div><strong>Tanggal:</strong> <span id="adj_execute_date">-</span></div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kuantitas Awal</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kuantitas Baru</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Penyesuaian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td id="qty_awal" class="px-6 py-4 text-center text-sm text-gray-500">-</td>
                                <td id="qty_baru" class="px-6 py-4 text-center text-sm text-gray-500">-</td>
                                <td id="qty_adj" class="px-6 py-4 text-center text-sm text-gray-500">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <h6 class="font-bold text-gray-900 mb-2">Detail Produk</h6>
                    <div class="space-y-1 text-sm">
                        <div><strong>Nama:</strong> <span id="product_name">-</span></div>
                        <div><strong>Brand:</strong> <span id="product_brand">-</span></div>
                        <div><strong>SKU:</strong> <span id="product_sku">-</span></div>
                        <div><strong>Warna:</strong> <span id="product_color">-</span></div>
                        <div><strong>Size:</strong> <span id="product_size">-</span></div>
                        <div><strong>COGS:</strong> <span id="cogs">-</span></div>
                    </div>
                </div>
                <div>
                    <h6 class="font-bold text-gray-900 mb-2">Lokasi</h6>
                    <div class="space-y-1 text-sm">
                        <div><strong>Gudang:</strong> <span id="warehouse">-</span></div>
                        <div><strong>BIN:</strong> <span id="bin">-</span></div>
                    </div>
                </div>
                <div>
                    <h6 class="font-bold text-gray-900 mb-2">Catatan/Alasan dari Pembuat</h6>
                    <div id="adj_note" class="text-sm text-gray-600">-</div>
                </div>
                <div class="flex justify-end gap-3 mt-5 hidden" id="btns_approval">
                    <button type="button" 
                            onclick="rejectAdjustment()"
                            id="btn_reject_adj"
                            class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors">
                        Tolak
                    </button>
                    <button type="button" 
                            onclick="approveAdjustment()"
                            id="btn_approve_adj"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors">
                        Setujui
                    </button>
                    <button type="button" 
                            onclick="closeActionModal()"
                            id="close_modal_approval"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                </div>
                <div class="flex justify-end gap-3 mt-5 hidden" id="btns_execution">
                    <button type="button" 
                            onclick="cancelAdjustment()"
                            id="btn_cancel_adj"
                            class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 transition-colors">
                        Batal
                    </button>
                    <button type="button" 
                            onclick="executeAdjustment()"
                            id="btn_exec_adj"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-lg hover:bg-green-600 transition-colors">
                        Eksekusi
                    </button>
                    <button type="button" 
                            onclick="closeActionModal()"
                            id="close_modal_exec"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Simple-datatables CSS -->
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.2.0/dist/style.css" rel="stylesheet" />
<!-- Daterangepicker CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Simple-datatables JS -->
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.2.0/dist/umd/simple-datatables.min.js"></script>
<!-- Daterangepicker JS -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- SweetAlert JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
<!-- jQuery Toast JS -->
<script src="{{ asset('cdn/jquery.toast.min.js') }}"></script>

<script>
let adjustmentHistoryDataTable;
let articleDataTable;
let currentPlId = '';

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Select2 for BIN selector
    $('#pl_id').select2({
        width: "100%",
        dropdownParent: $('#pl_id_parent')
    });

    // BIN change handler
    $('#pl_id').on('change', function() {
        currentPlId = $(this).val();
        $('#pl_id_hidden').val(currentPlId);
        if (currentPlId) {
            $('#_mode').val('adjust');
            openAdjustmentModal();
        }
    });

    // Filter change handlers
    $('#st_id_filter').on('change', function() {
        loadAdjustmentHistoryData();
    });

    $('#status').on('change', function() {
        loadAdjustmentHistoryData();
    });

    // Search handler
    $('#history_search').on('keyup', function() {
        loadAdjustmentHistoryData();
    });

    // Initialize daterangepicker
    $('#kt_dashboard_daterangepicker').daterangepicker({
        opens: 'left',
        startDate: moment(),
        endDate: moment(),
        locale: {
            format: 'DD/MM/YYYY'
        }
    }, function(start, end, label) {
        $('#stock_report_date').val(start.format('YYYY-MM-DD') + '|' + end.format('YYYY-MM-DD'));
        $('#kt_dashboard_daterangepicker_title').text(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#kt_dashboard_daterangepicker_date').text('');
        loadAdjustmentHistoryData();
    });

    // Adjustment form handler
    $('#f_adjustment').on('submit', function(e) {
        e.preventDefault();
        $("#save_adjustment_btn").html('Proses ..');
        $("#save_adjustment_btn").attr("disabled", true);
        $.ajax({
            type: 'POST',
            url: "{{ url('finish_adjustment') }}",
            data: {
                pl_id: currentPlId
            },
            dataType: 'json',
            success: function(data) {
                $("#save_adjustment_btn").html('Simpan');
                $("#save_adjustment_btn").attr("disabled", false);
                if (data.status == '200') {
                    closeAdjustmentModal();
                    toast('Berhasil', 'Adjustment berhasil disimpan', 'success');
                    loadAdjustmentHistoryData();
                    reloadLocation();
                } else {
                    toast('Gagal', 'Gagal menyimpan adjustment', 'error');
                }
            },
            error: function(data) {
                $("#save_adjustment_btn").html('Simpan');
                $("#save_adjustment_btn").attr("disabled", false);
                toast('Error', 'Terjadi kesalahan saat menyimpan adjustment', 'error');
            }
        });
    });

    // Add article form handler
    $('#f_article').on('submit', function(e) {
        e.preventDefault();
        $("#save_add_article_btn").html('Proses ..');
        $("#save_add_article_btn").attr("disabled", true);
        var formData = {
            pst_id: $('#pst_id_hidden').val(),
            pls_qty: $('#pls_qty').val(),
            article_note: $('#article_note').val(),
            pl_id: currentPlId
        };
        $.ajax({
            type: 'POST',
            url: "{{ url('add_article_adjustment') }}",
            data: formData,
            dataType: 'json',
            success: function(data) {
                $("#save_add_article_btn").html('Simpan');
                $("#save_add_article_btn").attr("disabled", false);
                if (data.status == '200') {
                    closeAddArticleModal();
                    toast('Berhasil', 'Artikel berhasil ditambahkan', 'success');
                    loadArticleData();
                } else {
                    toast('Gagal', 'Gagal menambahkan artikel', 'error');
                }
            },
            error: function(data) {
                $("#save_add_article_btn").html('Simpan');
                $("#save_add_article_btn").attr("disabled", false);
                toast('Error', 'Terjadi kesalahan saat menambahkan artikel', 'error');
            }
        });
    });

    // Product name autocomplete
    $('#product_name_input').on('keyup', function() {
        var query = $(this).val();
        if (query.length >= 2) {
            $.ajax({
                type: 'POST',
                url: "{{ url('autocomplete_article') }}",
                data: {
                    query: query
                },
                dataType: 'html',
                success: function(data) {
                    $('#itemList').html(data);
                    $('#itemList').show();
                }
            });
        } else {
            $('#itemList').hide();
        }
    });

    // Load initial data
    loadAdjustmentHistoryData();
});

function loadAdjustmentHistoryData() {
    var dateRange = $('#stock_report_date').val();
    var dateParts = dateRange ? dateRange.split('|') : [];
    
    $.ajax({
        url: "{{ url('adjustment_history_datatables') }}",
        type: 'GET',
        data: {
            search: $('#history_search').val(),
            st_id_filter: $('#st_id_filter').val(),
            status: $('#status').val(),
            start_date: dateParts[0] || '',
            end_date: dateParts[1] || '',
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (adjustmentHistoryDataTable) {
                adjustmentHistoryDataTable.destroy();
            }
            
            $('#AdjustmentHistorytb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50 cursor-pointer" data-id="' + row.ba_id + '">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' + escapeHtml(row.ba_code || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.st_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.pl_code || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.u_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.ba_approve || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.ba_executor || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.br_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.p_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.ps_barcode || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.p_color || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.sz_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.cogs || '0') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.ba_updated_at || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.ba_old_qty || '0') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.ba_new_qty || '0') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.ba_adjust || '0') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.ba_note || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.ba_status || '') + '</td>';
                    html += '</tr>';
                    $('#AdjustmentHistorytb tbody').append(html);
                });
            } else {
                $('#AdjustmentHistorytb tbody').append('<tr><td colspan="19" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("AdjustmentHistorytb") && typeof simpleDatatables !== 'undefined') {
                adjustmentHistoryDataTable = new simpleDatatables.DataTable("#AdjustmentHistorytb", {
                    searchable: true,
                    sortable: true,
                    perPage: 10,
                    perPageSelect: [5, 10, 25, 50, 100],
                    classes: {
                        wrapper: "datatable-wrapper",
                        top: "datatable-top",
                        bottom: "datatable-bottom",
                        search: "datatable-search",
                        selector: "datatable-selector",
                        table: "datatable-table",
                        thead: "",
                        tbody: "",
                        sorter: "datatable-sorter"
                    },
                    labels: {
                        placeholder: "Cari...",
                        perPage: "",
                        noRows: "Tidak ada data",
                        info: "Menampilkan {start} sampai {end} dari {rows} data",
                        noResults: "Tidak ada hasil pencarian"
                    }
                });
                
                setTimeout(function() {
                    $('#AdjustmentHistorytb tbody').off('click', 'tr').on('click', 'tr', function() {
                        var ba_id = $(this).data('id');
                        if (ba_id) {
                            loadAdjustmentDetail(ba_id);
                        }
                    });
                }, 100);
            }
        },
        error: function(xhr) {
            console.error('Error loading history data:', xhr);
            toast('Error', 'Gagal memuat data history', 'error');
        }
    });
}

function loadArticleData() {
    var mode = $('#_mode').val();
    var pl_id = mode == 'adjust' ? currentPlId : $('#pl_id_hidden').val();
    
    $.ajax({
        url: "{{ url('article_adjustment_datatables') }}",
        type: 'GET',
        data: {
            search: $('#article_search').val(),
            pl_id: pl_id,
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (articleDataTable) {
                articleDataTable.destroy();
            }
            
            $('#Articletb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.article || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.barcode || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.qty || '0') + '</td>';
                    html += '</tr>';
                    $('#Articletb tbody').append(html);
                });
            } else {
                $('#Articletb tbody').append('<tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Articletb") && typeof simpleDatatables !== 'undefined') {
                articleDataTable = new simpleDatatables.DataTable("#Articletb", {
                    searchable: true,
                    sortable: true,
                    perPage: 10,
                    perPageSelect: [5, 10, 25, 50, 100],
                    classes: {
                        wrapper: "datatable-wrapper",
                        top: "datatable-top",
                        bottom: "datatable-bottom",
                        search: "datatable-search",
                        selector: "datatable-selector",
                        table: "datatable-table",
                        thead: "",
                        tbody: "",
                        sorter: "datatable-sorter"
                    },
                    labels: {
                        placeholder: "Cari...",
                        perPage: "",
                        noRows: "Tidak ada data",
                        info: "Menampilkan {start} sampai {end} dari {rows} data",
                        noResults: "Tidak ada hasil pencarian"
                    }
                });
            }
        },
        error: function(xhr) {
            console.error('Error loading article data:', xhr);
            toast('Error', 'Gagal memuat data artikel', 'error');
        }
    });
}

function loadAdjustmentDetail(ba_id) {
    $.ajax({
        type: 'GET',
        url: "{{ url('get_adjustment_detail') }}/" + ba_id,
        dataType: 'json',
        success: function(response) {
            if (response.status == '200' && response.data) {
                var data = response.data;
                $('#ba_id').val(ba_id);
                $('#adj_code').text(data.ba_code || '-');
                $('#adj_status_badge').text(data.ba_status || '-');
                $('#adj_creator').text(data.creator || '-');
                $('#adj_create_date').text(data.create_date || '-');
                $('#adj_approval').text(data.approval || '-');
                $('#adj_approve_date').text(data.approve_date || '-');
                $('#adj_execute').text(data.executor || '-');
                $('#adj_execute_date').text(data.execute_date || '-');
                $('#qty_awal').text(data.ba_old_qty || '0');
                $('#qty_baru').text(data.ba_new_qty || '0');
                $('#qty_adj').text(data.ba_adjust || '0');
                $('#product_name').text(data.product_name || '-');
                $('#product_brand').text(data.brand || '-');
                $('#product_sku').text(data.sku || '-');
                $('#product_color').text(data.color || '-');
                $('#product_size').text(data.size || '-');
                $('#cogs').text(data.cogs || '0');
                $('#warehouse').text(data.warehouse || '-');
                $('#bin').text(data.bin || '-');
                $('#adj_note').text(data.ba_note || '-');
                
                // Show appropriate buttons based on status
                $('#btns_approval').addClass('hidden');
                $('#btns_execution').addClass('hidden');
                
                if (data.ba_status == 'WAITING APPROVAL') {
                    $('#btns_approval').removeClass('hidden');
                } else if (data.ba_status == 'APPROVED') {
                    $('#btns_execution').removeClass('hidden');
                }
                
                openActionModal();
            }
        },
        error: function() {
            toast('Error', 'Gagal memuat detail adjustment', 'error');
        }
    });
}

function reloadLocation() {
    $.ajax({
        type: "GET",
        dataType: 'html',
        url: "{{ url('reload_location') }}",
        success: function(r) {
            $('#pl_id').html(r);
            $('#pl_id').select2({
                width: "100%",
                dropdownParent: $('#pl_id_parent')
            });
        }
    });
}

function openAdjustmentModal() {
    if (!currentPlId) return;
    $('#adjustment_label').text($('#pl_id option:selected').text());
    $('#_pl_id').val(currentPlId);
    loadArticleData();
    document.getElementById('AdjustmentModal').classList.remove('hidden');
    document.getElementById('AdjustmentModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeAdjustmentModal() {
    document.getElementById('AdjustmentModal').classList.add('hidden');
    document.getElementById('AdjustmentModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
    $('#pl_id').val('').trigger('change');
    currentPlId = '';
}

function openAddArticleModal() {
    document.getElementById('AddArticleModal').classList.remove('hidden');
    document.getElementById('AddArticleModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeAddArticleModal() {
    document.getElementById('AddArticleModal').classList.add('hidden');
    document.getElementById('AddArticleModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
    $('#f_article')[0].reset();
    $('#itemList').hide();
}

function openActionModal() {
    document.getElementById('ActionAdjustmentModal').classList.remove('hidden');
    document.getElementById('ActionAdjustmentModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeActionModal() {
    document.getElementById('ActionAdjustmentModal').classList.add('hidden');
    document.getElementById('ActionAdjustmentModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
}

function approveAdjustment() {
    var ba_id = $('#ba_id').val();
    $.ajax({
        type: 'POST',
        url: "{{ url('approve_adjustment') }}/" + ba_id,
        dataType: 'json',
        success: function(response) {
            if (response.status == '200') {
                toast('Berhasil', 'Adjustment berhasil disetujui', 'success');
                closeActionModal();
                loadAdjustmentHistoryData();
            } else {
                toast('Gagal', 'Gagal menyetujui adjustment', 'error');
            }
        },
        error: function() {
            toast('Error', 'Terjadi kesalahan saat menyetujui adjustment', 'error');
        }
    });
}

function rejectAdjustment() {
    var ba_id = $('#ba_id').val();
    swal({
        title: "Tolak..?",
        text: "Yakin tolak adjustment ini?",
        icon: "warning",
        buttons: [
            'Batalkan',
            'Tolak'
        ],
        dangerMode: true,
    }).then(function(isConfirm) {
        if (isConfirm) {
            $.ajax({
                type: 'POST',
                url: "{{ url('reject_adjustment') }}/" + ba_id,
                dataType: 'json',
                success: function(response) {
                    if (response.status == '200') {
                        toast('Berhasil', 'Adjustment berhasil ditolak', 'success');
                        closeActionModal();
                        loadAdjustmentHistoryData();
                    } else {
                        toast('Gagal', 'Gagal menolak adjustment', 'error');
                    }
                },
                error: function() {
                    toast('Error', 'Terjadi kesalahan saat menolak adjustment', 'error');
                }
            });
        }
    });
}

function executeAdjustment() {
    var ba_id = $('#ba_id').val();
    swal({
        title: "Eksekusi..?",
        text: "Yakin eksekusi adjustment ini?",
        icon: "warning",
        buttons: [
            'Batalkan',
            'Eksekusi'
        ],
        dangerMode: false,
    }).then(function(isConfirm) {
        if (isConfirm) {
            $.ajax({
                type: 'POST',
                url: "{{ url('execute_adjustment') }}/" + ba_id,
                dataType: 'json',
                success: function(response) {
                    if (response.status == '200') {
                        toast('Berhasil', 'Adjustment berhasil dieksekusi', 'success');
                        closeActionModal();
                        loadAdjustmentHistoryData();
                    } else {
                        toast('Gagal', 'Gagal eksekusi adjustment', 'error');
                    }
                },
                error: function() {
                    toast('Error', 'Terjadi kesalahan saat eksekusi adjustment', 'error');
                }
            });
        }
    });
}

function cancelAdjustment() {
    var ba_id = $('#ba_id').val();
    swal({
        title: "Batal..?",
        text: "Yakin batalkan adjustment ini?",
        icon: "warning",
        buttons: [
            'Batalkan',
            'Ya, Batal'
        ],
        dangerMode: true,
    }).then(function(isConfirm) {
        if (isConfirm) {
            $.ajax({
                type: 'POST',
                url: "{{ url('cancel_adjustment') }}/" + ba_id,
                dataType: 'json',
                success: function(response) {
                    if (response.status == '200') {
                        toast('Berhasil', 'Adjustment berhasil dibatalkan', 'success');
                        closeActionModal();
                        loadAdjustmentHistoryData();
                    } else {
                        toast('Gagal', 'Gagal membatalkan adjustment', 'error');
                    }
                },
                error: function() {
                    toast('Error', 'Terjadi kesalahan saat membatalkan adjustment', 'error');
                }
            });
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>
@endpush
