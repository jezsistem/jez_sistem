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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">{{ $data['subtitle'] }}</h5>
                <!--end::Page Title-->
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
            <!-- Date Range Info -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <strong>Date Range:</strong> 
                        @if(request('date_filter') && request('date_filter') !== 'custom')
                            {{ ucfirst(str_replace('_', ' ', request('date_filter'))) }} 
                            ({{ request('start_date') }} to {{ request('end_date') }})
                        @else
                            {{ request('start_date') }} to {{ request('end_date') }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                @foreach($stats ?? [] as $stat)
                    <div class="col-lg-2 col-md-4">
                        <div class="card card-custom rounded-lg 
                            @switch($stat->at_status)
                                @case('present')
                                    bg-success
                                    @break
                                @case('late')
                                    bg-warning
                                    @break
                                @case('absent')
                                    bg-danger
                                    @break
                                @case('early_leave')
                                    bg-info
                                    @break
                                @case('scan_once')
                                    bg-secondary
                                    @break
                                @default
                                    @if(strpos($stat->at_status, 'leave_') === 0)
                                        bg-primary
                                    @else
                                        bg-dark
                                    @endif
                            @endswitch">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label">
                                            @switch($stat->at_status)
                                                @case('present')
                                                    <i class="ki-outline ki-check text-white"></i>
                                                    @break
                                                @case('late')
                                                    <i class="ki-outline ki-clock text-white"></i>
                                                    @break
                                                @case('absent')
                                                    <i class="ki-outline ki-cross text-white"></i>
                                                    @break
                                                @case('early_leave')
                                                    <i class="ki-outline ki-arrow-left text-white"></i>
                                                    @break
                                                @case('scan_once')
                                                    <i class="ki-outline ki-calendar-tick text-white"></i>
                                                    @break
                                                @default
                                                    @if(strpos($stat->at_status, 'leave_') === 0)
                                                        <i class="ki-outline ki-calendar text-white"></i>
                                                    @else
                                                        <i class="ki-outline ki-calendar text-white"></i>
                                                    @endif
                                            @endswitch
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-white font-weight-bold font-size-h6">{{ $stat->total }}</div>
                                        <div class="text-white-50">
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
                                            <option value="last_month" {{ request('date_filter', 'this_week') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                            <option value="custom" {{ request('date_filter', 'this_week') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" id="start_date_container" style="display: none;">
                                        <label for="start_date">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="{{ request('start_date', date('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-2" id="end_date_container" style="display: none;">
                                        <label for="end_date">Tanggal Akhir</label>
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
                                            <i class="ki-outline ki-filter-tick"></i> Apply Filters
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
                                    <a href="{{ route('attendance.create') }}" class="btn btn-dark font-weight-bolder mr-2">
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
                                    <!--begin::Button-->
                                    <a href="{{ route('attendance.upload') }}" class="btn btn-info font-weight-bolder mr-2">
                                        <span class="svg-icon svg-icon-md">
                                            <i class="ki-outline ki-file-up"></i>
                                        </span>Upload Excel</a>
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <!-- <a href="{{ route('attendance.export') }}" class="btn btn-success font-weight-bolder mr-2">
                                        <span class="svg-icon svg-icon-md">
                                            <i class="ki-outline ki-file-down"></i>
                                        </span>Export</a> -->
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <form method="POST" action="{{ route('attendance.reprocess-all') }}" style="display: inline; margin-bottom: 0px;">
                                        @csrf
                                        <input type="hidden" name="start_date" value="{{ request('start_date', date('Y-m-d')) }}">
                                        <input type="hidden" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}">
                                        <button type="submit" class="btn btn-warning font-weight-bolder" onclick="return confirm('Yakin ingin memproses ulang semua data absensi?')">
                                            <span class="svg-icon svg-icon-md">
                                                <i class="ki-outline ki-update-folder"></i>
                                            </span>Reprocess All</button>
                                    </form>
                                    <!--end::Button-->
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible">
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
                                    <th>NIP</th>
                                    <th>Divisi</th>
                                    <th>Shift</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
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
                startDate = new Date(today.setDate(today.getDate() - today.getDay() + 1)); // Monday
                endDate = new Date(today.setDate(today.getDate() - today.getDay() + 7)); // Sunday
                break;
            case 'past_week':
                startDate = new Date(today.setDate(today.getDate() - today.getDay() - 6)); // Last Monday
                endDate = new Date(today.setDate(today.getDate() - today.getDay())); // Last Sunday
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
</script>

@include('app._partials.js')
@include('app.attendance.attendance_js') 