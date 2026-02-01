@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola rating user</p>
        </div>
        <div>
            <input type="hidden" id="user_rating_date" value=""/>
            <button type="button" id="kt_dashboard_daterangepicker" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                <span id="kt_dashboard_daterangepicker_title">Today</span>
                <span id="kt_dashboard_daterangepicker_date" class="ml-2"></span>
            </button>
        </div>
    </div>
</div>

<!-- Rating Summary Card -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary Rating</h3>
        <div class="flex justify-between items-center mb-4">
            <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-64" id="user_search" placeholder="Cari kasir">
            <div class="relative">
                <button type="button" id="export_rating_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
                <div id="export_rating_menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <button type="button" id="export_rating_excel_btn" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table id="RatingTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Kasir</th>
                    <th scope="col" class="px-3 py-3">Store</th>
                    <th scope="col" class="px-3 py-3">Divisi</th>
                    <th scope="col" class="px-3 py-3">Penilaian</th>
                    <th scope="col" class="px-3 py-3">Total Rating</th>
                </tr>
            </thead>
            <tbody id="rating_tbody">
                <tr>
                    <td colspan="6" class="px-3 py-4 text-center text-gray-500">
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex items-center justify-between">
        <div id="rating_pagination_info" class="text-sm text-gray-700"></div>
        <div id="rating_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

<!-- Rating History Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">History Rating</h3>
        <div class="flex justify-between items-center mb-4">
            <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-64" id="user_history_search" placeholder="Cari kasir">
            <div class="relative">
                <button type="button" id="export_history_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
                <div id="export_history_menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <button type="button" id="export_history_excel_btn" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table id="RatingHistoryTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Kasir</th>
                    <th scope="col" class="px-3 py-3">Store</th>
                    <th scope="col" class="px-3 py-3">Divisi</th>
                    <th scope="col" class="px-3 py-3">Invoice</th>
                    <th scope="col" class="px-3 py-3">Customer</th>
                    <th scope="col" class="px-3 py-3">Value</th>
                    <th scope="col" class="px-3 py-3">Reason</th>
                    <th scope="col" class="px-3 py-3">Tanggal</th>
                </tr>
            </thead>
            <tbody id="rating_history_tbody">
                <tr>
                    <td colspan="9" class="px-3 py-4 text-center text-gray-500">
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex items-center justify-between">
        <div id="rating_history_pagination_info" class="text-sm text-gray-700"></div>
        <div id="rating_history_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@include('app.updated_user_rating.user_rating_modal')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js') {{-- This should load jQuery --}}
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    @include('app.updated_user_rating.user_rating_js')
@endpush
@endsection
