@extends('app.structure')

@section('title', 'Staff Backup Time Detail')

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

    /* Light statistics cards */
    .bg-break-total {
        background-color: #E8F5E8;
    }
    .bg-break-shifts {
        background-color: #FFF3CD;
    }
    .bg-break-exceeded {
        background-color: #F8D7DA;
    }
    .bg-break-avg {
        background-color: #F1F1F4;
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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Backup Time Details - {{ $data['staff']->u_name }}</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    <!-- <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ url('/break-times') }}" class="text-muted">Break Times</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('break-times.summary-report') }}" class="text-muted">Summary Report</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="text-muted">Staff Detail</span>
                        </li>
                    </ul> -->
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <a href="{{ route('break-times-backup.report') }}" class="btn btn-secondary font-weight-bolder">
                    <i class="ki-outline ki-arrow-left"></i> Back
                </a>
            </div>
            <!--end::Toolbar-->
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container-fluid">
            <div class="row">
            <div class="col-md-6">
            <div class="staff-info">
                <div class="row">
                    <div class="col-md-8">
                       <h4>{{ $data['staff']->u_name }}</h4>
                       <p><strong>NIP:</strong> {{ $data['staff']->u_nip }}</p>
                       <p><strong>Position:</strong> {{ $data['staff']->position_name }}</p>
                       <p><strong>Division:</strong> {{ $data['staff']->division_name }}</p>
                       <p><strong>Work Type:</strong> {{ $data['staff']->work_type }}</p>
                    </div>
                    <div class="col-md-4 text-right">
                    </div>
                </div>
            </div>
            </div>
            </div>


            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom rounded-lg bg-break-total">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-coffee text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="totalBreaks">-</div>
                                    <div class="text-dark-50">Total Backup</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom rounded-lg bg-break-shifts">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-calendar text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="totalShifts">-</div>
                                    <div class="text-dark-50">Total Shift</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-lg-3 col-md-6">
                    <div class="card card-custom rounded-lg bg-break-exceeded">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-time text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="exceededBreaks">-</div>
                                    <div class="text-dark-50">Exceeded Backup <small class="text-dark-50" id="breakAllowanceInfo">-</small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom rounded-lg bg-break-avg">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-calculator text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="avgDuration">-</div>
                                    <div class="text-dark-50">Avg Duration</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                                               value="{{ request('start_date', date('Y-m-d')) }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">Semua Status</option>
                                            <option value="active">Active</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
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
            <!--begin::Card-->
            <div class="card card-custom gutter-b">
            <div class="card-header">
                            <div class="card-toolbar d-flex justify-content-between align-items-center w-100">
                            <div class="d-flex align-items-center">
                            <!-- <input type="search" class="form-control" style="width: 300px;" id="summary_search" placeholder="Search"/> -->
                            </div>
                                <div class="d-flex align-items-center">
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
                                </div>
                            </div>
                        </div>
                <div class="card-body">
                    <!--begin::DataTable-->
                    <div class="table-responsive">
                        <table class="table table-hover table-checkable" id="staffTable">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Date</th>
                                    <th width="8%">Backup Type</th>
                                    <th width="8%">Backup Start</th>
                                    <th width="8%">Backup End</th>
                                    <th width="8%">Duration</th>
                                    <th width="8%">Status</th>
                                    <th width="8%">Shift Start</th>
                                    <th width="8%">Shift End</th>
                                    <th width="20%">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded by DataTables -->
                            </tbody>
                        </table>
                    </div>
                    <!--end::DataTable-->
                </div>
            </div>
            <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

@endsection

@include('app._partials.js')
@include('app.break_time_backup.staff_detail_js')
