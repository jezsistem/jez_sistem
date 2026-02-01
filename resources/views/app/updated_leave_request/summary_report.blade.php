@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                @if($data['dateFilter'] && $data['dateFilter'] !== 'custom')
                    {{ date('d M Y', strtotime($data['startDate'])) }} to {{ date('d M Y', strtotime($data['endDate'])) }}
                @else
                    {{ $data['startDate'] }} to {{ $data['endDate'] }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700" onclick="exportToExcel()">
                <i class="fas fa-file-excel mr-2"></i> Export Excel
            </button>
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700" onclick="exportToPDF()">
                <i class="fas fa-file-pdf mr-2"></i> Export PDF
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
@php
    $stats = $data['stats'] ?? [];
    $statsCount = is_array($stats) ? count($stats) : (is_object($stats) ? count((array)$stats) : 0);
@endphp
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
    @if(!empty($stats) && $statsCount > 0)
        @foreach($stats as $stat)
        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm
            @switch($stat->lr_status)
                @case('approved')
                    bg-red-50 border-red-200
                    @break
                @case('pending')
                    bg-yellow-50 border-yellow-200
                    @break
                @case('rejected')
                    bg-red-50 border-red-200
                    @break
                @case('cancelled')
                    bg-gray-50 border-gray-200
                    @break
                @default
                    @if(strpos($stat->lr_status, 'leave_') === 0)
                        bg-blue-50 border-blue-200
                    @else
                        bg-gray-50 border-gray-200
                    @endif
            @endswitch">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    @switch($stat->lr_status)
                        @case('approved')
                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                            @break
                        @case('pending')
                            <i class="fas fa-clock text-2xl text-yellow-600"></i>
                            @break
                        @case('rejected')
                            <i class="fas fa-times-circle text-2xl text-red-500"></i>
                            @break
                        @case('cancelled')
                            <i class="fas fa-ban text-2xl text-gray-600"></i>
                            @break
                        @default
                            @if(strpos($stat->lr_status, 'leave_') === 0)
                                <i class="fas fa-calendar-alt text-2xl text-blue-600"></i>
                            @else
                                <i class="fas fa-calendar text-2xl text-blue-600"></i>
                            @endif
                    @endswitch
                </div>
                <div class="ml-4">
                    <div class="text-xl font-semibold text-gray-900">{{ $stat->total }}</div>
                    <div class="text-sm text-gray-600">
                        @switch($stat->lr_status)
                            @case('approved')
                                Approved
                                @break
                            @case('pending')
                                Pending
                                @break
                            @case('rejected')
                                Rejected
                                @break
                            @case('cancelled')
                                Cancelled
                                @break
                            @default
                                @if(strpos($stat->lr_status, 'leave_') === 0 && isset($stat->lt_name))
                                    {{ $stat->lt_name }}
                                @else
                                    {{ ucfirst(str_replace('leave_', '', $stat->lr_status)) }}
                                @endif
                        @endswitch
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <!-- Default statistics if no stats provided -->
        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-red-50 border-red-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <div class="text-xl font-semibold text-gray-900">0</div>
                    <div class="text-sm text-gray-600">Approved</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-gray-50 border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-clock text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <div class="text-xl font-semibold text-gray-900">0</div>
                    <div class="text-sm text-gray-600">Pending</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-gray-50 border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-times-circle text-2xl text-red-500"></i>
                </div>
                <div class="ml-4">
                    <div class="text-xl font-semibold text-gray-900">0</div>
                    <div class="text-sm text-gray-600">Rejected</div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-gray-50 border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-ban text-2xl text-gray-600"></i>
                </div>
                <div class="ml-4">
                    <div class="text-xl font-semibold text-gray-900">0</div>
                    <div class="text-sm text-gray-600">Cancelled</div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Filters Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
    <form method="GET" action="{{ route('leave-requests_v2.summary-report') }}" id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Filter</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="date_filter" name="date_filter">
                    <option value="this_week" {{ $data['dateFilter'] == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="past_week" {{ $data['dateFilter'] == 'past_week' ? 'selected' : '' }}>Past Week</option>
                    <option value="next_week" {{ $data['dateFilter'] == 'next_week' ? 'selected' : '' }}>Next Week</option>
                    <option value="this_month" {{ $data['dateFilter'] == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $data['dateFilter'] == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="next_month" {{ $data['dateFilter'] == 'next_month' ? 'selected' : '' }}>Next Month</option>
                    <option value="custom" {{ $data['dateFilter'] == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div id="start_date_container" style="display: {{ $data['dateFilter'] == 'custom' ? 'block' : 'none' }};">
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="start_date" name="start_date" value="{{ $data['startDate'] }}">
            </div>
            <div id="end_date_container" style="display: {{ $data['dateFilter'] == 'custom' ? 'block' : 'none' }};">
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="end_date" name="end_date" value="{{ $data['endDate'] }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Division</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="division_id" name="division_id">
                    <option value="">All Divisions</option>
                    @foreach($data['divisions'] as $division)
                        <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                            {{ $division->ud_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                <i class="fas fa-filter mr-2"></i> Filter
            </button>
            <a href="{{ route('leave-requests_v2.summary-report') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-times mr-2"></i> Clear
            </a>
        </div>
    </form>
</div>

<!-- Report Content Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <!-- Search -->
    <div class="mb-6">
        <input type="search" id="summary_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari staff...">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="SummaryReportTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">NIP</th>
                    <th scope="col" class="px-3 py-3">Name</th>
                    <th scope="col" class="px-3 py-3">Position</th>
                    <th scope="col" class="px-3 py-3">Division</th>
                    <th scope="col" class="px-3 py-3">Total Requests</th>
                    <th scope="col" class="px-3 py-3">Total Days</th>
                    <th scope="col" class="px-3 py-3">Total Hours</th>
                    <th scope="col" class="px-3 py-3">Annual Leave Balance</th>
                    @foreach($data['leaveTypes'] as $leaveType)
                        <th scope="col" class="px-3 py-3">{{ $leaveType->lt_name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="summary_tbody">
                <tr>
                    <td colspan="{{ 9 + count($data['leaveTypes']) }}" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
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
    <script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
    @include('app._partials.js')
    @include('app.updated_leave_request.summary_report_js')
@endpush
@endsection
