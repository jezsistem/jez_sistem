@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data user</p>
        </div>
    </div>
</div>

<!-- Filters and Actions -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Actions</label>
            <button type="button" id="add_btn" class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-plus mr-2"></i>Data Baru
            </button>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-4">
        <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-64" id="user_search" placeholder="Cari user">
    </div>
    <div class="overflow-x-auto">
        <table id="UserTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Nama</th>
                    <th scope="col" class="px-3 py-3">Level</th>
                    <th scope="col" class="px-3 py-3">Divisi</th>
                    <th scope="col" class="px-3 py-3">Store</th>
                    <th scope="col" class="px-3 py-3">NIP</th>
                    <th scope="col" class="px-3 py-3">KTP</th>
                    <th scope="col" class="px-3 py-3">Kode</th>
                    <th scope="col" class="px-3 py-3">No Telp</th>
                    <th scope="col" class="px-3 py-3">Email</th>
                    <th scope="col" class="px-3 py-3">Alamat</th>
                </tr>
            </thead>
            <tbody id="user_tbody">
                <tr>
                    <td colspan="11" class="px-3 py-4 text-center text-gray-500">
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex items-center justify-between">
        <div id="user_pagination_info" class="text-sm text-gray-700"></div>
        <div id="user_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@include('app.updated_user_management.user_management_modal')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_user_management.user_management_js')
@endpush
@endsection
