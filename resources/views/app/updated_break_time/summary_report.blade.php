@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Break Time Summary Report</h1>
            <p class="text-sm text-gray-600 mt-1">
                @if($data['dateFilter'] && $data['dateFilter'] !== 'custom')
                    {{ date('d M Y', strtotime($data['startDate'])) }} to {{ date('d M Y', strtotime($data['endDate'])) }}
                @else
                    {{ $data['startDate'] }} to {{ $data['endDate'] }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('break-times_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
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
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6" id="stats-container">
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-green-50 border-green-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-user-square text-2xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="stat-total-staff">0</div>
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
                <div class="text-xl font-semibold text-gray-900" id="stat-total-shifts">0</div>
                <div class="text-sm text-gray-600">Total Shifts</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-red-50 border-red-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-coffee text-2xl text-red-500"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="stat-total-breaks">0</div>
                <div class="text-sm text-gray-600">Total Breaks</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-gray-50 border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-times-circle text-2xl text-gray-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="stat-no-break-shifts">0</div>
                <div class="text-sm text-gray-600">No Break Shifts</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-green-50 border-green-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-clock text-2xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="stat-exceeded-break-time">0</div>
                <div class="text-sm text-gray-600">Exceeded Break</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-yellow-50 border-yellow-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calculator text-2xl text-yellow-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="stat-avg-breaks-per-staff">0</div>
                <div class="text-sm text-gray-600">Avg Breaks/Staff</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <label for="date_filter" class="block text-sm font-medium text-gray-700 mb-1">Date Filter</label>
            <select id="date_filter" name="date_filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" onchange="handleDateFilterChange(this.value)">
                <option value="this_week" {{ $data['dateFilter'] == 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="past_week" {{ $data['dateFilter'] == 'past_week' ? 'selected' : '' }}>Past Week</option>
                <option value="this_month" {{ $data['dateFilter'] == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $data['dateFilter'] == 'last_month' ? 'selected' : '' }}>Past Month</option>
                <option value="custom" {{ $data['dateFilter'] == 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>
        </div>
        <div id="start_date_container" style="display: {{ $data['dateFilter'] == 'custom' ? 'block' : 'none' }};">
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="{{ $data['startDate'] }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div id="end_date_container" style="display: {{ $data['dateFilter'] == 'custom' ? 'block' : 'none' }};">
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input type="date" id="end_date" name="end_date" value="{{ $data['endDate'] }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
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
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <!-- Search -->
    <div class="mb-6">
        <input type="search" id="summary_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari staff...">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="SummaryTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Staff</th>
                    <th scope="col" class="px-3 py-3">NIP</th>
                    <th scope="col" class="px-3 py-3">Division</th>
                    <th scope="col" class="px-3 py-3">Total Shifts</th>
                    <th scope="col" class="px-3 py-3">Total Breaks</th>
                    <th scope="col" class="px-3 py-3">No Break Shifts</th>
                    <th scope="col" class="px-3 py-3">Exceeded Break</th>
                </tr>
            </thead>
            <tbody id="summary_tbody">
                <tr>
                    <td colspan="8" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
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
    @include('app.updated_break_time.summary_report_js')
@endpush
@endsection
