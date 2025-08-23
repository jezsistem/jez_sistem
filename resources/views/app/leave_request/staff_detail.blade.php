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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Staff Leave Details</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page Heading-->
                
            </div>
            <!--end::Info-->
             <!--begin::Toolbar-->
             <div class="d-flex align-items-center">
                <a href="{{ route('leave-requests.summary-report') }}" class="btn btn-secondary font-weight-bolder">
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
        <div class="container-fluid">
            
        <!-- Staff Information Card -->
        <div class="row">
                <div class="col-md-6">
                    <div class="staff-info">
                        <div class="row">
                            <div class="col-md-8">
                                <h4>{{ $data['user']->u_name }}</h4>
                                <p><strong>NIP:</strong> {{ $data['user']->u_nip }}</p>
                                <p><strong>Position:</strong> {{ $data['user']->position_name }}</p>
                                <p><strong>Division:</strong> {{ $data['user']->division_name }}</p>
                                <p><strong>Work Type:</strong> {{ $data['user']->work_type }}</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <div class="d-flex flex-column align-items-end">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom rounded-lg bg-all">
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
                    <div class="card card-custom rounded-lg bg-other">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-loading text-dark"></i>
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
                    <div class="card card-custom rounded-lg bg-other">
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
                    <div class="card card-custom rounded-lg bg-other">
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

            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                Filter
                            </h6>
                        </div>
                        <div class="card-body">
                            <form id="filterForm" method="GET">
                                <div class="row w-100">
                                    <div class="col-md-2">
                                        <label for="date_filter">Date Filter</label>
                                        <select class="form-control" id="date_filter" name="date_filter" onchange="handleDateFilterChange(this.value)">
                                            <option value="this_week" {{ $data['dateFilter'] == 'this_week' ? 'selected' : '' }}>This Week</option>
                                            <option value="past_week" {{ $data['dateFilter'] == 'past_week' ? 'selected' : '' }}>Past Week</option>
                                            <option value="this_month" {{ $data['dateFilter'] == 'this_month' ? 'selected' : '' }}>This Month</option>
                                            <option value="last_month" {{ $data['dateFilter'] == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                            <option value="custom" {{ $data['dateFilter'] == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2" id="start_date_container" style="display: {{ $data['dateFilter'] == 'custom' ? 'block' : 'none' }};">
                                        <label for="start_date" class="mr-2">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $data['startDate'] }}">
                                    </div>
                                    
                                    <div class="col-md-2" id="end_date_container" style="display: {{ $data['dateFilter'] == 'custom' ? 'block' : 'none' }};">
                                        <label for="end_date" class="mr-2">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $data['endDate'] }}">
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label for="status" class="mr-2">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">All Status</option>
                                            <option value="pending">Pending</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
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
                <div class="card-header flex-wrap py-3">
                    <div class="card-toolbar d-flex justify-content-between w-100">
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
                        <table class="table table-checkable table-hover" id="staffTable">
                            <thead>
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="12%">Start Date</th>
                                    <th width="12%">End Date</th>
                                    <th width="10%">Duration</th>
                                    <th width="15%">Leave Type</th>
                                    <th width="10%">Status</th>
                                    <th width="20%">Reason</th>
                                    <th width="16%">Request Date</th>
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
