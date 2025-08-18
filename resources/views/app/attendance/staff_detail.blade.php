@extends('app.structure')
@section('content')
<style>
    .table-responsive {
        overflow-x: auto;
    }
    
    .badge {
        font-size: 11px;
        padding: 4px 8px;
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
    
    .staff-info {
        background: #FFEBEB;
        color: ##071437;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .staff-info h4 {
        margin: 0;
        font-weight: 600;
    }
    
    .staff-info p {
        margin: 5px 0;
        opacity: 0.9;
    }

    /* Dropdown menu styling */
    .dropdown { position: relative; display: inline-block; }
    .menu.menu-sub-dropdown { z-index: 9999 !important; position: absolute !important; top: 100% !important; left: 0 !important; margin-top: 5px !important; min-width: 150px !important; background: white !important; border: 1px solid #e4e6ef !important; border-radius: 0.475rem !important; box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075) !important; }
    #staffAttendanceTable td { position: relative; }
    .menu-item .menu-link { cursor: pointer; transition: all 0.3s ease; display: block; padding: 0.5rem 1rem; text-decoration: none; color: #3f4254 !important; font-weight: 500; font-size: 1rem; }
    .menu-item .menu-link:hover { background-color: #f3f6f9 !important; color: #3699FF !important; }
    [data-kt-menu-trigger="click"] { cursor: pointer; user-select: none; }
    .svg-icon { display: inline-block; vertical-align: middle; }
    .svg-icon svg { width: 1em; height: 1em; }
    .menu.show { display: block !important; opacity: 1 !important; visibility: visible !important; }
    .menu:not(.show) { display: none !important; }
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
            
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <a href="{{ route('attendance.index') }}" class="btn btn-secondary font-weight-bolder">
                    <i class="ki-outline ki-arrow-left"></i> Back
                </a>
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
    <!--end::Subheader-->
    
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!-- Staff Information Card -->
            <div class="staff-info">
                <div class="row">
                    <div class="col-md-8">
                        <h4>{{ $staff->u_name }}</h4>
                        <p><strong>NIP:</strong> {{ $staff->u_nip }}</p>
                        <p><strong>Divisi:</strong> {{ $staff->ud_name ?? 'Tidak ada divisi' }}</p>
                        <p><strong>Email:</strong> {{ $staff->u_email ?? 'Tidak ada email' }}</p>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="d-flex flex-column align-items-end">
                            <span class="badge badge-light badge-pill mb-2">Staff ID: {{ $staff->id }}</span>
                            <span class="badge badge-primary badge-pill">{{ $staff->u_status ?? 'Active' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                Filter
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="filterForm">
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
                                        <label for="start_date">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="{{ request('start_date', date('Y-m-d', strtotime('-30 days'))) }}">
                                    </div>
                                    <div class="col-md-2" id="end_date_container" style="display: none;">
                                        <label for="end_date">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="{{ request('end_date', date('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">Semua Status</option>
                                            <option value="present">Present</option>
                                            <option value="absent">Absent</option>
                                            <option value="late">Late</option>
                                            <option value="early_leave">Early Leave</option>
                                            <option value="scan_once">Scan Once</option>
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

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom bg-success text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-0" id="present_count">0</h4>
                            <small>Present</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom bg-warning text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-0" id="late_count">0</h4>
                            <small>Late</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom bg-danger text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-0" id="absent_count">0</h4>
                            <small>Absent</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom" style="background-color: #3699FF; color: white;">
                        <div class="card-body text-center">
                            <h4 class="mb-0" id="leave_annual_count">0</h4>
                            <small>Annual Leave</small>
                        </div>
                    </div>
                </div>

                <!-- <div class="col-lg-2 col-md-4">
                    <div class="card card-custom bg-info text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-0" id="early_leave_count">0</h4>
                            <small>Early Leave</small>
                        </div>
                    </div>
                </div> -->
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom bg-primary text-white">
                        <div class="card-body text-center">
                            <h4 class="mb-0" id="total_days">0</h4>
                            <small>Total Hari</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Statistics -->
            <!-- <div class="row mb-4">
                <div class="col-12">
                    <h6 class="text-dark font-weight-bold mb-3">📋 Leave Statistics</h6>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom" style="background-color: #3699FF; color: white;">
                        <div class="card-body text-center">
                            <h4 class="mb-0" id="leave_annual_count">0</h4>
                            <small>Annual Leave</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom" style="background-color: #F64E60; color: white;">
                        <div class="card-body text-center">
                            <h4 class="mb-0" id="leave_sick_count">0</h4>
                            <small>Sick Leave</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom" style="background-color: #FFA800; color: white;">
                        <div class="card-body text-center">
                            <h4 class="mb-0" id="leave_maternity_count">0</h4>
                            <small>Maternity</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom" style="background-color: #8950FC; color: white;">
                        <div class="card-body text-center">
                            <h4 class="mb-0" id="leave_emergency_count">0</h4>
                            <small>Emergency</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom" style="background-color: #1BC5BD; color: white;">
                        <div class="card-body text-center">
                            <h4 class="mb-0" id="leave_half_day_count">0</h4>
                            <small>Half Day</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom" style="background-color: #E4E6EF; color: #3F4254;">
                        <div class="card-body text-center">
                            <h4 class="mb-0" id="leave_special_count">0</h4>
                            <small>Special</small>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Attendance Table -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">
                                    <i class="ki-outline ki-calendar"></i> Data Absensi
                                </h6>
                                <div class="d-flex">
                                    <button type="button" class="btn btn-success btn-sm mr-2" onclick="exportStaffToExcel()">
                                        <i class="ki-outline ki-file-down"></i> Export Excel
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="exportStaffToPDF()">
                                        <i class="ki-outline ki-file-down"></i> Export PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-hover table-striped" id="staffAttendanceTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@endsection

@include('app._partials.js')
@include('app.attendance.staff_detail_js')
