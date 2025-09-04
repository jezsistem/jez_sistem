@extends('app.structure')

@section('title', 'Break Time Summary Report')

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
    .bg-break-total {
        background-color: #E8F5E8;
    }
    .bg-break-shifts {
        background-color: #FFF3CD;
    }
    .bg-break-breaks {
        background-color: #F8D7DA;
    }
    .bg-break-no-break {
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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Break Time Summary Report</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
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
            <div class="row mb-4" id="statsContainer">
                <div class="col-lg-2 col-md-6">
                    <div class="card card-custom rounded-lg bg-break-total">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-user-square text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="stat-total-staff">{{ $summaryData->count() ?? 0 }}</div>
                                    <div class="text-dark-50">Total Staff</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="card card-custom rounded-lg bg-break-shifts">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-calendar text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="stat-total-shifts">{{ $summaryData->sum('total_shifts') ?? 0 }}</div>
                                    <div class="text-dark-50">Total Shifts</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="card card-custom rounded-lg bg-break-breaks">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-coffee text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="stat-total-breaks">{{ $summaryData->sum('total_breaks') ?? 0 }}</div>
                                    <div class="text-dark-50">Total Breaks</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="card card-custom rounded-lg bg-break-no-break">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-cross-circle text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="stat-no-break-shifts">{{ $summaryData->sum('no_break_shifts') ?? 0 }}</div>
                                    <div class="text-dark-50">No Break Shifts</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="card card-custom rounded-lg bg-break-total">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-time text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="stat-exceeded-break-time">{{ $summaryData->sum('exceeded_break_time') ?? 0 }}</div>
                                    <div class="text-dark-50">Exceeded Break</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="card card-custom rounded-lg bg-break-shifts">
                        <div class="card-body px-7">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-calculator text-dark"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-dark font-weight-bold font-size-h5" id="stat-avg-breaks-per-staff">{{ $summaryData->count() > 0 ? round($summaryData->sum('total_breaks') / $summaryData->count(), 1) : 0 }}</div>
                                    <div class="text-dark-50">Avg Breaks/Staff</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Filters</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('break-times.summary-report') }}" id="filterForm">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="date_filter">Date Filter</label>
                                        <select class="form-control" id="date_filter" name="date_filter" onchange="handleDateFilterChange(this.value)">
                                            <option value="this_week" {{ $data['dateFilter'] == 'this_week' ? 'selected' : '' }}>This Week</option>
                                            <option value="past_week" {{ $data['dateFilter'] == 'past_week' ? 'selected' : '' }}>Past Week</option>
                                            <option value="this_month" {{ $data['dateFilter'] == 'this_month' ? 'selected' : '' }}>This Month</option>
                                            <option value="last_month" {{ $data['dateFilter'] == 'last_month' ? 'selected' : '' }}>Past Month</option>
                                            <option value="custom" {{ $data['dateFilter'] == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2" id="start_date_container" style="display: {{ $data['dateFilter'] == 'custom' ? 'block' : 'none' }};">
                                        <label for="start_date">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="{{ $data['startDate'] }}">
                                    </div>
                                    <div class="col-md-2" id="end_date_container" style="display: {{ $data['dateFilter'] == 'custom' ? 'block' : 'none' }};">
                                        <label for="end_date">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="{{ $data['endDate'] }}">
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
                                        <label for="search">Search</label>
                                        <input type="text" class="form-control" id="search" name="search" placeholder="Search name or NIP..." value="{{ request('search') }}">
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
                            <button type="button" class="btn btn-light-green font-weight-bolder mr-2" onclick="exportToExcel()">
                                <span class="svg-icon svg-icon-md">
                                    <i class="ki-outline ki-file-down"></i>
                                </span>Export Excel
                            </button>
                            <button type="button" class="btn btn-secondary font-weight-bolder mr-2" onclick="exportToPDF()">
                                <span class="svg-icon svg-icon-md">
                                    <span class="svg-icon svg-icon-md">
                                        <i class="ki-outline ki-file-down"></i>
                                    </span>
                                </span>Export PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!--begin::Table-->
                    <div class="table-responsive">
                        <table class="table table-hover table-checkable" id="summaryTable">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">NIP</th>
                                    <th width="15%">Staff</th>
                                    <th width="12%">Position</th>
                                    <th width="12%">Division</th>
                                    <th width="15%">User Type</th>
                                    <th width="8%" class="text-center">Total Break</th>
                                    <th width="15%" class="text-center">No Break</th>
                                    <th width="8%" class="text-center">Exceeded Break</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded by DataTables -->
                            </tbody>
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
@include('app.break_time.summary_report_js')
