@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Laporan shift user berdasarkan tanggal dan store</p>
        </div>
    </div>
</div>

<!-- Filters and Actions -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
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
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
            <input type="hidden" id="sales_date" value="" />
            <button type="button" class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer" id="kt_dashboard_daterangepicker">
                <span class="text-gray-500 mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                <span class="font-semibold" id="kt_dashboard_daterangepicker_date"></span>
            </button>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Actions</label>
            <div class="relative">
                <button type="button" id="export_btn" class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
                <div id="export_menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <button type="button" id="export_excel_btn" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-4">
        <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-64" id="user_shift_search" placeholder="Cari Nama Shift">
    </div>
    <div class="overflow-x-auto">
        <table id="UserShiftTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Nama</th>
                    <th scope="col" class="px-3 py-3">Toko</th>
                    <th scope="col" class="px-3 py-3">Tanggal</th>
                    <th scope="col" class="px-3 py-3">Mulai</th>
                    <th scope="col" class="px-3 py-3">Selesai</th>
                    <th scope="col" class="px-3 py-3 text-right">Total Expected</th>
                    <th scope="col" class="px-3 py-3 text-right">Total Actual</th>
                    <th scope="col" class="px-3 py-3 text-right">Actual Ending Cash</th>
                    <th scope="col" class="px-3 py-3 text-right">Difference</th>
                </tr>
            </thead>
            <tbody id="user_shift_tbody">
                <tr>
                    <td colspan="10" class="px-3 py-4 text-center text-gray-500">
                        Silakan pilih tanggal untuk memuat data
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex items-center justify-between">
        <div id="user_shift_pagination_info" class="text-sm text-gray-700"></div>
        <div id="user_shift_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@include('app.updated_report_shift.shift_modal')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js') {{-- This should load jQuery --}}
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @include('app.updated_report_shift.shift_js')
@endpush
@endsection
