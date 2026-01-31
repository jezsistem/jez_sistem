@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Manage mass adjustments</p>
        </div>
    </div>

    <!-- Filter and Stats Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Filter Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Template</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Store</label>
                    <select id="st_filter" 
                            class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value='all'>- Semua Store -</option>
                        @foreach ($data['st_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="st_filter_parent"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                    <select id="br_filter" 
                            class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value='all'>- Semua Brand -</option>
                        @foreach ($data['br_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="br_filter_parent"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sub Kategori</label>
                    <select id="psc_filter" 
                            class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value='all'>- Semua Sub Kategori -</option>
                        @foreach ($data['psc_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="psc_filter_parent"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Qty Filter</label>
                    <select id="qty_filter" 
                            class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value='1'>- Hanya yang Ada Stok -</option>
                        <option value='0'>- Termasuk yang Sudah Habis -</option>
                    </select>
                    <div id="qty_filter_parent"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">BIN</label>
                    <select id="bin_filter" 
                            class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value=''>- Pilih Store Terlebih Dahulu -</option>
                    </select>
                    <div id="bin_filter_parent"></div>
                </div>
                <div id="bin_filter_panel" class="mt-4"></div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-500 text-white rounded-lg p-4 text-center">
                    <div class="text-xl font-semibold" id="cc_qty">0</div>
                    <div class="text-sm mt-1">Cash/Credit</div>
                </div>
                <div class="bg-blue-500 text-white rounded-lg p-4 text-center">
                    <div class="text-xl font-semibold" id="c_qty">0</div>
                    <div class="text-sm mt-1">Consignment</div>
                </div>
                <div class="bg-blue-500 text-white rounded-lg p-4 text-center">
                    <div class="text-xl font-semibold" id="cc_value">0</div>
                    <div class="text-sm mt-1">C/C Value</div>
                </div>
                <div class="bg-blue-500 text-white rounded-lg p-4 text-center">
                    <div class="text-xl font-semibold" id="c_value">0</div>
                    <div class="text-sm mt-1">Con Value</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Stock Data</h3>
            <button onclick="exportTemplate()" 
                    id="export_btn"
                    class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                Export Template
            </button>
        </div>
        <div class="p-4 md:p-5">
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="cft-standard-stroke cft-search text-gray-400"></i>
                    </div>
                    <input type="search" 
                           id="stock_search"
                           class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Cari stok...">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="Stocktb">
                    <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                        <tr>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">BIN</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">BRAND</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">ARTIKEL</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">WARNA</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">SIZE</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Sub Kategori</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Stok</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Harga Beli</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mass Adjustment Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Mass Adjustment List</h3>
            <div class="flex items-center gap-3">
                <button onclick="openImportModal()" 
                        id="import_btn"
                        class="px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-colors">
                    Import Template
                </button>
            </div>
        </div>
        <div class="p-4 md:p-5">
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="cft-standard-stroke cft-search text-gray-400"></i>
                    </div>
                    <input type="search" 
                           id="ma_search"
                           class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                           placeholder="Cari kode / User / Sku / Note...">
                </div>
            </div>
            <div class="mb-4 flex items-center justify-end gap-3">
                <input type="hidden" id="ma_date" />
                <button onclick="exportByDate()" 
                        id="export_by_date"
                        class="px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-colors">
                    Export By Date
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="MassAdjustmenttb">
                    <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                        <tr>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Kode</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Store</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Dibuat Oleh</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Approval</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Eksekutor</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Editor</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Note</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Tipe</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Dibuat</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Diupdate</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Status</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mass Adjustment Detail Table Section (Hidden by default) -->
    <div id="mad_panel" class="hidden bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="p-4 md:p-5 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <span class="px-4 py-2 bg-blue-500 text-white rounded-lg font-medium" id="ma_code">MADJxxxxxx</span>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="cft-standard-stroke cft-search text-gray-400"></i>
                        </div>
                        <input type="search" 
                               id="mad_search"
                               class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Cari artikel / Sku...">
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium text-gray-700">Approval:</label>
                        <input class="px-4 py-2 border border-gray-300 rounded-lg text-sm" 
                               placeholder="Approval" 
                               data-id="" 
                               type="text"
                               id="approval_label" 
                               readonly />
                        <button onclick="approveAdjustment()" 
                                id="approval_btn"
                                class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                            Approve
                        </button>
                    </div>
                    <button onclick="exportMAD()" 
                            id="export_mad_btn"
                            class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors">
                        Export
                    </button>
                    <button onclick="executeAdjustment()" 
                            id="execution_btn"
                            class="px-4 py-2 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-colors">
                        Eksekusi Penyesuaian
                    </button>
                </div>
            </div>
        </div>
        <div class="p-4 md:p-5">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="MassAdjustmentDetailtb">
                    <thead class="text-sm text-body bg-gray-100 border-b rounded-base border-gray-200">
                        <tr>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 !text-center">No</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">BIN</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">BRAND</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">SKU</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">ARTIKEL</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">WARNA</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">SIZE</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Sub Kategori</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">HB</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">HJ</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Qty System</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Qty SO</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Type</th>
                            <th class="px-6 !py-3 text-left text-xs !font-medium !text-gray-500 uppercase tracking-wider !border-gray-200 text-center">Diff</th>
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

<!-- Import Modal -->
<div id="ImportModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeImportModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white shadow-xl transition-all">
            <form id="f_import" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Import Data</h3>
                    <button type="button" 
                            onclick="closeImportModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4 md:p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih template yang sudah diisi dari hasil export template <span class="text-red-500">*</span></label>
                        <input type="file" 
                               name="template" 
                               id="template" 
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Adjustment <span class="text-red-500">*</span></label>
                        <select name="tipe_adjustment" 
                                id="tipe_adjustment" 
                                required
                                class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Tipe Adjustment --</option>
                            <option value="KERUGIAN">KERUGIAN</option>
                            <option value="BELUM TERBAYAR">BELUM TERBAYAR</option>
                            <option value="TERBAYAR">TERBAYAR</option>
                            <option value="BIAYA PROMOSI">BIAYA PROMOSI</option>
                            <option value="BIAYA OPERASIONAL">BIAYA OPERASIONAL</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note Adjustment <span class="text-red-500">*</span></label>
                        <select id="note_adjustment" 
                                name="note_adjustment" 
                                required
                                class="w-full px-4 py-2 bg-white border-2 border-gray-900 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                            <option value="KERUGIAN RETUR MP">KERUGIAN RETUR MP</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-200">
                    <button type="button" 
                            onclick="closeImportModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button type="submit" 
                            id="import_data_btn"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900 transition-colors">
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Simple-datatables CSS -->
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.2.0/dist/style.css" rel="stylesheet" />
@endpush

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Simple-datatables JS -->
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.2.0/dist/umd/simple-datatables.min.js"></script>
<!-- SweetAlert JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"
        integrity="sha512-AA1Bzp5Q0K1KanKKmvN/4d3IRKVlv9PYgwFPvm32nPO6QS8yH1HO7LbgB1pgiOxPtfeg5zEn2ba64MUcqJx6CA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>
<!-- jQuery Toast JS -->
<script src="{{ asset('cdn/jquery.toast.min.js') }}"></script>
<!-- XLSX Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
let stockDataTable;
let massAdjustmentDataTable;
let massAdjustmentDetailDataTable;
let st_id = $('#st_filter').val();
let psc_id = $('#psc_filter').val();
let br_id = $('#br_filter').val();
let qty_filter = $('#qty_filter').val();
let pl_id = [];
let mad_id = '';

$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Select2 for filters
    $('#st_filter').select2({
        width: "100%",
        dropdownParent: $('#st_filter_parent')
    });
    $('#br_filter').select2({
        width: "100%",
        dropdownParent: $('#br_filter_parent')
    });
    $('#psc_filter').select2({
        width: "100%",
        dropdownParent: $('#psc_filter_parent')
    });
    $('#qty_filter').select2({
        width: "100%",
        dropdownParent: $('#qty_filter_parent')
    });
    $('#bin_filter').select2({
        width: "100%",
        dropdownParent: $('#bin_filter_parent')
    });

    // Filter change handlers
    $('#st_filter').on('change', function() {
        st_id = $(this).val();
        loadLocation(st_id);
        loadAsset(st_id, psc_id, br_id);
        loadStockData();
    });

    $('#br_filter').on('change', function() {
        br_id = $(this).val();
        loadAsset(st_id, psc_id, br_id);
        loadStockData();
    });

    $('#psc_filter').on('change', function() {
        psc_id = $(this).val();
        loadAsset(st_id, psc_id, br_id);
        loadStockData();
    });

    $('#qty_filter').on('change', function() {
        qty_filter = $(this).val();
        loadAsset(st_id, psc_id, br_id);
        loadStockData();
    });

    $('#bin_filter').on('change', function() {
        var selectedBins = $(this).val();
        if (Array.isArray(selectedBins)) {
            pl_id = selectedBins;
        } else {
            pl_id = selectedBins ? [selectedBins] : [];
        }
        loadAsset(st_id, psc_id, br_id);
        loadStockData();
    });

    // Search handlers
    $('#stock_search').on('keyup', function() {
        loadStockData();
    });

    $('#ma_search').on('keyup', function() {
        loadMassAdjustmentData();
    });

    $('#mad_search').on('keyup', function() {
        loadMassAdjustmentDetailData();
    });

    // Import form handler
    $('#f_import').on('submit', function(e) {
        e.preventDefault();
        $("#import_data_btn").html('Proses ..');
        $("#import_data_btn").attr("disabled", true);
        var formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: "{{ url('import_mass_adjustment_template') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                if (data.status == '200') {
                    closeImportModal();
                    toast('Berhasil', 'Data berhasil diimport', 'success');
                    loadMassAdjustmentData();
                } else {
                    toast('Gagal', 'Data gagal diimport', 'error');
                }
            },
            error: function(data) {
                $("#import_data_btn").html('Import');
                $("#import_data_btn").attr("disabled", false);
                toast('Error', 'Terjadi kesalahan saat import data', 'error');
            }
        });
    });

    // Load initial data
    loadMassAdjustmentData();
});

