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
                <button type="button" id="export_staff_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
                <div id="export_staff_menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                    <div class="py-1">
                        <button type="button" id="export_staff_excel_btn" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <!-- Filters -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="position_filter" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
            <select id="position_filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">All Positions</option>
                @foreach($data['positions'] as $position)
                    <option value="{{ $position->id }}">{{ $position->up_code }} - {{ $position->up_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="division_filter" class="block text-sm font-medium text-gray-700 mb-2">Division</label>
            <select id="division_filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">All Divisions</option>
                @foreach($data['divisions'] as $division)
                    <option value="{{ $division->id }}">{{ $division->ud_code }} - {{ $division->ud_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="staff_search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="search" id="staff_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Cari staff...">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
            <button type="button" id="apply_filters_btn" class="w-full px-4 py-2.5 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-filter mr-2"></i>Apply Filters
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="StaffTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">NIP</th>
                    <th scope="col" class="px-3 py-3">Staff</th>
                    <th scope="col" class="px-3 py-3">Position</th>
                    <th scope="col" class="px-3 py-3">Division</th>
                    <th scope="col" class="px-3 py-3">User Type</th>
                    <th scope="col" class="px-3 py-3">Annual Leave Balance</th>
                    <th scope="col" class="px-3 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="staff_tbody">
                <tr>
                    <td colspan="8" class="px-3 py-4 text-center text-gray-500">
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <div id="staff_pagination_info" class="text-sm text-gray-700"></div>
        <div id="staff_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@include('app.updated_staff.staff_modal')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
    @include('app._partials.js')
    @include('app.updated_staff.staff_js')
@endpush
@endsection
