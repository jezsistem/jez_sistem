@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Break Time Details - {{ $data['staff']->u_name }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('break-times.summary-report_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>
</div>

<!-- Staff Information Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6 bg-red-50 border-red-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="flex-1">
            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $data['staff']->u_name }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                <div><strong>NIP:</strong> {{ $data['staff']->u_nip }}</div>
                <div><strong>Division:</strong> {{ $data['staff']->division_name ?? '-' }}</div>
                <div><strong>Position:</strong> {{ $data['staff']->position_name ?? '-' }}</div>
                <div><strong>Work Type:</strong> {{ $data['staff']->work_type ?? '-' }}</div>
                @if($data['staff']->u_email)
                <div><strong>Email:</strong> {{ $data['staff']->u_email }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6" id="stats-container">
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-green-50 border-green-200">
        <div class="text-center">
            <div class="text-xl font-semibold text-gray-900" id="stat-total-breaks">0</div>
            <div class="text-sm text-gray-600 mt-1">Total Breaks</div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-yellow-50 border-yellow-200">
        <div class="text-center">
            <div class="text-xl font-semibold text-gray-900" id="stat-active-breaks">0</div>
            <div class="text-sm text-gray-600 mt-1">Active</div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-green-50 border-green-200">
        <div class="text-center">
            <div class="text-xl font-semibold text-gray-900" id="stat-completed-breaks">0</div>
            <div class="text-sm text-gray-600 mt-1">Completed</div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-red-50 border-red-200">
        <div class="text-center">
            <div class="text-xl font-semibold text-gray-900" id="stat-cancelled-breaks">0</div>
            <div class="text-sm text-gray-600 mt-1">Cancelled</div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-blue-50 border-blue-200">
        <div class="text-center">
            <div class="text-xl font-semibold text-gray-900" id="stat-break-1-count">0</div>
            <div class="text-sm text-gray-600 mt-1">Break 1</div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-indigo-50 border-indigo-200">
        <div class="text-center">
            <div class="text-xl font-semibold text-gray-900" id="stat-break-2-count">0</div>
            <div class="text-sm text-gray-600 mt-1">Break 2</div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="{{ $data['startDate'] }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input type="date" id="end_date" name="end_date" value="{{ $data['endDate'] }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <!-- Search and Actions -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <input type="search" id="staff_break_time_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari...">
        <div class="flex items-center gap-3">
            <button type="button" onclick="exportToExcel()" class="px-4 py-2 text-sm font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </button>
            <button type="button" onclick="exportToPDF()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="StaffBreakTimeTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Date</th>
                    <th scope="col" class="px-3 py-3">Type</th>
                    <th scope="col" class="px-3 py-3">Start Time</th>
                    <th scope="col" class="px-3 py-3">End Time</th>
                    <th scope="col" class="px-3 py-3">Duration</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                    <th scope="col" class="px-3 py-3">Notes</th>
                    <th scope="col" class="px-3 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="staff_break_time_tbody">
                <tr>
                    <td colspan="9" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <div id="staff_break_time_pagination_info" class="text-sm text-gray-700"></div>
        <div id="staff_break_time_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_break_time.staff_detail_js')
@endpush
@endsection