function loadLocation(st_id) {
    $.ajax({
        type: 'POST',
        url: "{{ url('load_mass_adjustment_location') }}",
        data: {
            st_id: st_id,
            pl_id: pl_id
        },
        dataType: 'json',
        success: function(response) {
            if (response.data && Object.keys(response.data).length > 0) {
                $('#bin_filter').empty();
                $('#bin_filter').append('<option value="">- Pilih Lokasi -</option>');
                Object.entries(response.data).forEach(function([key, value]) {
                    $('#bin_filter').append('<option value="' + key + '">' + value + '</option>');
                });
            } else {
                $('#bin_filter').empty();
                $('#bin_filter').append('<option value="">- Tidak Ada Lokasi Tersedia -</option>');
            }
        },
        error: function() {
            $('#bin_filter').empty();
            $('#bin_filter').append('<option value="">- Gagal Memuat Lokasi -</option>');
        }
    });
}

function loadAsset(st_id, psc_id, br_id) {
    $.ajax({
        type: 'POST',
        url: "{{ url('load_mass_asset') }}",
        data: {
            st_id: st_id,
            psc_id: psc_id,
            br_id: br_id,
            pl_id: pl_id,
            qty_filter: qty_filter
        },
        dataType: 'json',
        success: function(r) {
            if (r.status == '200') {
                $('#cc_qty').text(r.cc_qty || '0');
                $('#c_qty').text(r.c_qty || '0');
                $('#cc_value').text(r.cc_value || '0');
                $('#c_value').text(r.c_value || '0');
            }
        }
    });
}

