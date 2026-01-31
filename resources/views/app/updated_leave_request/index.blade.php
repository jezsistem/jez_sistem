@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                @if($dateFilter && $dateFilter !== 'custom')
                    {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}
                @else
                    {{ $startDate }} to {{ $endDate }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('leave-requests_v2.summary-report') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                <i class="fas fa-chart-bar mr-2"></i>Summary Report
            </a>
            <div class="relative">
                <button type="button" id="export_leave_request_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                    <i class="fas fa-download mr-2"></i>Export
                </button>
                <div id="export_leave_request_menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                    <div class="py-1">
                        <button type="button" id="export_leave_request_excel_btn" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-excel mr-2"></i>Excel
                        </button>
                    </div>
                </div>
            </div>
            @php
                $canCreate = function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'create');
            @endphp
            @if($canCreate)
            <button id="add_leave_request_btn" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-plus mr-2"></i>Data Baru
            </button>
            @endif
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar-check text-2xl text-red-500"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="total-requests">0</div>
                <div class="text-sm text-gray-600">Total Requests</div>
            </div>
        </div>
    </div>
    <div class="bg-yellow-50 rounded-lg p-4 border border-yellow-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-clock text-2xl text-yellow-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="pending-requests">0</div>
                <div class="text-sm text-gray-600">Pending</div>
            </div>
        </div>
    </div>
    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="approved-requests">0</div>
                <div class="text-sm text-gray-600">Approved</div>
            </div>
        </div>
    </div>
    <div class="bg-red-50 rounded-lg p-4 border border-red-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-times-circle text-2xl text-red-500"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="rejected-requests">0</div>
                <div class="text-sm text-gray-600">Rejected</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
    <form method="GET" action="{{ route('leave-requests_v2') }}" id="filterForm">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Filter</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="date_filter" name="date_filter">
                    <option value="this_week" {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="past_week" {{ $dateFilter == 'past_week' ? 'selected' : '' }}>Past Week</option>
                    <option value="next_week" {{ $dateFilter == 'next_week' ? 'selected' : '' }}>Next Week</option>
                    <option value="this_month" {{ $dateFilter == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ $dateFilter == 'last_month' ? 'selected' : '' }}>Last Month</option>
                    <option value="next_month" {{ $dateFilter == 'next_month' ? 'selected' : '' }}>Next Month</option>
                    <option value="custom" {{ $dateFilter == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div id="start_date_container" style="display: {{ $dateFilter == 'custom' ? 'block' : 'none' }};">
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="start_date" name="start_date" value="{{ $startDate }}">
            </div>
            <div id="end_date_container" style="display: {{ $dateFilter == 'custom' ? 'block' : 'none' }};">
                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="end_date" name="end_date" value="{{ $endDate }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Staff</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="user_id" name="user_id">
                    <option value="">All Staffs</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                            {{ $user->u_name }} ({{ $user->u_nip }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="leave_type_id" name="leave_type_id">
                    <option value="">All Types</option>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}" {{ $leaveTypeId == $type->id ? 'selected' : '' }}>
                            {{ $type->lt_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="status" name="status">
                    <option value="" {{ $status === '' || $status === null ? 'selected' : '' }}>All Status</option>
                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <!-- Search -->
    <div class="mb-6">
        <input type="search" id="leave_request_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari leave request...">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="LeaveRequestTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Request Date</th>
                    <th scope="col" class="px-3 py-3">Staff</th>
                    <th scope="col" class="px-3 py-3">Division</th>
                    <th scope="col" class="px-3 py-3">Leave Type</th>
                    <th scope="col" class="px-3 py-3">Start Date</th>
                    <th scope="col" class="px-3 py-3">End Date</th>
                    <th scope="col" class="px-3 py-3">Attachment</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                    <th scope="col" class="px-3 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="leave_request_tbody">
                <tr>
                    <td colspan="10" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <div id="leave_request_pagination_info" class="text-sm text-gray-700"></div>
        <div id="leave_request_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@include('app.updated_leave_request.leave_request_modal')
@include('app.updated_leave_request.approval_modal')
@include('app.updated_leave_request.attachment_modal')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('cdn') }}/jquery.table2excel.js?v2"></script>
    @include('app._partials.js')
    @include('app.updated_leave_request.leave_request_js')
@endpush
@endsection
