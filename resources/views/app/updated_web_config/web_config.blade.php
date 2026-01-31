@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola pengaturan ERP</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <button type="button" id="export_web_config_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
                <div id="export_web_config_menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <button type="button" id="export_web_config_excel_btn" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </button>
                    </div>
                </div>
            </div>
            <button id="reset_erp_btn" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700">
                <i class="fas fa-exclamation-triangle mr-2"></i>Reset Seluruh Data ERP
            </button>
        </div>
    </div>
</div>

<!-- Warning Notice -->
<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
    <div class="flex items-start">
        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-0.5 mr-3"></i>
        <div class="flex-1">
            <p class="text-sm text-yellow-800 font-medium">
                Reset akan menghapus seluruh data pada ERP kecuali: menu, menu akses, brand, kategori, ecommerce bank, data accounting, metode pembayaran, pengaturan erp dan tipe stok.
            </p>
        </div>
    </div>
</div>

<!-- Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Filters -->
    <div class="mb-6">
        <input type="search" id="web_config_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari pengaturan...">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="WebConfigTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Nama</th>
                    <th scope="col" class="px-3 py-3">Nilai</th>
                </tr>
            </thead>
            <tbody id="web_config_tbody">
                <tr>
                    <td colspan="3" class="px-3 py-4 text-center text-gray-500">
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <div id="web_config_pagination_info" class="text-sm text-gray-700"></div>
        <div id="web_config_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@include('app.updated_web_config.web_config_modal')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
    @include('app._partials.js')
    @include('app.updated_web_config.web_config_js')
@endpush
@endsection