function loadStockData() {
    $.ajax({
        url: "{{ url('mass_stock_datatables') }}",
        type: 'GET',
        data: {
            st_id: st_id,
            psc_id: psc_id,
            br_id: br_id,
            pl_id: pl_id,
            qty_filter: qty_filter,
            search: $('#stock_search').val(),
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (stockDataTable) {
                stockDataTable.destroy();
            }
            
            $('#Stocktb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.pl_code || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.br_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.p_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.p_color || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.sz_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.psc_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.pls_qty || '0') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.purchase || '0') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.sell || '0') + '</td>';
                    html += '</tr>';
                    $('#Stocktb tbody').append(html);
                });
            } else {
                $('#Stocktb tbody').append('<tr><td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("Stocktb") && typeof simpleDatatables !== 'undefined') {
                stockDataTable = new simpleDatatables.DataTable("#Stocktb", {
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
            console.error('Error loading stock data:', xhr);
            toast('Error', 'Gagal memuat data stok', 'error');
        }
    });
}

function loadMassAdjustmentData() {
    $.ajax({
        url: "{{ url('mass_adjustment_datatables') }}",
        type: 'GET',
        data: {
            search: $('#ma_search').val(),
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (massAdjustmentDataTable) {
                massAdjustmentDataTable.destroy();
            }
            
            $('#MassAdjustmenttb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50 cursor-pointer" data-id="' + row.id + '">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' + escapeHtml(row.ma_code || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.st_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.u_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.approve || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.executor || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.editor || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.ma_note || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.ma_type || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.created_at || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.updated_at || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.ma_status || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.action || '') + '</td>';
                    html += '</tr>';
                    $('#MassAdjustmenttb tbody').append(html);
                });
            } else {
                $('#MassAdjustmenttb tbody').append('<tr><td colspan="13" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("MassAdjustmenttb") && typeof simpleDatatables !== 'undefined') {
                massAdjustmentDataTable = new simpleDatatables.DataTable("#MassAdjustmenttb", {
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
                    $('#MassAdjustmenttb tbody').off('click', 'tr').on('click', 'tr', function() {
                        var id = $(this).data('id');
                        if (id) {
                            mad_id = id;
                            $('#ma_code').text($(this).find('td:eq(1)').text());
                            $('#ma_code').attr('data-id', id);
                            loadMassAdjustmentDetailData();
                            loadApproval();
                            $('#mad_panel').removeClass('hidden');
                        }
                    });
                }, 100);
            }
        },
        error: function(xhr) {
            console.error('Error loading mass adjustment data:', xhr);
            toast('Error', 'Gagal memuat data mass adjustment', 'error');
        }
    });
}

function loadMassAdjustmentDetailData() {
    $.ajax({
        url: "{{ url('mass_adjustment_detail_datatables') }}",
        type: 'GET',
        data: {
            ma_id: mad_id,
            search: $('#mad_search').val(),
            length: -1
        },
        dataType: 'json',
        success: function(response) {
            if (massAdjustmentDetailDataTable) {
                massAdjustmentDetailDataTable.destroy();
            }
            
            $('#MassAdjustmentDetailtb tbody').empty();
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(row, index) {
                    var html = '<tr class="hover:bg-gray-50">';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (index + 1) + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.pl_code || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.br_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.sku || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escapeHtml(row.article || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.p_color || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.sz_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.psc_name || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.hb || '0') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + (row.hj || '0') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.qty_system || '0') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.qty_so || '0') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(row.type || '') + '</td>';
                    html += '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">' + (row.diff || '0') + '</td>';
                    html += '</tr>';
                    $('#MassAdjustmentDetailtb tbody').append(html);
                });
            } else {
                $('#MassAdjustmentDetailtb tbody').append('<tr><td colspan="14" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td></tr>');
            }
            
            if (document.getElementById("MassAdjustmentDetailtb") && typeof simpleDatatables !== 'undefined') {
                massAdjustmentDetailDataTable = new simpleDatatables.DataTable("#MassAdjustmentDetailtb", {
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
            console.error('Error loading detail data:', xhr);
            toast('Error', 'Gagal memuat data detail', 'error');
        }
    });
}

