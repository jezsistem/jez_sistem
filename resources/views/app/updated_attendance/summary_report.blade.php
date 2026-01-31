@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Attendance Summary Report</h1>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="exportToExcel()" class="px-4 py-2 text-sm font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </button>
            <button type="button" onclick="exportToPDF()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-red-50 border-red-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-user-square text-2xl text-red-500"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="total-staff">{{ $summaryStats['total_staff'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Staff</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-yellow-50 border-yellow-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar text-2xl text-yellow-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="total-shifts">{{ $summaryStats['total_shifts'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Shifts</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-green-50 border-green-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-entrance-left text-2xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="late-days">{{ $summaryStats['late_days'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Late Days</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-blue-50 border-blue-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-time text-2xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="scan-once-days">{{ $summaryStats['scan_once_days'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Scan Once</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-gray-50 border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-cross-circle text-2xl text-gray-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="alpha-days">{{ $summaryStats['alpha_days'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Alpha Days</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label for="date_filter" class="block text-sm font-medium text-gray-700 mb-1">Date Filter</label>
            <select id="date_filter" name="date_filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" onchange="handleDateFilterChange(this.value)">
                <option value="this_week" {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="past_week" {{ $dateFilter == 'past_week' ? 'selected' : '' }}>Past Week</option>
                <option value="this_month" {{ $dateFilter == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $dateFilter == 'last_month' ? 'selected' : '' }}>Past Month</option>
                <option value="custom" {{ $dateFilter == 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>
        </div>
        <div id="start_date_container" style="display: {{ $dateFilter == 'custom' ? 'block' : 'none' }};">
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div id="end_date_container" style="display: {{ $dateFilter == 'custom' ? 'block' : 'none' }};">
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div>
            <label for="division_id" class="block text-sm font-medium text-gray-700 mb-1">Division</label>
            <select id="division_id" name="division_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">All Divisions</option>
                @foreach($divisions as $division)
                    <option value="{{ $division->id }}">{{ $division->ud_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input type="text" id="search" name="search" placeholder="Search name or NIP..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
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
    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="SummaryTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">NIP</th>
                    <th scope="col" class="px-3 py-3">Staff</th>
                    <th scope="col" class="px-3 py-3">Position</th>
                    <th scope="col" class="px-3 py-3">Division</th>
                    <th scope="col" class="px-3 py-3">User Type</th>
                    <th scope="col" class="px-3 py-3 text-center">Total Shifts</th>
                    <th scope="col" class="px-3 py-3 text-center">Present Days</th>
                    <th scope="col" class="px-3 py-3 text-center">Day off</th>
                    <th scope="col" class="px-3 py-3 text-center">Leave Days</th>
                    <th scope="col" class="px-3 py-3 text-center">Sick Days</th>
                    <th scope="col" class="px-3 py-3 text-center">Late Days</th>
                    <th scope="col" class="px-3 py-3 text-center">Scan Once</th>
                    <th scope="col" class="px-3 py-3 text-center">Alpha Days</th>
                </tr>
            </thead>
            <tbody id="summary_tbody">
                <tr>
                    <td colspan="14" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <div id="summary_pagination_info" class="text-sm text-gray-700"></div>
        <div id="summary_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_attendance.summary_report_js')
@endpush
@endsection
