@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan kartu stok berdasarkan artikel</p>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer" id="kt_dashboard_daterangepicker">
                <span class="text-gray-500 mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                <span class="font-semibold" id="kt_dashboard_daterangepicker_date"></span>
            </button>
            <input type="hidden" id="dashboard_date" value="" />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Store</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="store_filter">
                <option value=''>- Store -</option>
                @foreach ($data['st_id'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="brand_filter">
                <option value=''>- Tentukan Brand -</option>
                @foreach ($data['br_id'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Exception</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="exception_filter">
                <option value="noexcept">No Exception</option>
                <option value="except">Exception</option>
            </select>
        </div>
    </div>
    <div class="flex gap-2">
        <button type="button" id="exec_btn" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
            Tampilkan
        </button>
        <button type="button" id="export_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
            Download Excel
        </button>
    </div>
</div>

<!-- Article Table Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6" id="article_panel" style="display: none;">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Artikel</h3>
        <div class="flex gap-2">
            <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-64" id="article_search" placeholder="Cari Artikel ID">
        </div>
    </div>
    <div class="overflow-x-auto">
        <table id="ArticleTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Article ID</th>
                    <th scope="col" class="px-3 py-3">Item Name</th>
                    <th scope="col" class="px-3 py-3">SKU</th>
                    <th scope="col" class="px-3 py-3">Size</th>
                    <th scope="col" class="px-3 py-3">Brand</th>
                    <th scope="col" class="px-3 py-3">Beginning Stock</th>
                    <th scope="col" class="px-3 py-3">Purchase Order</th>
                    <th scope="col" class="px-3 py-3">Trans-In</th>
                    <th scope="col" class="px-3 py-3">Trans-Out</th>
                    <th scope="col" class="px-3 py-3">Sales</th>
                    <th scope="col" class="px-3 py-3">Adj(+)</th>
                    <th scope="col" class="px-3 py-3">Adj(-)</th>
                    <th scope="col" class="px-3 py-3">Adj diff</th>
                    <th scope="col" class="px-3 py-3">Ending Stocks</th>
                    <th scope="col" class="px-3 py-3">Today's Stock</th>
                </tr>
            </thead>
            <tbody id="article_tbody">
                <tr>
                    <td colspan="16" class="px-3 py-4 text-center text-gray-500">
                        Silakan pilih filter dan klik Tampilkan untuk memuat data
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4">
        <div id="article_pagination_info" class="text-sm text-gray-600"></div>
        <div id="article_pagination_controls" class="flex items-center gap-2"></div>
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
    @include('app.updated_stock_card.stock_card_js_v2')
@endpush
@endsection
