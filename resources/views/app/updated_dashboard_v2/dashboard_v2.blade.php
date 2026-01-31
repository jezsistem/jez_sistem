@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Dashboard informasi penjualan, profit, pembelian, asset, dan hutang</p>
        </div>
        <div class="flex gap-2">
            <button type="button" id="synchronize_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                Synchronize
            </button>
            <input type="hidden" id="dashboard_date" value="" />
            <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer" id="kt_dashboard_daterangepicker">
                <span class="text-gray-500 mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                <span class="font-semibold" id="kt_dashboard_daterangepicker_date"></span>
            </button>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    <!-- Jual Bersih -->
    <div class="bg-blue-600 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium">Jual Bersih</span>
        </div>
        <div class="text-3xl font-bold" id="nett_sales_label">0</div>
    </div>
    
    <!-- Profit -->
    <div class="bg-blue-600 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium">Profit</span>
        </div>
        <div class="text-3xl font-bold" id="profit_label">0</div>
    </div>
    
    <!-- Pembelian -->
    <div class="bg-white border border-gray-300 rounded-lg p-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Pembelian</span>
        </div>
        <div class="text-3xl font-bold text-gray-900" id="purchase_label">0</div>
    </div>
    
    <!-- Asset CC -->
    <div class="bg-blue-600 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium" id="cash_credit_asset_label">Asset CC</span>
        </div>
        <div class="text-3xl font-bold" id="assets_label">0</div>
    </div>
    
    <!-- Asset CON -->
    <div class="bg-blue-600 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium" id="consignment_asset_label">Asset CON</span>
        </div>
        <div class="text-3xl font-bold" id="consign_assets_label">0</div>
    </div>
    
    <!-- Hutang -->
    <div class="bg-blue-600 rounded-lg p-6 text-white">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium">Hutang</span>
        </div>
        <div class="text-3xl font-bold" id="debt_label">0</div>
    </div>
</div>

<!-- Store Table Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Store</h3>
        <div class="flex gap-2">
            <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-64" id="store_search" placeholder="Cari store">
            <button type="button" id="store_excel" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i>Excel
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table id="StoreTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Tanggal</th>
                    <th scope="col" class="px-3 py-3">Store</th>
                    <th scope="col" class="px-3 py-3 text-right">Sales</th>
                    <th scope="col" class="px-3 py-3 text-right">Profit</th>
                    <th scope="col" class="px-3 py-3 text-right">Purchase</th>
                    <th scope="col" class="px-3 py-3 text-right">Asset CC</th>
                    <th scope="col" class="px-3 py-3 text-right">Asset Con</th>
                    <th scope="col" class="px-3 py-3 text-right">Hutang</th>
                </tr>
            </thead>
            <tbody id="store_tbody">
                <tr>
                    <td colspan="9" class="px-3 py-4 text-center text-gray-500">
                        Silakan pilih tanggal untuk memuat data
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex items-center justify-between">
        <div id="store_pagination_info" class="text-sm text-gray-700"></div>
        <div id="store_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

<!-- Brand Table Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Brand</h3>
        <div class="flex gap-2">
            <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-64" id="brand_search" placeholder="Cari brand">
            <button type="button" id="brand_excel" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i>Excel
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table id="BrandTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Tanggal</th>
                    <th scope="col" class="px-3 py-3">Brand</th>
                    <th scope="col" class="px-3 py-3 text-right">Sales</th>
                    <th scope="col" class="px-3 py-3 text-right">Profit</th>
                    <th scope="col" class="px-3 py-3 text-right">Purchase</th>
                    <th scope="col" class="px-3 py-3 text-right">Asset CC</th>
                    <th scope="col" class="px-3 py-3 text-right">Asset Con</th>
                    <th scope="col" class="px-3 py-3 text-right">Hutang</th>
                </tr>
            </thead>
            <tbody id="brand_tbody">
                <tr>
                    <td colspan="9" class="px-3 py-4 text-center text-gray-500">
                        Silakan pilih tanggal untuk memuat data
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex items-center justify-between">
        <div id="brand_pagination_info" class="text-sm text-gray-700"></div>
        <div id="brand_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@include('app.updated_dashboard_v2.dashboard_v2_modal')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js') {{-- This should load jQuery --}}
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
    @include('app.updated_dashboard_v2.dashboard_v2_js')
@endpush
@endsection
