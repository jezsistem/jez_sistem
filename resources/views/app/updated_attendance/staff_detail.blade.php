@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">{{ $data['subtitle'] }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>
</div>

<!-- Staff Information Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6 bg-red-50 border-red-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div class="flex-1">
            <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $staff->u_name }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                <div><strong>NIP:</strong> {{ $staff->u_nip }}</div>
                <div><strong>Divisi:</strong> {{ $staff->ud_name ?? 'Tidak ada divisi' }}</div>
                <div><strong>Email:</strong> {{ $staff->u_email ?? 'Tidak ada email' }}</div>
                <div>
                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">{{ $staff->u_status ?? 'Active' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4 mb-6" id="stats-container">
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-yellow-50 border-yellow-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-user-square text-2xl text-yellow-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="total_shifts">0</div>
                <div class="text-sm text-gray-600">Total Shifts</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-green-50 border-green-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar text-2xl text-green-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="present_days">0</div>
                <div class="text-sm text-gray-600">Present Days</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-yellow-50 border-yellow-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar-check text-2xl text-yellow-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="total_libur">0</div>
                <div class="text-sm text-gray-600">Total Day off</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-gray-50 border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar text-2xl text-gray-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="sick_days">0</div>
                <div class="text-sm text-gray-600">Sick Days</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-gray-50 border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar text-2xl text-gray-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="leave_days">0</div>
                <div class="text-sm text-gray-600">Leave Days</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-gray-50 border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar text-2xl text-gray-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="late_days">0</div>
                <div class="text-sm text-gray-600">Late Days</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-gray-50 border-gray-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar text-2xl text-gray-600"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="scan_once_days">0</div>
                <div class="text-sm text-gray-600">Scan Once</div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm bg-red-50 border-red-200">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-calendar text-2xl text-red-500"></i>
            </div>
            <div class="ml-4">
                <div class="text-xl font-semibold text-gray-900" id="alpha_days">0</div>
                <div class="text-sm text-gray-600">Alpha Days</div>
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
                <option value="this_week" selected>This Week</option>
                <option value="past_week">Past Week</option>
                <option value="this_month">This Month</option>
                <option value="last_month">Past Month</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>
        <div id="start_date_container" style="display: none;">
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
            <input type="date" id="start_date" name="start_date" value="{{ date('Y-m-d', strtotime('-30 days')) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div id="end_date_container" style="display: none;">
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
            <input type="date" id="end_date" name="end_date" value="{{ date('Y-m-d') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">Semua Status</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="late">Late</option>
                <option value="early_leave">Early Leave</option>
                <option value="scan_once">Scan Once</option>
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </div>
    </form>
</div>

<!-- Alpha Details Section (Accordion) -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <div class="flex items-center justify-between cursor-pointer" onclick="toggleAlphaAccordion()">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <i class="fas fa-calendar-times mr-2 text-red-500"></i>Alpha Details
        </h3>
        <i class="fas fa-chevron-down" id="alphaAccordionIcon"></i>
    </div>
    <div class="hidden mt-4" id="alphaAccordionBody">
        <p class="text-sm text-gray-600 mb-4">
            <strong>Alpha Logics:</strong> Date with shift (except day off), no absent, no leave, and no sick.
        </p>
        <div id="alpha_dates_container" class="text-center text-gray-500">
            <i class="fas fa-calendar-check text-4xl mb-2"></i>
            <p>Alpha dates will be displayed here</p>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <!-- Search and Actions -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <input type="search" id="staff_attendance_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari attendance...">
        <div class="flex items-center gap-3">
            <button type="button" onclick="exportStaffToExcel()" class="px-4 py-2 text-sm font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </button>
            <button type="button" onclick="exportStaffToPDF()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="StaffAttendanceTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Date</th>
                    <th scope="col" class="px-3 py-3">Shift</th>
                    <th scope="col" class="px-3 py-3">Jam Masuk</th>
                    <th scope="col" class="px-3 py-3">Jam Keluar</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                    <th scope="col" class="px-3 py-3">Notes</th>
                    <th scope="col" class="px-3 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="staff_attendance_tbody">
                <tr>
                    <td colspan="8" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <div id="staff_attendance_pagination_info" class="text-sm text-gray-700"></div>
        <div id="staff_attendance_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_attendance.staff_detail_js')
@endpush
@endsection
