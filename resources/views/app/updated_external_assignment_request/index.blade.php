@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('external-assignment.summary-report') }}" class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200">
                <i class="fas fa-chart-bar mr-2"></i>Summary Report
            </a>
            <a href="{{ route('external-assignment.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-plus mr-2"></i>Data Baru
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-red-50 border-red-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar-check text-2xl text-red-500"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="total-requests">{{ $summary['total'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Total Requests</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-yellow-50 border-yellow-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-hourglass-half text-2xl text-yellow-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="pending-requests">{{ $summary['pending'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Pending</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-green-50 border-green-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="approved-requests">{{ $summary['approved'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Approved</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-red-50 border-red-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-times-circle text-2xl text-red-500"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="rejected-requests">{{ $summary['rejected'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Rejected</div>
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
                <option value="next_week" {{ $dateFilter == 'next_week' ? 'selected' : '' }}>Next Week</option>
                <option value="this_month" {{ $dateFilter == 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $dateFilter == 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="next_month" {{ $dateFilter == 'next_month' ? 'selected' : '' }}>Next Month</option>
                <option value="custom" {{ $dateFilter == 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>
        </div>
        <div id="start_date_container" style="display: {{ $dateFilter == 'custom' ? 'block' : 'none' }};">
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div id="end_date_container" style="display: {{ $dateFilter == 'custom' ? 'block' : 'none' }};">
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div>
            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Staff</label>
            <select id="user_id" name="user_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">All Staffs</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                        {{ $user->u_name }} ({{ $user->u_nip }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="ea_id" class="block text-sm font-medium text-gray-700 mb-1">Assignment Type</label>
            <select id="ea_id" name="ea_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">All Types</option>
                @foreach($assignmentTypes as $type)
                    <option value="{{ $type->id }}" {{ $eaId == $type->id ? 'selected' : '' }}>
                        {{ $type->ea_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="" {{ empty($status) ? 'selected' : '' }}>All Status</option>
                <option value="Pending Approval" {{ $status == 'Pending Approval' ? 'selected' : '' }}>Pending Approval</option>
                <option value="Approved" {{ $status == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Rejected" {{ $status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="HR Check" {{ $status == 'HR Check' ? 'selected' : '' }}>HR Check</option>
                <option value="Finance Process" {{ $status == 'Finance Process' ? 'selected' : '' }}>Finance Process</option>
                <option value="DONE" {{ $status == 'DONE' ? 'selected' : '' }}>Done</option>
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
        <input type="search" id="external_assignment_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari external assignment request...">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="ExternalAssignmentTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Request Date</th>
                    <th scope="col" class="px-3 py-3">Staff</th>
                    <th scope="col" class="px-3 py-3">Division</th>
                    <th scope="col" class="px-3 py-3">Assignment Type</th>
                    <th scope="col" class="px-3 py-3">Assignment Area</th>
                    <th scope="col" class="px-3 py-3">Start Date</th>
                    <th scope="col" class="px-3 py-3">End Date</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                    <th scope="col" class="px-3 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="external_assignment_tbody">
                <tr>
                    <td colspan="10" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <div id="external_assignment_pagination_info" class="text-sm text-gray-700"></div>
        <div id="external_assignment_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_external_assignment_request.external_assignment_request_js')
@endpush
@endsection
