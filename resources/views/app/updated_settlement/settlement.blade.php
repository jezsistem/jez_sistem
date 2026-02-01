@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola settlement transaksi</p>
        </div>
    </div>
</div>

<!-- Filter Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h4 class="text-lg font-semibold text-gray-900 mb-6">Filter Transaksi</h4>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
            <input type="date" id="start_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
            <input type="date" id="end_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div id="payment_method">
            <!-- Payment method akan di-load via AJAX -->
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Outlet</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="st_id" name="st_id" onchange="loadPaymentMethods()">
                <option value="0">-- Pilih Outlet --</option>
                @foreach ($data['st_id'] as $storeId => $storeName)
                    <option value="{{ $storeId }}">{{ $storeName }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status TRX</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="status_trx">
                <option value="0">-- Pilih Status --</option>
                @foreach ($data['statusses'] as $status)
                    <option value="{{ $status }}">{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status Settlement</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="status_settle">
                <option value="0">-- Pilih Status --</option>
                <option value="Settled">Settled</option>
                <option value="Unsettled">Unsettled</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status COGS</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="status_cogs">
                <option value="0">-- Pilih Status --</option>
                <option value="Calculated">Calculated</option>
                <option value="Uncalculated">Uncalculated</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sub Payment</label>
            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="sub_payment_filter">
                <option value="0">-- Pilih Sub Payment --</option>
                <option value="1">CASH</option>
                <option value="2">COD</option>
                <option value="3">ON US</option>
                <option value="4">OFF US</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Invoice / Order Number</label>
            <input type="text" id="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Search...">
        </div>
    </div>
    <div class="flex gap-2 mt-4">
        <button type="button" id="filter_btn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
            <i class="fas fa-filter mr-2"></i>Filter
        </button>
        <button type="button" id="reset_btn" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            <i class="fas fa-undo mr-2"></i>Reset
        </button>
        <div class="relative">
            <button type="button" onclick="toggleExportMenu('settlementExportMenu')" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors flex items-center gap-2">
                <i class="cft-standard-stroke cft-download"></i>
                Export
            </button>
            <div id="settlementExportMenu" class="hidden absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                <div class="py-1">
                    <a href="#" id="export_trx" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="cft-standard-stroke cft-excel mr-2"></i>
                        Export Transaction
                    </a>
                    <a href="#" id="export_detail_trx" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="cft-standard-stroke cft-excel mr-2"></i>
                        Export Detail Transaction
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div>
            <h3 class="text-sm font-medium text-gray-600 mb-1">Total Net Sales</h3>
            <h1 class="text-xl font-semibold text-green-600">Rp. <span id="total_netsales">0</span></h1>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-600 mb-1">Total COGS</h3>
            <h1 class="text-xl font-semibold text-green-600">Rp. <span id="total_cogs">0</span></h1>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-600 mb-1">Total Margin</h3>
            <h1 class="text-xl font-semibold text-green-600">Rp. <span id="total_margin">0</span></h1>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-600 mb-1">Presentase Margin</h3>
            <h1 class="text-xl font-semibold text-green-600"><span id="margin_percentage">0</span></h1>
        </div>
        <div>
            <h3 class="text-sm font-medium text-gray-600 mb-1">Dana Cair</h3>
            <h1 class="text-xl font-semibold text-green-600">Rp. <span id="total_dana_cair">0</span></h1>
        </div>
    </div>
    <div class="mt-6 pt-6 border-t flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <p class="text-sm text-gray-600">Selected for settlement: <span id="selected" class="font-semibold">0</span> transaction</p>
            <p class="text-sm text-gray-600 mt-1"><strong>Selected Net Sales:</strong> <span class="text-green-600 font-bold">Rp</span> <span class="text-green-600 font-bold" id="selected_netsales">0</span></p>
        </div>
        <div class="flex gap-2">
            <button type="button" id="settlement_btn" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900">
                <i class="fas fa-check mr-2"></i>Settlement
            </button>
            <button type="button" id="calc_cogs_tag_btn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                <i class="fas fa-check mr-2"></i>Calc Cogs & Price Tag
            </button>
        </div>
    </div>
</div>

<!-- Net Sales by Payment Method -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <p class="text-lg font-semibold text-gray-900 mb-4">Net Sales by Payment Method</p>
    <div id="payment_calc_cards" class="flex flex-wrap gap-4">
        <!-- Cards akan di-load via AJAX -->
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="overflow-x-auto">
        <table id="SettlementTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">
                        <input type="checkbox" name="check_all_data" id="check_all_data">
                    </th>
                    <th scope="col" class="px-3 py-3">Tanggal Transaksi</th>
                    <th scope="col" class="px-3 py-3">Receipt Number</th>
                    <th scope="col" class="px-3 py-3">Outlet</th>
                    <th scope="col" class="px-3 py-3">Qty</th>
                    <th scope="col" class="px-3 py-3">Net Sales</th>
                    <th scope="col" class="px-3 py-3">COGS</th>
                    <th scope="col" class="px-3 py-3">Payment Method</th>
                    <th scope="col" class="px-3 py-3">Sub Payment</th>
                    <th scope="col" class="px-3 py-3">Status TRX</th>
                    <th scope="col" class="px-3 py-3">Status Settle</th>
                </tr>
            </thead>
            <tbody id="settlement_tbody">
                <tr>
                    <td colspan="11" class="px-3 py-4 text-center text-gray-500">
                        Silakan pilih Tanggal Mulai dan Tanggal Akhir, lalu klik Filter
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4">
        <div id="settlement_pagination_info" class="text-sm text-gray-600"></div>
        <div id="settlement_pagination_controls" class="flex items-center gap-2"></div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_settlement.settlement_js_v2')
@endpush

@include('app.updated_settlement.settlement_modal_v2')
@endsection
