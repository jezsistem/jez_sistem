@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data customer</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <!-- Division Filter -->
            <select class="px-3 py-2 text-sm bg-blue-600 text-white border border-blue-600 rounded-lg focus:ring-2 focus:ring-blue-500" id="stt_filter">
                <option value="">- Divisi -</option>
                <option value="online">ONLINE</option>
                <option value="offline">OFFLINE</option>
            </select>
            
            <!-- Customer Type Filter -->
            <select class="px-3 py-2 text-sm bg-blue-600 text-white border border-blue-600 rounded-lg focus:ring-2 focus:ring-blue-500" id="cust_type_filter">
                <option value="">- Tipe Customer -</option>
                @foreach ($data['ct_id'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            
            <!-- Date Filter Toggle -->
            <select class="px-3 py-2 text-sm bg-blue-100 text-gray-700 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500" id="date_filter">
                <option value="0">- Filter Tanggal -</option>
                <option value="1">Aktif</option>
            </select>
            
            <!-- Graph Button -->
            <button id="graph_btn" class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600 focus:ring-2 focus:ring-blue-500">
                <i class="fa fa-bar-chart"></i>
            </button>
            
            <!-- Date Range Picker -->
            <button id="kt_dashboard_daterangepicker" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-500 border border-blue-500 rounded-lg hover:bg-blue-600 focus:ring-2 focus:ring-blue-400 cursor-pointer">
                <span id="kt_dashboard_daterangepicker_title" class="mr-1">Hari Ini:</span>
                <span id="kt_dashboard_daterangepicker_date" class="font-semibold"></span>
                <i class="fa fa-calendar ml-2"></i>
            </button>
        </div>
    </div>
</div>

<!-- Hidden inputs for date range -->
<input type="hidden" id="po_date" name="po_date" value="">

<!-- Province & City Summary Tables -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Province Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer per Provinsi</h3>
        <div class="overflow-x-auto max-h-64">
            <table class="min-w-full divide-y divide-gray-200" id="ProvinceTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Provinsi</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    </tr>
                </thead>
                <tbody id="ProvinceTableBody" class="bg-white divide-y divide-gray-200 text-sm">
                    <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin"></i> Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- City Rank Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer per Kota</h3>
        <div class="overflow-x-auto max-h-64">
            <table class="min-w-full divide-y divide-gray-200" id="CityRankTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kota</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    </tr>
                </thead>
                <tbody id="CityRankTableBody" class="bg-white divide-y divide-gray-200 text-sm">
                    <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin"></i> Memuat...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Customer Type Section (Collapsible) -->
<div id="customerTypeSection" class="hidden mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Tipe Customer</h3>
            <div class="flex items-center gap-2">
                <button id="add_customer_type_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    <i class="fa fa-plus mr-2"></i> Data Baru
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <input type="search" id="customer_type_search" class="w-full md:w-80 px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Cari tipe customer...">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="CustomerTypeTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody id="CustomerTypeTableBody" class="bg-white divide-y divide-gray-200">
                        <tr><td colspan="3" class="px-4 py-4 text-center text-gray-500"><i class="fa fa-spinner fa-spin"></i> Memuat...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Main Customer Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="flex flex-col md:flex-row items-center justify-between px-6 py-4 border-b border-gray-200 gap-4">
        <h3 class="text-lg font-semibold text-gray-900">Detail Customer</h3>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Export Button -->
            <a href="{{ url('customer_data_export') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                <i class="fa fa-download mr-2"></i> Export
            </a>
            <!-- Import Button -->
            <button id="import_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                <i class="fa fa-upload mr-2"></i> Import
            </button>
            <!-- Add Customer Button -->
            <button id="add_customer_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                <i class="fa fa-plus mr-2"></i> Data Baru
            </button>
            <!-- Toggle Customer Type -->
            <button id="toggle_customer_type_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fa fa-tags mr-2"></i> Tipe Customer
            </button>
        </div>
    </div>
    <div class="p-6">
        <!-- Search & Per Page -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa fa-search text-gray-400"></i>
                </div>
                <input type="search" id="customer_search" class="block w-full pl-10 pr-4 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari nama, telepon, email...">
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Show</span>
                <select id="per_page" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="10">10</option>
                    <option value="25" selected>25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>
        </div>
        
        <!-- Table Container -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="CustomerTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Toko</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Telp</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alamat</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Belanja</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Join</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Belanja</th>
                    </tr>
                </thead>
                <tbody id="CustomerTableBody" class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fa fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i>
                                <span>Memuat data...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
            <div class="text-sm text-gray-600">
                Menampilkan <span id="showing_start">0</span> - <span id="showing_end">0</span> dari <span id="total_records">0</span> data
            </div>
            <div id="pagination_container" class="flex items-center gap-1">
                <!-- Pagination buttons will be generated by JS -->
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('scripts')
    @include('app._partials.js')
    @include('app.updated_customer.customer_js_v2')
@endpush

@include('app.updated_customer.customer_modal_v2')
@endsection
