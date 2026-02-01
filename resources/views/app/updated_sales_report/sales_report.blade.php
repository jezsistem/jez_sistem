@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan penjualan berdasarkan invoice dan artikel</p>
        </div>
        <div class="flex gap-2">
            <button type="button" id="check_hb_hj" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                Cek HB HJ
            </button>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Autorefresh</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="autorefresh">
                <option value="">Statik</option>
                <option value="auto">Autorefresh</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="stt_id" name="stt_id">
                <option value="">- Divisi -</option>
                @foreach ($data['stt_id'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Storage</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="st_id_filter" name="st_id_filter" required>
                <option value="">- Storage -</option>
                @foreach ($data['st_id'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="dp_id" name="dp_id" required>
                <option value="">- Status -</option>
                <option value="DP">DP</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="hidden" id="sales_date" value="" />
            <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer" id="kt_dashboard_daterangepicker">
                <span class="text-gray-500 mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                <span class="font-semibold" id="kt_dashboard_daterangepicker_date"></span>
            </button>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Actions</label>
            <button type="button" id="sales_summary_btn" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                Summary Penjualan
            </button>
        </div>
    </div>
</div>

<!-- By Invoice Table Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">By Invoice</h3>
        <div class="flex gap-2">
            <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-64" id="invoice_report_search" placeholder="Cari invoice / customer / user / divisi">
            <button type="button" data-type="invoice" id="export_invoice_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                <i class="fas fa-download mr-2"></i>Export Excel
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table id="InvoiceReportTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Tanggal</th>
                    <th scope="col" class="px-3 py-3">Store</th>
                    <th scope="col" class="px-3 py-3">Invoice</th>
                    <th scope="col" class="px-3 py-3">Customer</th>
                    <th scope="col" class="px-3 py-3">Cross Order</th>
                    <th scope="col" class="px-3 py-3">User</th>
                    <th scope="col" class="px-3 py-3">Divisi</th>
                    <th scope="col" class="px-3 py-3">Item Qty</th>
                    <th scope="col" class="px-3 py-3">Item Value</th>
                    <th scope="col" class="px-3 py-3">Ongkir</th>
                    <th scope="col" class="px-3 py-3">Kode Unik</th>
                    <th scope="col" class="px-3 py-3">Biaya Admin</th>
                    <th scope="col" class="px-3 py-3">Diskon Penjual</th>
                    <th scope="col" class="px-3 py-3">Biaya Lain</th>
                    <th scope="col" class="px-3 py-3">Nameset</th>
                    <th scope="col" class="px-3 py-3">Value - Admin</th>
                    <th scope="col" class="px-3 py-3">Total Semua</th>
                    <th scope="col" class="px-3 py-3">Tipe Bayar 1</th>
                    <th scope="col" class="px-3 py-3">Jumlah Bayar 1</th>
                    <th scope="col" class="px-3 py-3">Kartu 1</th>
                    <th scope="col" class="px-3 py-3">Ref 1</th>
                    <th scope="col" class="px-3 py-3">Tipe Bayar 2</th>
                    <th scope="col" class="px-3 py-3">Jumlah Bayar 2</th>
                    <th scope="col" class="px-3 py-3">Kartu 2</th>
                    <th scope="col" class="px-3 py-3">Ref 2</th>
                    <th scope="col" class="px-3 py-3">Pelunasan</th>
                    <th scope="col" class="px-3 py-3">Sub Payment</th>
                    <th scope="col" class="px-3 py-3">Tanggal Pelunasan</th>
                    <th scope="col" class="px-3 py-3">Pos Order Number</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                    <th scope="col" class="px-3 py-3">Note</th>
                </tr>
            </thead>
            <tbody id="invoice_report_tbody">
                <tr>
                    <td colspan="32" class="px-3 py-4 text-center text-gray-500">
                        Silakan pilih filter dan klik tanggal untuk memuat data
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4">
        <div id="invoice_report_pagination_info" class="text-sm text-gray-600"></div>
        <div id="invoice_report_pagination_controls" class="flex items-center gap-2"></div>
    </div>
</div>

<!-- By Artikel Table Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4">
            <h3 class="text-lg font-semibold text-gray-900">By Artikel</h3>
            <input type="hidden" id="pt_id_filter" value="" />
            <span id="pt_id_filter_label" class=""></span>
        </div>
        <div class="flex gap-2">
            <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-64" id="article_report_search" placeholder="Cari artikel">
            <button type="button" data-type="article" id="export_article_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                <i class="fas fa-download mr-2"></i>Export Excel
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table id="ArticleReportTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Tanggal</th>
                    <th scope="col" class="px-3 py-3">Invoice</th>
                    <th scope="col" class="px-3 py-3">Brand</th>
                    <th scope="col" class="px-3 py-3">Artikel</th>
                    <th scope="col" class="px-3 py-3">Kategori</th>
                    <th scope="col" class="px-3 py-3">Sub Kategori</th>
                    <th scope="col" class="px-3 py-3">Sub Sub Kategori</th>
                    <th scope="col" class="px-3 py-3">Warna</th>
                    <th scope="col" class="px-3 py-3">Size</th>
                    <th scope="col" class="px-3 py-3">Qty</th>
                    <th scope="col" class="px-3 py-3">Bandrol</th>
                    <th scope="col" class="px-3 py-3">Harga Jual</th>
                    <th scope="col" class="px-3 py-3">Total Harga</th>
                </tr>
            </thead>
            <tbody id="article_report_tbody">
                <tr>
                    <td colspan="14" class="px-3 py-4 text-center text-gray-500">
                        Silakan pilih filter dan klik tanggal untuk memuat data
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4">
        <div id="article_report_pagination_info" class="text-sm text-gray-600"></div>
        <div id="article_report_pagination_controls" class="flex items-center gap-2"></div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @include('app.updated_sales_report.sales_report_js_v2')
@endpush

@include('app.updated_sales_report.sales_report_modal_v2')
@endsection
