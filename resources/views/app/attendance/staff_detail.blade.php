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
    .bg-attendance-total {
        background-color: #E8F5E8;
    }
    .bg-attendance-shifts {
        background-color: #FFF3CD;
    }
    .bg-attendance-late {
        background-color: #F8D7DA;
    }
    .bg-attendance-alpha {
        background-color: #F1F1F4;
    }
    .bg-all {
    background-color: #FFEBEB;
    }
    .bg-other {
        background-color: #F1F1F4;
    }
    
    /* Accordion styles */
    .collapse {
        display: none;
    }
    
    .collapse.show {
        display: block;
    }
    
    .card-header[style*="cursor: pointer"]:hover {
        background-color: #f8f9fa;
    }
    
    #alphaAccordionIcon {
        transition: all 0.3s ease;
    }
    
    #alphaAccordionIcon.ki-arrow-up {
        transform: rotate(180deg);
    }
    
    #alphaAccordionIcon.ki-arrow-down {
        color: #6c757d !important;
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
             <div class="row">
                <div class="col-md-6">
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
                                    <!-- <span class="badge badge-light badge-pill mb-2">Staff ID: {{ $staff->id }}</span> -->
                                    <span class="badge badge-primary badge-pill">{{ $staff->u_status ?? 'Active' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom rounded-lg bg-attendance-shifts">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-user-square text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="total_shifts">0</div>
                                    <div class="text-dark-50">Total Shifts</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom rounded-lg bg-attendance-shifts">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-calendar-tick text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="total_libur">0</div>
                                    <div class="text-dark-50">Total Day off</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom rounded-lg bg-attendance-total">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-calendar text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="present_days">0</div>
                                    <div class="text-dark-50">Present Days</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom rounded-lg bg-other">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-calendar text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="sick_days">0</div>
                                    <div class="text-dark-50">Sick Days</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom rounded-lg bg-other">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-calendar text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="leave_days">0</div>
                                    <div class="text-dark-50">Leave Days</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-custom rounded-lg bg-other">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-calendar text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="late_days">0</div>
                                    <div class="text-dark-50">Late Days</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mt-4">
                    <div class="card card-custom rounded-lg bg-all">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-calendar text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="alpha_days">0</div>
                                    <div class="text-dark-50">Alpha Days</div>
                                </div>
                            </div>
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

            <!-- Alpha Details Section (Accordion) -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card card-custom">
                        <div class="card-header" id="alphaAccordionHeader" style="cursor: pointer;" onclick="toggleAlphaAccordion()">
                            <div class="d-flex justify-content-between align-items-center w-100">
                                <h6 class="card-title mb-0 mt-0">
                                    <i class="ki-outline ki-calendar-remove mr-3" style="font-size: 1.7rem; color: #FF5D5D;"></i>
                                   Alpha Details
                                </h6>
                                <i class="ki-outline ki-down" id="alphaAccordionIcon" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <div class="card-body collapse" id="alphaAccordionBody">
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="text-muted mb-5 fs-6">
                                        <strong>Alpha Logics:</strong> Date with shift (except day off), no absent, no leave, and no sick.
                                    </p>
                                    <div id="alpha_dates_container">
                                        <div class="text-center text-muted">
                                            <i class="ki-outline ki-calendar-tick" style="font-size: 2rem;"></i>
                                            <p>Alpha dates will be displayed here</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <div class="card-toolbar d-flex justify-content-between align-items-center w-100">
                            <div class="d-flex align-items-center">
                            <!-- <input type="search" class="form-control" style="width: 300px;" id="summary_search" placeholder="Search"/> -->
                            </div>
                                <div class="d-flex align-items-center">
                                <!--begin::Button-->
                            <button type="button" class="btn btn-light-green font-weight-bolder mr-2" onclick="exportStaffToExcel()">
                                        <span class="svg-icon svg-icon-md">
                                            <i class="ki-outline ki-file-down"></i>
                                        </span>Export Excel</button>
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <button type="button" class="btn btn-secondary font-weight-bolder mr-2" onclick="exportStaffToPDF()">
                                        <span class="svg-icon svg-icon-md">
                                            <i class="ki-outline ki-file-down"></i>
                                        </span>Export PDF</button>
                                    <!--end::Button-->
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-hover table-checkable" id="staffAttendanceTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Shift</th>
                                        <th>Jam Masuk</th>
                                        <th>Jam Keluar</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                        <th>Action</th>
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
