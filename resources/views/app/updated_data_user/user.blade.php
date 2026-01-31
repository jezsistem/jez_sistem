@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data user dan user level</p>
        </div>
        <div>
            <button type="button" id="toggle_user_level_btn" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                <i class="fas fa-users mr-2"></i>User Level
            </button>
        </div>
    </div>
</div>

<!-- User Level Section (Collapsible) -->
<div id="user_level_section" class="bg-white rounded-lg shadow-md p-6 mb-6 hidden">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">User Level</h3>
        <div class="flex gap-2">
            <input type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2 w-64" id="group_search" placeholder="Cari user level">
            <button type="button" id="add_group_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-plus mr-2"></i>Data Baru
            </button>
            <div class="relative">
                <button type="button" id="export_group_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
                <div id="export_group_menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <button type="button" id="export_group_excel_btn" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table id="GroupTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Nama Level</th>
                    <th scope="col" class="px-3 py-3">Deskripsi</th>
                </tr>
            </thead>
            <tbody id="group_tbody">
                <tr>
                    <td colspan="3" class="px-3 py-4 text-center text-gray-500">
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="mt-4 flex items-center justify-between">
        <div id="group_pagination_info" class="text-sm text-gray-700"></div>
        <div id="group_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

<!-- Filters and Actions -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status User</label>
            <select class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 px-3 py-2" id="filter_delete" name="filter_delete">
                <option value="">- Pilih Status User -</option>
                <option value="0">Active</option>
                <option value="1">Non Active</option>
            </select>
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
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Actions</label>
            <button type="button" id="add_user_btn" class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
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
                    <th scope="col" class="px-3 py-3">Menu Access</th>
                    <th scope="col" class="px-3 py-3">Delete Access</th>
                    <th scope="col" class="px-3 py-3">Divisi</th>
                    <th scope="col" class="px-3 py-3">Store</th>
                    <th scope="col" class="px-3 py-3">Kode</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                    <th scope="col" class="px-3 py-3">POS Access</th>
                    <th scope="col" class="px-3 py-3">Pick Access</th>
                    <th scope="col" class="px-3 py-3">Manual Attendance</th>
                    <th scope="col" class="px-3 py-3">Detail</th>
                </tr>
            </thead>
            <tbody id="user_tbody">
                <tr>
                    <td colspan="13" class="px-3 py-4 text-center text-gray-500">
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

@include('app.updated_data_user.user_modal')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @include('app._partials.js')
    @include('app.updated_data_user.user_js')
@endpush
@endsection
