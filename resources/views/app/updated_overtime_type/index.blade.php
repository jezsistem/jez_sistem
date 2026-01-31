@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <button type="button" id="export_overtime_type_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
                <div id="export_overtime_type_menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                    <div class="py-1">
                        <button type="button" id="export_overtime_type_excel_btn" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </button>
                    </div>
                </div>
            </div>
            @php
                $canCreate = function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'create');
            @endphp
            @if($canCreate)
            <button id="add_overtime_type_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-plus mr-2"></i>Data Baru
            </button>
            @endif
        </div>
    </div>
</div>

<!-- Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <!-- Filters -->
    <div class="mb-6">
        <input type="search" id="overtime_type_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari overtime type...">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="OvertimeTypeTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Type Name</th>
                    <th scope="col" class="px-3 py-3">Description</th>
                    <th scope="col" class="px-3 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="overtime_type_tbody">
                <tr>
                    <td colspan="4" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <div id="overtime_type_pagination_info" class="text-sm text-gray-700"></div>
        <div id="overtime_type_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@include('app.updated_overtime_type.overtime_type_modal')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
    @include('app._partials.js')
    @include('app.updated_overtime_type.overtime_type_js')
@endpush
@endsection
