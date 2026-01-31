@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola cek dana online</p>
        </div>
        <div class="flex gap-2">
            <button type="button" id="import_modal_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                <i class="fas fa-upload mr-2"></i>Import
            </button>
            <button type="button" id="export_btn" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                <i class="fas fa-download mr-2"></i>Export
            </button>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cari No Order / No Resi</label>
            <input type="search" id="cek_dana_online_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Cari...">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Store</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="st_id" name="st_id">
                <option value="">- Pilih Store -</option>
                @foreach ($data['st_id'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Platform</label>
            <select name="filter_platform" id="filter_platform" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">-- Pilih Platform --</option>
                <option value="tiktok">Tiktok</option>
                <option value="shopee">Shopee</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="filter_status" id="filter_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="0">-- Pilih Status --</option>
                <option value="1">Done</option>
                <option value="2">Belum Cair</option>
                <option value="3">Belum TRX</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex items-center gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transaksi</label>
                <input type="date" id="trx_date_start" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                <span class="mx-2">-</span>
                <input type="date" id="trx_date_end" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
            </div>
            <div class="flex items-center">
                <input type="checkbox" id="use_trx_date_filter" checked class="mr-2">
                <label for="use_trx_date_filter" class="text-sm text-gray-700">Gunakan filter</label>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Cash Out</label>
                <input type="date" id="cash_out_date_start" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
                <span class="mx-2">-</span>
                <input type="date" id="cash_out_date_end" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5">
            </div>
            <div class="flex items-center">
                <input type="checkbox" id="use_cash_out_date_filter" checked class="mr-2">
                <label for="use_cash_out_date_filter" class="text-sm text-gray-700">Gunakan filter</label>
            </div>
        </div>
    </div>
    <div class="mt-4">
        <button type="button" id="filter_btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            <i class="fas fa-filter mr-2"></i>Filter
        </button>
    </div>
</div>

<!-- Summary Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Total Dana Cair</h3>
            <h1 class="text-3xl font-bold text-green-600">Rp. <span id="total_dana_cair">0</span></h1>
        </div>
        <div class="flex flex-col md:flex-row items-center gap-4">
            <div class="text-right">
                <p class="text-sm text-gray-600">Selected for settlement: <span id="selected" class="font-semibold">0</span> transaction</p>
                <p class="text-sm text-gray-600 mt-1"><strong>Selected Dana Cair:</strong> <span class="text-green-600 font-bold">Rp</span> <span class="text-green-600 font-bold" id="selected_dana_cair">0</span></p>
            </div>
            <button type="button" id="settlement_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-check mr-2"></i>Settlement
            </button>
        </div>
    </div>
</div>

<!-- Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="CekDanaOnlinetb" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">
                        <input type="checkbox" name="check_all_data" id="check_all_data">
                    </th>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Store</th>
                    <th scope="col" class="px-3 py-3">Platform Name</th>
                    <th scope="col" class="px-3 py-3">No Order</th>
                    <th scope="col" class="px-3 py-3">Order Date Settlement</th>
                    <th scope="col" class="px-3 py-3">Total Revenue</th>
                    <th scope="col" class="px-3 py-3">Total Settlement Amount</th>
                    <th scope="col" class="px-3 py-3">Seller voucher discount</th>
                    <th scope="col" class="px-3 py-3">Total Fees</th>
                    <th scope="col" class="px-3 py-3">Presentase Fee</th>
                    <th scope="col" class="px-3 py-3">Presentase Seller Voucher</th>
                    <th scope="col" class="px-3 py-3">Tanggal Transaksi</th>
                    <th scope="col" class="px-3 py-3">Net Sales Jezpro</th>
                    <th scope="col" class="px-3 py-3">Diff Jezpro - MP</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                    <th scope="col" class="px-3 py-3">Refund</th>
                    <th scope="col" class="px-3 py-3">Settlement</th>
                </tr>
            </thead>
            <tbody id="cek_dana_online_tbody">
                <tr>
                    <td colspan="18" class="px-3 py-4 text-center text-gray-500">
                        <div class="flex justify-center items-center">
                            <svg class="animate-spin h-5 w-5 mr-3 text-blue-500" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memuat data...
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4">
        <div id="cek_dana_online_pagination_info" class="text-sm text-gray-600"></div>
        <div id="cek_dana_online_pagination_controls" class="flex items-center gap-2"></div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_cek_dana_online.cek_dana_online_js_v2')
@endpush

@include('app.updated_cek_dana_online.cek_dana_online_modal_v2')
@endsection
