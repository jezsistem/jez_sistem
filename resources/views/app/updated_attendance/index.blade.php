@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Log {{ $data['subtitle'] }}</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.summary-report_v2') }}" class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200">
                <i class="fas fa-chart-bar mr-2"></i>Summary Report
            </a>
            <a href="{{ route('attendance.upload_v2') }}" class="px-4 py-2 text-sm font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200">
                <i class="fas fa-upload mr-2"></i>Upload Excel
            </a>
            <a href="{{ route('attendance.create_v2') }}" class="px-4 py-2 text-sm font-medium text-white bg-gray-800 rounded-lg hover:bg-gray-900">
                <i class="fas fa-plus mr-2"></i>Data Baru
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6" id="stats-container">
    @foreach($stats ?? [] as $stat)
        <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm 
            @switch($stat->at_status)
                @case('present')
                    bg-red-50 border-red-200
                    @break
                @case('late')
                    bg-yellow-50 border-yellow-200
                    @break
                @case('absent')
                    bg-gray-50 border-gray-200
                    @break
                @case('early_leave')
                    bg-purple-50 border-purple-200
                    @break
                @case('scan_once')
                    bg-green-50 border-green-200
                    @break
                @default
                    @if(strpos($stat->at_status, 'leave_') === 0)
                        bg-blue-50 border-blue-200
                    @else
                        bg-gray-50 border-gray-200
                    @endif
            @endswitch">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    @switch($stat->at_status)
                        @case('present')
                            <i class="fas fa-check-circle text-2xl text-red-500"></i>
                            @break
                        @case('late')
                            <i class="fas fa-clock text-2xl text-yellow-600"></i>
                            @break
                        @case('absent')
                            <i class="fas fa-times-circle text-2xl text-gray-600"></i>
                            @break
                        @case('early_leave')
                            <i class="fas fa-arrow-left text-2xl text-purple-600"></i>
                            @break
                        @case('scan_once')
                            <i class="fas fa-calendar-check text-2xl text-green-600"></i>
                            @break
                        @default
                            <i class="fas fa-calendar text-2xl text-blue-600"></i>
                    @endswitch
                </div>
                <div class="ml-4">
                    <div class="text-xl font-semibold text-gray-900">{{ $stat->total }}</div>
                    <div class="text-sm text-gray-600">
                        @switch($stat->at_status)
                            @case('present')
                                Hadir
                                @break
                            @case('late')
                                Terlambat
                                @break
                            @case('absent')
                                Tidak Hadir
                                @break
                            @case('early_leave')
                                Pulang Awal
                                @break
                            @case('scan_once')
                                Scan 1 Kali
                                @break
                            @default
                                @if(strpos($stat->at_status, 'leave_') === 0)
                                    @php
                                        $leaveType = str_replace('leave_', '', $stat->at_status);
                                        $leaveNames = [
                                            'ANNUAL' => 'Cuti Tahunan',
                                            'SICK' => 'Cuti Sakit',
                                            'MATERNITY' => 'Cuti Melahirkan',
                                            'EMERGENCY' => 'Cuti Darurat',
                                            'HALF_DAY' => 'Setengah Hari',
                                            'SPECIAL' => 'Cuti Khusus'
                                        ];
                                    @endphp
                                    {{ $leaveNames[$leaveType] ?? 'Cuti ' . $leaveType }}
                                @else
                                    {{ ucfirst($stat->at_status) }}
                                @endif
                        @endswitch
                    </div>
                </div>
            </div>
        </div>
    @endforeach
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
                <option value="">All Staff</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->u_name }} ({{ $user->u_nip }})</option>
                @endforeach
            </select>
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
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">All Status</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="late">Late</option>
                <option value="early_leave">Early Leave</option>
                <option value="scan_once">Scan Once</option>
                @foreach($leaveTypes as $leaveType)
                    <option value="leave_{{ $leaveType->lt_code }}">{{ $leaveType->lt_name }}</option>
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
    <!-- Search and Actions -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <input type="search" id="attendance_search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-1/3 p-2.5" placeholder="Cari attendance...">
        <div class="flex items-center gap-3">
            <button type="button" onclick="exportToExcel()" class="px-4 py-2 text-sm font-medium text-green-700 bg-green-100 rounded-lg hover:bg-green-200">
                <i class="fas fa-file-excel mr-2"></i>Export Excel
            </button>
            <button type="button" onclick="exportToPDF()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </button>
            <form method="POST" action="{{ route('attendance.reprocess-all') }}" class="inline">
                @csrf
                <input type="hidden" name="start_date" id="reprocess_start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" id="reprocess_end_date" value="{{ $endDate }}">
                <button type="submit" onclick="return confirm('Yakin ingin memproses ulang semua data absensi?')" class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200">
                    <i class="fas fa-sync mr-2"></i>Reprocess All
                </button>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="AttendanceTable" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">No</th>
                    <th scope="col" class="px-3 py-3">Tanggal</th>
                    <th scope="col" class="px-3 py-3">Staff</th>
                    <th scope="col" class="px-3 py-3">Divisi</th>
                    <th scope="col" class="px-3 py-3">Shift</th>
                    <th scope="col" class="px-3 py-3">Time In</th>
                    <th scope="col" class="px-3 py-3">Time Out</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                    <th scope="col" class="px-3 py-3">Actions</th>
                </tr>
            </thead>
            <tbody id="attendance_tbody">
                <tr>
                    <td colspan="9" class="px-3 py-4 text-center text-gray-500">Memuat data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
        <div id="attendance_pagination_info" class="text-sm text-gray-700"></div>
        <div id="attendance_pagination_controls" class="flex gap-2"></div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('app._partials.js')
    @include('app.updated_attendance.attendance_js')
@endpush
@endsection
