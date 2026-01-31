@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ url('overtime/create') }}" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-plus mr-2"></i>New Request
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-gray-50">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-list text-2xl text-gray-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900">{{ $summary['total'] ?? 0 }}</div>
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
                <div class="text-xl font-semibold text-gray-900">{{ $summary['pending'] ?? 0 }}</div>
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
                <div class="text-xl font-semibold text-gray-900">{{ $summary['approved'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Approved</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-blue-50 border-blue-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-user-check text-2xl text-blue-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900">{{ $summary['hr_check'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">HR Check</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-purple-50 border-purple-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-double text-2xl text-purple-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900">{{ $summary['done'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Done</div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
    <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
                <option value="HR Check">HR Check</option>
                <option value="Done">Done</option>
            </select>
        </div>
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input type="date" id="start_date" name="start_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input type="date" id="end_date" name="end_date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-search mr-2"></i>Apply
            </button>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <!-- Search -->
    <div class="mb-6">
        <input type="search" id="overtime_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari overtime request...">
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="OvertimeTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Submission Date</th>
                    <th scope="col" class="px-3 py-3">Department</th>
                    <th scope="col" class="px-3 py-3">Assigned Staff</th>
                    <th scope="col" class="px-3 py-3">Start</th>
                    <th scope="col" class="px-3 py-3">End</th>
                    <th scope="col" class="px-3 py-3">Duration</th>
                    <th scope="col" class="px-3 py-3">Claim Type</th>
                    <th scope="col" class="px-3 py-3">Requested By</th>
                    <th scope="col" class="px-3 py-3">Approver</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                    <th scope="col" class="px-3 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="overtime_tbody">
                <tr>
                    <td colspan="12" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <div id="overtime_pagination_info" class="text-sm text-gray-700"></div>
        <div id="overtime_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_overtime.overtime_js')
@endpush
@endsection
