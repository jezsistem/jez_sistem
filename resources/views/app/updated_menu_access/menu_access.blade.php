@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola menu access</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <button type="button" id="export_menu_access_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
                <div id="export_menu_access_menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <button type="button" id="export_menu_access_excel_btn" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </button>
                    </div>
                </div>
            </div>
            <button id="add_menu_access_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-plus mr-2"></i>Data Baru
            </button>
        </div>
    </div>
</div>

<!-- Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="search" id="menu_access_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Cari menu...">
        <select id="mt_id_filter" name="mt_id_filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            <option value="">- Main Menu -</option>
            @foreach ($data['mt_id'] as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="MenuAccessTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Main Menu</th>
                    <th scope="col" class="px-3 py-3">Menu Access</th>
                    <th scope="col" class="px-3 py-3">Urutan</th>
                    <th scope="col" class="px-3 py-3">Slug</th>
                </tr>
            </thead>
            <tbody id="menu_access_tbody">
                <tr>
                    <td colspan="5" class="px-3 py-4 text-center text-gray-500">
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <div id="menu_access_pagination_info" class="text-sm text-gray-700"></div>
        <div id="menu_access_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@include('app.updated_menu_access.menu_access_modal')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
    @include('app._partials.js')
    @include('app.updated_menu_access.menu_access_js')
@endpush
@endsection