function loadApproval() {
    $('#approval_label').attr('data-id', '');
    $('#approval_label').val('');
    $.ajax({
        type: 'POST',
        url: "{{ url('load_mass_approval') }}",
        data: {
            ma_id: mad_id
        },
        dataType: 'json',
        success: function(r) {
            if (r.status == '200' && r.data) {
                $('#approval_label').attr('data-id', r.data.id);
                $('#approval_label').val(r.data.u_name || '');
            }
        }
    });
}

function exportTemplate() {
    window.location.href = "{{ url('export_mass_adjustment_template') }}";
}

function exportByDate() {
    var date = $('#ma_date').val();
    if (!date) {
        toast('Warning', 'Pilih tanggal terlebih dahulu', 'warning');
        return;
    }
    window.location.href = "{{ url('export_mass_by_date') }}?date=" + date;
}

function exportMAD() {
    if (!mad_id) {
        toast('Warning', 'Pilih adjustment terlebih dahulu', 'warning');
        return;
    }
    window.location.href = "{{ url('export_mass_adjustment_result') }}?ma_id=" + mad_id;
}

function approveAdjustment() {
    var approval_id = $('#approval_label').attr('data-id');
    if (!approval_id) {
        toast('Warning', 'Pilih approval terlebih dahulu', 'warning');
        return;
    }
    $.ajax({
        type: 'POST',
        url: "{{ url('mass_adjustment_approval') }}",
        data: {
            ma_id: mad_id,
            approval_id: approval_id
        },
        dataType: 'json',
        success: function(r) {
            if (r.status == '200') {
                toast('Berhasil', 'Approval berhasil', 'success');
                loadMassAdjustmentData();
            } else {
                toast('Gagal', 'Gagal approve', 'error');
            }
        }
    });
}

function executeAdjustment() {
    if (!mad_id) {
        toast('Warning', 'Pilih adjustment terlebih dahulu', 'warning');
        return;
    }
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
                url: "{{ url('mass_adjustment_exec') }}",
                data: {
                    ma_id: mad_id
                },
                dataType: 'json',
                success: function(r) {
                    if (r.status == '200') {
                        toast('Berhasil', 'Adjustment berhasil dieksekusi', 'success');
                        loadMassAdjustmentData();
                        $('#mad_panel').addClass('hidden');
                    } else {
                        toast('Gagal', 'Gagal eksekusi', 'error');
                    }
                }
            });
        }
    });
}

function openImportModal() {
    document.getElementById('ImportModal').classList.remove('hidden');
    document.getElementById('ImportModal').setAttribute('aria-hidden', 'false');
    document.body.classList.add('overflow-hidden');
}

function closeImportModal() {
    document.getElementById('ImportModal').classList.add('hidden');
    document.getElementById('ImportModal').setAttribute('aria-hidden', 'true');
    document.body.classList.remove('overflow-hidden');
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
