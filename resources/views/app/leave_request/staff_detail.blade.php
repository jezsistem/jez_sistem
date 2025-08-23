@extends('app.structure')

@section('title', 'Staff Leave Detail')

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
    .bg-leave-approved {
        background-color: #E8F5E8;
    }
    .bg-leave-pending {
        background-color: #FFF3CD;
    }
    .bg-leave-rejected {
        background-color: #F8D7DA;
    }
    .bg-leave-cancelled {
        background-color: #F1F1F4;
    }
    .bg-leave-annual {
        background-color: #E3F2FD;
    }
    .bg-leave-sick {
        background-color: #FFF8E1;
    }
    .bg-leave-emergency {
        background-color: #FFEBEE;
    }
    .bg-leave-maternity {
        background-color: #F3E5F5;
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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Staff Leave Detail - {{ $data['user']->u_name }}</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/dashboard') }}" class="text-muted">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ url('/leave-requests') }}" class="text-muted">Leave Requests</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('leave-requests.summary-report') }}" class="text-muted">Summary Report</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="text-muted">Staff Detail</span>
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
        <div class="container-fluid">
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom rounded-lg bg-leave-approved">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-check-circle text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="approvedCount">0</div>
                                    <div class="text-dark-50">Approved</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom rounded-lg bg-leave-pending">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-clock text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="pendingCount">0</div>
                                    <div class="text-dark-50">Pending</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom rounded-lg bg-leave-rejected">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-cross-circle text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="rejectedCount">0</div>
                                    <div class="text-dark-50">Rejected</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom rounded-lg bg-leave-annual">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-calendar text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="totalDays">0</div>
                                    <div class="text-dark-50">Total Days</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--begin::Card-->
            <div class="card card-custom gutter-b">
                <div class="card-header flex-wrap py-3">
                    <div class="card-toolbar d-flex justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <h3 class="card-label">Staff Leave Detail - {{ $data['user']->u_name }}</h3>
                        </div>
                        <div class="d-flex align-items-center">
                            <!--begin::Button-->
                            <button type="button" class="btn btn-success font-weight-bolder mr-2" onclick="exportToExcel()">
                                <span class="svg-icon svg-icon-md">
                                    <i class="ki-outline ki-file-down"></i>
                                </span>Export Excel</button>
                            <!--end::Button-->
                            <!--begin::Button-->
                            <button type="button" class="btn btn-danger font-weight-bolder" onclick="exportToPDF()">
                                <span class="svg-icon svg-icon-md">
                                    <i class="ki-outline ki-file-down"></i>
                                </span>Export PDF</button>
                            <!--end::Button-->
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!--begin::Staff Information-->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card card-custom bg-light-primary">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-50 symbol-light-primary mr-4">
                                            <span class="symbol-label">
                                                <i class="fas fa-user text-primary"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-muted font-weight-bold">Staff Information</div>
                                            <div class="font-size-h6 font-weight-bold text-primary">{{ $data['user']->u_name }}</div>
                                            <div class="text-muted">{{ $data['user']->u_nip }}</div>
                                            <div class="text-muted">{{ $data['user']->position_name }}</div>
                                            <div class="text-muted">{{ $data['user']->division_name }}</div>
                                            <div class="text-muted">{{ $data['user']->work_type }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-custom bg-light-info">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-50 symbol-light-info mr-4">
                                            <span class="symbol-label">
                                                <i class="fas fa-chart-pie text-info"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-muted font-weight-bold">Leave Statistics</div>
                                            <div id="leaveStats">
                                                <div class="text-muted">Loading statistics...</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Staff Information-->

                    <!--begin::Filter Section-->
                    <div class="card card-custom bg-light-secondary mb-4">
                        <div class="card-body">
                            <form id="filterForm" method="GET" class="form-inline">
                                <div class="form-group mr-3">
                                    <label for="date_filter" class="mr-2">Date Filter:</label>
                                    <select class="form-control" id="date_filter" name="date_filter" onchange="handleDateFilterChange(this.value)">
                                        <option value="this_week" {{ $data['dateFilter'] == 'this_week' ? 'selected' : '' }}>This Week</option>
                                        <option value="past_week" {{ $data['dateFilter'] == 'past_week' ? 'selected' : '' }}>Past Week</option>
                                        <option value="this_month" {{ $data['dateFilter'] == 'this_month' ? 'selected' : '' }}>This Month</option>
                                        <option value="last_month" {{ $data['dateFilter'] == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                        <option value="custom" {{ $data['dateFilter'] == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                    </select>
                                </div>
                                
                                <div class="form-group mr-3" id="start_date_container" style="display: {{ $data['dateFilter'] == 'custom' ? 'block' : 'none' }};">
                                    <label for="start_date" class="mr-2">Start Date:</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $data['startDate'] }}">
                                </div>
                                
                                <div class="form-group mr-3" id="end_date_container" style="display: {{ $data['dateFilter'] == 'custom' ? 'block' : 'none' }};">
                                    <label for="end_date" class="mr-2">End Date:</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $data['endDate'] }}">
                                </div>
                                
                                <div class="form-group mr-3">
                                    <label for="status" class="mr-2">Status:</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                        </div>
                    </div>
                    <!--end::Filter Section-->

                    <!--begin::DataTable-->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="staffTable">
                            <thead>
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="12%">Tanggal Mulai</th>
                                    <th width="12%">Tanggal Selesai</th>
                                    <th width="10%">Durasi</th>
                                    <th width="15%">Jenis Leave</th>
                                    <th width="10%">Status</th>
                                    <th width="20%">Alasan</th>
                                    <th width="16%">Tanggal Request</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
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
@include('app.leave_request.staff_detail_js')
