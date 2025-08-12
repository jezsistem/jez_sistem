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
                                    <div class="col-md-3">
                                        <label for="start_date">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="{{ request('start_date', date('Y-m-d', strtotime('-30 days'))) }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="end_date">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="{{ request('end_date', date('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-3">
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
                                    <div class="col-md-3">
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
                        <!-- <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="ki-outline ki-calendar"></i> Data Absensi
                            </h6>
                        </div> -->
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
