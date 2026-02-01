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
            <a href="{{ route('leave-requests_v2.summary-report') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700" onclick="exportToExcel()">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </button>
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700" onclick="exportToPDF()">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
        </div>
    </div>
</div>

<!-- Staff Information Card -->
<div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">{{ $data['staffUser']->u_name }}</h3>
            <div class="space-y-2">
                <p class="text-sm"><strong>NIP:</strong> {{ $data['staffUser']->u_nip }}</p>
                <p class="text-sm"><strong>Position:</strong> {{ $data['staffUser']->position_name ?? '-' }}</p>
                <p class="text-sm"><strong>Division:</strong> {{ $data['staffUser']->division_name ?? '-' }}</p>
                <p class="text-sm"><strong>Work Type:</strong> {{ $data['staffUser']->work_type ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="approvedCount">0</div>
                <div class="text-sm text-gray-600">Approved</div>
            </div>
        </div>
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-clock text-2xl text-yellow-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="pendingCount">0</div>
                <div class="text-sm text-gray-600">Pending</div>
            </div>
        </div>
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-times-circle text-2xl text-red-500"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="rejectedCount">0</div>
                <div class="text-sm text-gray-600">Rejected</div>
            </div>
        </div>
    </div>
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar text-2xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="totalDays">0</div>
                <div class="text-sm text-gray-600">Total Days</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
    <form method="GET" action="{{ route('leave-requests_v2.staff-detail', $data['staffUser']->id) }}" id="filterForm">
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="{{ route('leave-requests_v2.staff-detail', $data['staffUser']->id) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-times mr-2"></i>Clear
            </a>
        </div>
    </form>
</div>

<!-- Report Content Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="StaffTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Start Date</th>
                    <th scope="col" class="px-3 py-3">End Date</th>
                    <th scope="col" class="px-3 py-3">Duration</th>
                    <th scope="col" class="px-3 py-3">Leave Type</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                    <th scope="col" class="px-3 py-3">Reason</th>
                    <th scope="col" class="px-3 py-3">Request Date</th>
                </tr>
            </thead>
            <tbody id="staff_tbody">
                <tr>
                    <td colspan="8" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    <script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
    @include('app.updated_leave_request.staff_detail_js')
@endpush
@endsection
