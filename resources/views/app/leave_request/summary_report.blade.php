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

    /* Light statistics cards */
    .bg-leave-approved {
        background-color: #FFEBEB;
    }
    .bg-leave-pending {
        background-color: #F1F1F4;
    }
    .bg-leave-rejected {
        background-color: #F1F1F4;
    }
    .bg-leave-cancelled {
        background-color: #F1F1F4;
    }
    .bg-leave-annual {
        background-color: #F1F1F4;
    }
    .bg-leave-sick {
        background-color: #F1F1F4;
    }
    .bg-leave-emergency {
        background-color: #F1F1F4;
    }
    .bg-leave-maternity {
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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">{{ $data['title'] }}</h5>
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
        <div class="container-fluid">
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                
                @if(isset($data['stats']) && count($data['stats']) > 0)
                    @foreach($data['stats'] as $stat)
                        <div class="col-lg-2 col-md-4">
                            <div class="card card-custom rounded-lg 
                                @switch($stat->lr_status)
                                    @case('approved')
                                        bg-leave-approved
                                        @break
                                    @case('pending')
                                        bg-leave-pending
                                        @break
                                    @case('rejected')
                                        bg-leave-rejected
                                        @break
                                    @case('cancelled')
                                        bg-leave-cancelled
                                        @break
                                    @default
                                        @if(strpos($stat->lr_status, 'leave_') === 0)
                                            bg-leave-annual
                                        @else
                                            bg-leave-pending
                                        @endif
                                @endswitch">
                                <div class="card-body px-7">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40 mr-4">
                                            <span class="symbol-label">
                                                @switch($stat->lr_status)
                                                    @case('approved')
                                                        <i class="ki-outline ki-check-circle text-dark"></i>
                                                        @break
                                                    @case('pending')
                                                        <i class="ki-outline ki-clock text-dark"></i>
                                                        @break
                                                    @case('rejected')
                                                        <i class="ki-outline ki-cross-circle text-dark"></i>
                                                        @break
                                                    @case('cancelled')
                                                        <i class="ki-outline ki-cross text-dark"></i>
                                                        @break
                                                    @default
                                                        <i class="ki-outline ki-calendar text-dark"></i>
                                                @endswitch
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-dark font-weight-bold font-size-h5">{{ $stat->total }}</div>
                                            <div class="text-dark-50">
                                                @switch($stat->lr_status)
                                                    @case('approved')
                                                        Approved
                                                        @break
                                                    @case('pending')
                                                        Pending
                                                        @break
                                                    @case('rejected')
                                                        Rejected
                                                        @break
                                                    @case('cancelled')
                                                        Cancelled
                                                        @break
                                                    @default
                                                        {{ ucfirst($stat->lr_status) }}
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- Default statistics if no stats provided -->
                    <div class="col-lg-2 col-md-4">
                        <div class="card card-custom rounded-lg bg-leave-approved">
                            <div class="card-body px-7">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label">
                                            <i class="ki-outline ki-check-circle text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5">0</div>
                                        <div class="text-dark-50">Approved</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <div class="card card-custom rounded-lg bg-leave-pending">
                            <div class="card-body px-7">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label">
                                            <i class="ki-outline ki-loading text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5">0</div>
                                        <div class="text-dark-50">Pending</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <div class="card card-custom rounded-lg bg-leave-rejected">
                            <div class="card-body px-7">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label">
                                            <i class="ki-outline ki-cross-circle text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5">0</div>
                                        <div class="text-dark-50">Rejected</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <div class="card card-custom rounded-lg bg-leave-cancelled">
                            <div class="card-body px-7">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label">
                                            <i class="ki-outline ki-cross text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5">0</div>
                                        <div class="text-dark-50">Cancelled</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <div class="card card-custom rounded-lg bg-leave-annual">
                            <div class="card-body px-7">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label">
                                            <i class="ki-outline ki-calendar text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5">0</div>
                                        <div class="text-dark-50">Annual Leave</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <div class="card card-custom rounded-lg bg-leave-sick">
                            <div class="card-body px-7">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label">
                                            <i class="ki-outline ki-calendar text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5">0</div>
                                        <div class="text-dark-50">Sick Leave</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Filters</h3>
                        </div>
                        <div class="card-body">
                            <form id="filterForm" method="GET" action="{{ route('leave-requests.summary-report') }}">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="date_filter">Date Filter</label>
                                        <select class="form-control" id="date_filter" name="date_filter" onchange="handleDateFilterChange(this.value)">
                                            <option value="this_week" {{ $data['dateFilter'] == 'this_week' ? 'selected' : '' }}>This Week</option>
                                            <option value="past_week" {{ $data['dateFilter'] == 'past_week' ? 'selected' : '' }}>Past Week</option>
                                            <option value="next_week" {{ $data['dateFilter'] == 'next_week' ? 'selected' : '' }}>Next Week</option>
                                            <option value="this_month" {{ $data['dateFilter'] == 'this_month' ? 'selected' : '' }}>This Month</option>
                                            <option value="last_month" {{ $data['dateFilter'] == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                            <option value="next_month" {{ $data['dateFilter'] == 'next_month' ? 'selected' : '' }}>Next Month</option>
                                            <option value="custom" {{ $data['dateFilter'] == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" id="start_date_container" style="display: {{ $data['dateFilter'] == 'custom' ? 'block' : 'none' }};">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $data['startDate'] }}">
                                    </div>
                                    <div class="col-md-2" id="end_date_container" style="display: {{ $data['dateFilter'] == 'custom' ? 'block' : 'none' }};">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $data['endDate'] }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="division_id">Division</label>
                                        <select class="form-control" id="division_id" name="division_id">
                                            <option value="">All Divisions</option>
                                            @foreach($data['divisions'] as $division)
                                                <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                                    {{ $division->ud_name }}
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

            <!--begin::Card-->
            <div class="card card-custom gutter-b">
                <div class="card-header flex-wrap py-3">
                    <div class="card-toolbar d-flex justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <input type="search" class="form-control" style="width: 300px;" id="summary_search" placeholder="Search"/>
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
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table table-hover" id="summaryTable">
                            <thead>
                            <tr>
                                <th>No.</th>
                                <th>NIP</th>
                                <th>Staff</th>
                                <th>Position</th>
                                <th>Division</th>
                                <th>User Type</th>
                                <th>Pending Approval</th>
                                <th>Approved</th>
                                <th>Rejected</th>
                                <th>Reporting</th>
                                <th>HR Check</th>
                                <th>Finance Process</th>
                                <th>Done</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <!--end::Table-->
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
@include('app.leave_request.summary_report_js')
