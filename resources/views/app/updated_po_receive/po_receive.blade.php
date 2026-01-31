@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan data barang yang datang berdasarkan Purchase Order</p>
        </div>
    </div>
</div>

<!-- Filters and Actions -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Storage</label>
            <select class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="st_id_filter" name="st_id_filter">
                <option value="">- Storage -</option>
                @foreach ($data['st_id'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="status_filter">
                <option value="">Status</option>
                <option value="full">Fullfilled</option>
                <option value="not">Unfulfilled</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Date Filter</label>
            <select class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="date_filter">
                <option value="0">Abaikan Tanggal</option>
                <option value="1">Gunakan Tanggal</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
            <button type="button" class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer" id="kt_dashboard_daterangepicker">
                <span class="text-gray-500 mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                <span class="font-semibold" id="kt_dashboard_daterangepicker_date"></span>
            </button>
            <input type="hidden" id="report_date" value="" />
        </div>
    </div>
    <div class="flex justify-end gap-2">
        <button type="button" id="export_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
            <i class="fas fa-download mr-2"></i>Export Data
        </button>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-4">
        <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-64" id="po_receive_search" placeholder="Cari No PO">
    </div>
    <div class="overflow-x-auto">
        <table id="PoReceiveTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3 whitespace-nowrap">No PO</th>
                    <th scope="col" class="px-3 py-3 whitespace-nowrap">Store</th>
                    <th scope="col" class="px-3 py-3 whitespace-nowrap">Tgl PO</th>
                    <th scope="col" class="px-3 py-3 whitespace-nowrap">Tgl Terima</th>
                    <th scope="col" class="px-3 py-3 text-right">Qty PO</th>
                    <th scope="col" class="px-3 py-3 text-right">Qty Terima</th>
                    <th scope="col" class="px-3 py-3 text-right">Total PO</th>
                    <th scope="col" class="px-3 py-3 text-right">Total Terima</th>
                </tr>
            </thead>
            <tbody id="po_receive_tbody">
                <tr>
                    <td colspan="9" class="px-3 py-4 text-center text-gray-500">
                        Silakan pilih tanggal untuk memuat data
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex items-center justify-between">
        <div id="po_receive_pagination_info" class="text-sm text-gray-700"></div>
        <div id="po_receive_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@include('app.updated_po_receive.po_receive_modal')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js') {{-- This should load jQuery --}}
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @include('app.updated_po_receive.po_receive_js')
@endpush
@endsection
