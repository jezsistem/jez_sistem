@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Cycle count stock</p>
        </div>
    </div>

    <!-- Scan Items & Items Detail Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Scan Items Card -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Scan Items</h3>
            </div>
            <div class="p-5 space-y-4">
                <div id="bin_filter_parent">
                    <label for="bin_filter" class="block text-sm font-medium text-gray-700 mb-2">Pilih Bin</label>
                    <select id="bin_filter" name="bin_filter" class="w-full px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg border-0 focus:ring-2 focus:ring-blue-500">
                        <option value=''>- Pilih Store Terlebih Dahulu -</option>
                    </select>
                </div>
                <div>
                    <label for="scan_sku" class="block text-sm font-medium text-gray-700 mb-2">Scan SKU</label>
                    <input type="text" id="scan_sku" name="scan_sku" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Scan SKU disini">
                    <input type="hidden" id="ccn_number" name="ccn_number" value="">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="reset_btn" class="px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 transition-colors">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Items Detail Card -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Items Detail</h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label for="p_name" class="block text-sm font-medium text-gray-700 mb-2">Article Name</label>
                    <input type="text" id="p_name" name="p_name" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-100" disabled>
                </div>
                <div>
                    <label for="sz_name" class="block text-sm font-medium text-gray-700 mb-2">Variant</label>
                    <input type="text" id="sz_name" name="sz_name" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-100" disabled>
                </div>
                <div>
                    <label for="pl_code" class="block text-sm font-medium text-gray-700 mb-2">Bin</label>
                    <input type="text" id="pl_code" name="pl_code" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-100" disabled>
                </div>
                <div>
                    <label for="pls_qty" class="block text-sm font-medium text-gray-700 mb-2">QTY</label>
                    <input type="text" id="pls_qty" name="pls_qty" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm bg-gray-100" disabled>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Table Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Stock List</h3>
                <button type="button" id="export_btn" class="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
                    Export Template
                </button>
            </div>
        </div>
        <div class="p-5">
            <!-- Search -->
            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="stock_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari stok">
                </div>
            </div>

            <!-- Data Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="Stocktb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">BIN</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">BRAND</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ARTIKEL</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">WARNA</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SIZE</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sub Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga Beli</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga Jual</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div id="pagination-container" class="mt-4 flex items-center justify-between">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Loader -->
<div id="loader" class="hidden fixed inset-0 z-9999 flex items-center justify-center bg-white bg-opacity-80">
    <div class="text-center">
        <img src="{{ asset('pos') }}/jez.gif" alt="loading" class="w-20 h-20 mx-auto mb-4 bg-white p-2 rounded-lg">
        <div class="loading-text text-xl font-bold text-gray-700">Loading data...<span class="dots">...</span></div>
    </div>
</div>

<div id="loader_download" class="hidden fixed inset-0 z-9999 flex items-center justify-center bg-white bg-opacity-80">
    <div class="text-center">
        <img src="{{ asset('pos') }}/jez.gif" alt="loading" class="w-20 h-20 mx-auto mb-4 bg-white p-2 rounded-lg">
        <div class="loading-text text-xl font-bold text-gray-700">Downloading data...<span class="dots">...</span></div>
    </div>
</div>

@include('app.updated_cycle_counts.cycle_count_modal')
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@include('app.updated_cycle_counts.cycle_count_css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@include('app.updated_cycle_counts.cycle_count_js')
@endpush
