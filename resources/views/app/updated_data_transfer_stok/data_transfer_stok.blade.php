@extends('layouts.app_v2')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between bg-white rounded-lg border border-gray-200 p-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Data penerimaan transfer stock</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Export Button -->
            <div class="relative">
                <button type="button" onclick="toggleExportDropdown('export-dropdown-std')"
                        class="px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    Export
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div id="export-dropdown-std" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                    <div class="py-2">
                        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Tentukan Tanggal :</div>
                        <div class="px-4 py-2">
                            <input type="date" id="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                        </div>
                        <div class="px-4 py-2">
                            <input type="date" id="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                        </div>
                        <div class="px-4 py-2">
                            <button type="button" id="std_export_all_btn" class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                <i class="fas fa-file-excel"></i>
                                Export All
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Data Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-5">
            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div class="md:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="search" id="stock_transfer_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari user / Artikel id / Kode Transfer">
                    </div>
                </div>
                <div>
                    <select id="st_id_start" name="st_id_start" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">- Pilih Store Awal -</option>
                        @foreach ($data['st_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="st_id_start_parent"></div>
                </div>
                <div>
                    <select id="st_id_end" name="st_id_end" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">- Pilih Store Tujuan -</option>
                        @foreach ($data['st_id'] as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <div id="st_id_end_parent"></div>
                </div>
            </div>

            <!-- Date Range Picker -->
            <div class="mb-4">
                <a href="#" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors border border-blue-200"
                   id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Tanggal Invoice" data-placement="left">
                    <i class="fas fa-calendar-alt"></i>
                    <span id="kt_dashboard_daterangepicker_title">Today</span>
                    <span id="kt_dashboard_daterangepicker_date" class="font-bold"></span>
                    <input type="hidden" id="transfer_receive_date" />
                </a>
            </div>

            <!-- Main Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="StockTransferDatatb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pengirim</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Qty</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Store Awal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Store Tujuan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penerima</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- History Section -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">History Penerimaan</h3>
        </div>
        <div class="p-5">
            <!-- Search -->
            <div class="mb-4">
                <div class="relative max-w-md">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" id="history_search" class="block w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari kode / artikel">
                </div>
            </div>

            <!-- History Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200" id="Historytb">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider text-center">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Store Awal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Store Tujuan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Brand</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Artikel</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Warna</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Size</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty Terima</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">HB</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">HJ</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Penerima</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('app.updated_data_transfer_stok.data_transfer_stok_modal')
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@include('app.updated_data_transfer_stok.data_transfer_stok_css')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@include('app.updated_data_transfer_stok.data_transfer_stok_js')
@endpush
