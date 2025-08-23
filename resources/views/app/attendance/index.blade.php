@extends('app.structure')
@section('content')
<style>
    .table-responsive {
        overflow-x: auto;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 2px;
        width: 30px;
        height: 30px;
        padding: 5px;
        font-size: 12px;
    }
    
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
    
    .badge {
        font-size: 11px;
        padding: 4px 8px;
    }
    
    .dataTables_wrapper .dt-buttons {
        margin-bottom: 10px;
    }
    
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px;
    }
    
    .table th {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        font-weight: 600;
        font-size: 12px;
    }
    
    .table td {
        font-size: 12px;
        vertical-align: middle;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.02);
    }

    /* Custom CSS for Metronic dropdown menu */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .menu.menu-sub-dropdown {
        z-index: 9999 !important;
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        margin-top: 5px !important;
        min-width: 150px !important;
        background: white !important;
        border: 1px solid #e4e6ef !important;
        border-radius: 0.475rem !important;
        box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075) !important;
    }

    /* Ensure proper positioning for DataTables */
    .dataTables_wrapper .dataTables_processing {
        z-index: 9998;
    }

    /* Fix for menu positioning in table cells */
    #attendanceTable td {
        position: relative;
    }

    /* Menu item styling */
    .menu-item .menu-link {
        cursor: pointer;
        transition: all 0.3s ease;
        display: block;
        padding: 0.5rem 1rem;
        text-decoration: none;
        color: #3f4254 !important;
        font-weight: 500;
        font-size: 1rem;
    }

    .menu-item .menu-link:hover {
        background-color: #f3f6f9 !important;
        color: #3699FF !important;
    }

    .menu-item .menu-link.text-danger {
        color: #f64e60 !important;
    }

    .menu-item .menu-link.text-danger:hover {
        background-color: #ffe2e5 !important;
        color: #f64e60 !important;
    }

    /* Button styling for menu trigger */
    [data-kt-menu-trigger="click"] {
        cursor: pointer;
        user-select: none;
    }

    /* SVG icon styling */
    .svg-icon {
        display: inline-block;
        vertical-align: middle;
    }

    .svg-icon svg {
        width: 1em;
        height: 1em;
    }

    /* Fallback menu system styles */
    .menu.show {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    .menu:not(.show) {
        display: none !important;
    }

    .bg-all {
    background-color: #FFEBEB;
    }
    .bg-other {
        background-color: #F1F1F4;
    }
</style>
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Page Heading-->
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Log {{ $data['subtitle'] }}</h5>
                <!--end::Page Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item text-muted">
                        <span class="text-muted">
                            @if($data['dateFilter'] && $data['dateFilter'] !== 'custom')
                                {{ date('d M Y', strtotime($data['startDate'])) }} to {{ date('d M Y', strtotime($data['endDate'])) }}
                            @else
                                {{ $data['startDate'] }} to {{ $data['endDate'] }}
                            @endif
                        </span>
                    </li>
                </ul>
                <!--end::Breadcrumb-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">


            <!-- Statistics Cards -->
            <div class="row mb-4">
                @foreach($stats ?? [] as $stat)
                    <div class="col-lg-2 col-md-4">
                        <div class="card card-custom rounded-lg 
                            @switch($stat->at_status)
                                @case('present')
                                    bg-all
                                    @break
                                @case('late')
                                    bg-other
                                    @break
                                @case('absent')
                                    bg-other
                                    @break
                                @case('early_leave')
                                    bg-other
                                    @break
                                @case('scan_once')
                                    bg-other
                                    @break
                                @default
                                    @if(strpos($stat->at_status, 'leave_') === 0)
                                        bg-other
                                    @else
                                        bg-other
                                    @endif
                            @endswitch">
                            <div class="card-body px-7">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label">
                                            @switch($stat->at_status)
                                                @case('present')
                                                    <i class="ki-outline ki-check-circle text-dark"></i>
                                                    @break
                                                @case('late')
                                                    <i class="ki-outline ki-entrance-left text-dark"></i>
                                                    @break
                                                @case('absent')
                                                    <i class="ki-outline ki-cross-circle text-dark"></i>
                                                    @break
                                                @case('early_leave')
                                                    <i class="ki-outline ki-arrow-left text-dark"></i>
                                                    @break
                                                @case('scan_once')
                                                    <i class="ki-outline ki-calendar-tick text-dark"></i>
                                                    @break
                                                @default
                                                    @if(strpos($stat->at_status, 'leave_') === 0)
                                                        <i class="ki-outline ki-calendar text-dark"></i>
                                                    @else
                                                        <i class="ki-outline ki-calendar text-dark"></i>
                                                    @endif
                                            @endswitch
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5">{{ $stat->total }}</div>
                                        <div class="text-dark-50">
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
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Filters</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('attendance.index') }}">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="date_filter">Date Filter</label>
                                        <select class="form-control" id="date_filter" name="date_filter" onchange="handleDateFilterChange(this.value)">
                                            <option value="this_week" {{ request('date_filter', 'this_week') == 'this_week' ? 'selected' : '' }}>This Week</option>
                                            <option value="past_week" {{ request('date_filter', 'this_week') == 'past_week' ? 'selected' : '' }}>Past Week</option>
                                            <option value="this_month" {{ request('date_filter', 'this_week') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                            <option value="last_month" {{ request('date_filter', 'this_week') == 'last_month' ? 'selected' : '' }}>Past Month</option>
                                            <option value="custom" {{ request('date_filter', 'this_week') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" id="start_date_container" style="display: none;">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="{{ request('start_date', date('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-2" id="end_date_container" style="display: none;">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="{{ request('end_date', date('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="user_id">Staff</label>
                                        <select class="form-control" id="user_id" name="user_id">
                                            <option value="">All Staff</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->u_name }} ({{ $user->u_nip }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="division_id">Division</label>
                                        <select class="form-control" id="division_id" name="division_id">
                                            <option value="">All Divisions</option>
                                            @foreach($divisions as $division)
                                                <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                                    {{ $division->ud_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">All Status</option>
                                            <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                                            <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                                            <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                                            <option value="early_leave" {{ request('status') == 'early_leave' ? 'selected' : '' }}>Early Leave</option>
                                            <option value="scan_once" {{ request('status') == 'scan_once' ? 'selected' : '' }}>Scan Once</option>
                                            @foreach($leaveTypes as $leaveType)
                                                <option value="leave_{{ $leaveType->lt_code }}" {{ request('status') == 'leave_' . $leaveType->lt_code ? 'selected' : '' }}>
                                                    {{ $leaveType->lt_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="ki-outline ki-filter-tick"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3">
                            <div class="card-toolbar d-flex justify-content-between w-100">
                                <div class="d-flex align-items-center">
                                    <input type="search" class="form-control" style="width: 300px;" id="attendance_search" placeholder="Search"/>
                                </div>
                                <div class="d-flex align-items-center">
                                    <!--begin::Button-->
                                    <!-- <a href="{{ route('attendance.summary-report') }}" class="btn btn-info font-weight-bolder mr-2">
                                        <span class="svg-icon svg-icon-md">
                                            <i class="ki-outline ki-chart-line"></i>
                                        </span>Summary Report</a> -->
                                    <!--end::Button-->
                                   

                                    <!--begin::Button-->
                                    <button type="button" class="btn btn-light-green font-weight-bolder mr-2" onclick="exportToExcel()">
                                        <span class="svg-icon svg-icon-md">
                                            <i class="ki-outline ki-file-down"></i>
                                        </span>Export Excel</button>
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <button type="button" class="btn btn-secondary font-weight-bolder mr-2" onclick="exportToPDF()">
                                        <span class="svg-icon svg-icon-md">
                                            <i class="ki-outline ki-file-down"></i>
                                        </span>Export PDF</button>
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <form method="POST" action="{{ route('attendance.reprocess-all') }}" style="display: inline; margin-bottom: 0px;">
                                        @csrf
                                        <input type="hidden" name="start_date" value="{{ request('start_date', date('Y-m-d')) }}">
                                        <input type="hidden" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}">
                                        <button type="submit" class="btn btn-light-primary font-weight-bolder mr-2" onclick="return confirm('Yakin ingin memproses ulang semua data absensi?')">
                                            <span class="svg-icon svg-icon-md">
                                                <i class="ki-outline ki-update-folder"></i>
                                            </span>Reprocess All</button>
                                    </form>
                                    <!--end::Button-->
                                     <!--begin::Button-->
                                     <a href="{{ route('attendance.upload') }}" class="btn btn-green font-weight-bolder mr-2">
                                        <span class="svg-icon svg-icon-md">
                                            <i class="ki-outline ki-file-up"></i>
                                        </span>Upload Excel</a>
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <a href="{{ route('attendance.create') }}" class="btn btn-dark font-weight-bolder">
                                        <span class="svg-icon svg-icon-md">
                                            <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24" />
                                                    <circle fill="#000000" cx="9" cy="15" r="6" />
                                                    <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>Data Baru</a>
                                    <!--end::Button-->
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible" id="successAlert">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible" id="errorAlert">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('error') }}
                                </div>
                            @endif

                            <!--begin: Datatable-->
                            <table class="table table-hover table-checkable" id="attendanceTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                        <th>Staff</th>
                                    <!-- <th>NIP</th> -->
                                    <th>Divisi</th>
                                    <th>Shift</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                            <!--end: Datatable-->
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Check for session messages and ensure they're visible
    if ($('#successAlert').length > 0) {
        console.log('Success message found:', $('#successAlert').text());
        // Ensure success message is visible and doesn't get hidden
        $('#successAlert').show();
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('#successAlert').fadeOut();
        }, 5000);
    }
    
    if ($('#errorAlert').length > 0) {
        console.log('Error message found:', $('#errorAlert').text());
        // Ensure error message is visible and doesn't get hidden
        $('#errorAlert').show();
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('#errorAlert').fadeOut();
        }, 5000);
    }
});
</script>
@endsection

<script>
// Date filter functionality
function handleDateFilterChange(value) {
    const startDateContainer = document.getElementById('start_date_container');
    const endDateContainer = document.getElementById('end_date_container');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (value === 'custom') {
        startDateContainer.style.display = 'block';
        endDateContainer.style.display = 'block';
    } else {
        startDateContainer.style.display = 'none';
        endDateContainer.style.display = 'none';
        
        // Set default dates based on filter
        const today = new Date();
        let startDate, endDate;
        
        switch (value) {
            case 'this_week':
                startDate = new Date(today.getTime());
                startDate.setDate(today.getDate() - today.getDay() + 1); // Monday
                endDate = new Date(today.getTime());
                endDate.setDate(today.getDate() - today.getDay() + 7); // Sunday
                break;
            case 'past_week':
                startDate = new Date(today.getTime());
                startDate.setDate(today.getDate() - today.getDay() - 6); // Last Monday
                endDate = new Date(today.getTime());
                endDate.setDate(today.getDate() - today.getDay()); // Last Sunday
                break;
            case 'this_month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1); // First day of month
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of month
                break;
            case 'last_month':
                startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1); // First day of last month
                endDate = new Date(today.getFullYear(), today.getMonth(), 0); // Last day of last month
                break;
        }
        
        // Format dates for hidden inputs
        if (startDate && endDate) {
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            startDateInput.value = formatDate(startDate);
            endDateInput.value = formatDate(endDate);
        }
    }
}

// Initialize date filter on page load
document.addEventListener('DOMContentLoaded', function() {
    const dateFilter = document.getElementById('date_filter');
    if (dateFilter) {
        handleDateFilterChange(dateFilter.value);
    }
});

// Export functions
function exportToExcel() {
    const url = new URL('{{ route("attendance.export-excel") }}');
    
    // Add current filters to URL
    const dateFilter = document.getElementById('date_filter').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const userId = document.getElementById('user_id').value;
    const divisionId = document.getElementById('division_id').value;
    const status = document.getElementById('status').value;
    
    if (dateFilter && dateFilter !== 'custom') {
        url.searchParams.append('date_filter', dateFilter);
    }
    if (startDate) {
        url.searchParams.append('start_date', startDate);
    }
    if (endDate) {
        url.searchParams.append('end_date', endDate);
    }
    if (userId) {
        url.searchParams.append('user_id', userId);
    }
    if (divisionId) {
        url.searchParams.append('division_id', divisionId);
    }
    if (status) {
        url.searchParams.append('status', status);
    }
    
    // Create download link
    const link = document.createElement('a');
    link.href = url.toString();
    link.download = 'attendance_export.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportToPDF() {
    const url = new URL('{{ route("attendance.export-pdf") }}');
    
    // Add current filters to URL
    const dateFilter = document.getElementById('date_filter').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    const userId = document.getElementById('user_id').value;
    const divisionId = document.getElementById('division_id').value;
    const status = document.getElementById('status').value;
    
    if (dateFilter && dateFilter !== 'custom') {
        url.searchParams.append('date_filter', dateFilter);
    }
    if (startDate) {
        url.searchParams.append('start_date', startDate);
    }
    if (endDate) {
        url.searchParams.append('end_date', endDate);
    }
    if (userId) {
        url.searchParams.append('user_id', userId);
    }
    if (divisionId) {
        url.searchParams.append('division_id', divisionId);
    }
    if (status) {
        url.searchParams.append('status', status);
    }
    
    // Create download link
    const link = document.createElement('a');
    link.href = url.toString();
    link.download = 'attendance_export.pdf';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

@include('app._partials.js')
@include('app.attendance.attendance_js') 