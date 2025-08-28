@extends('app.structure')
@section('content')
<style>
    /* Remove table-responsive scroll */
    .table-responsive {
        overflow-x: visible;
        overflow-y: visible;
        max-height: none;
    }
    
    /* Fix table height and prevent vertical scroll */
    .table {
        max-height: none;
        overflow: visible;
    }
    
    /* Ensure card body doesn't force scroll */
    .card-body {
        overflow: visible;
        max-height: none;
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
        text-transform: uppercase;
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

    /* Fix for menu positioning in table cells */
    .table td {
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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Log Backup Time</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item text-muted">
                            <span class="text-muted">
                                @if($dateFilter && $dateFilter !== 'custom')
                                    {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}
                                @else
                                    {{ $startDate }} to {{ $endDate }}
                                @endif
                            </span>
                        </li>
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <a href="{{ route('break-times-backup.index') }}" class="btn btn-light-primary">
                    <i class="ki-outline ki-left"></i>
                    Back
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
            <!-- Statistics Cards -->
            <div class="row mb-4" id="statsContainer">
                @foreach($stats ?? [] as $stat)
                    <div class="col-md-2">
                        <div class="card bg-all">
                            <div class="card-body text-center">
                                <h4 class="text-primary" id="stat-{{ $stat->bt_status }}-count">{{ $stat->total }}</h4>
                                <small class="text-muted">
                                    @switch($stat->bt_status)
                                        @case('active')
                                            Active
                                            @break
                                        @case('completed')
                                            Completed
                                            @break
                                        @case('cancelled')
                                            Cancelled
                                            @break
                                        @default
                                            {{ ucfirst($stat->bt_status) }}
                                    @endswitch
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <!-- Additional stats cards -->
                <div class="col-md-2">
                    <div class="card bg-other">
                        <div class="card-body text-center">
                            <h4 class="text-primary" id="stat-total-count">{{ $stats->sum('total') ?? 0 }}</h4>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-other">
                        <div class="card-body text-center">
                            <h4 class="text-primary" id="stat-break1-count">0</h4>
                            <small class="text-muted">Backup 1</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card bg-other">
                        <div class="card-body text-center">
                            <h4 class="text-primary" id="stat-break2-count">0</h4>
                            <small class="text-muted">Backup 2</small>
                        </div>
                    </div>
                </div>
            </div>
             <div class="row mb-4">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Filters</h3>
                        </div>
                        <div class="card-body">
                        <form method="GET" action="{{ route('break-times-backup.report') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date_filter">Date Filter</label>
                                            <select class="form-control" id="date_filter" name="date_filter" onchange="handleDateFilterChange(this.value)">
                                                <option value="this_week" {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
                                                <option value="past_week" {{ $dateFilter == 'past_week' ? 'selected' : '' }}>Past Week</option>
                                                <option value="this_month" {{ $dateFilter == 'this_month' ? 'selected' : '' }}>This Month</option>
                                                <option value="last_month" {{ $dateFilter == 'last_month' ? 'selected' : '' }}>Past Month</option>
                                                <option value="custom" {{ $dateFilter == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2" id="start_date_container" style="display: {{ $dateFilter == 'custom' ? 'block' : 'none' }};">
                                        <div class="form-group">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                                   value="{{ $startDate }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2" id="end_date_container" style="display: {{ $dateFilter == 'custom' ? 'block' : 'none' }};">
                                        <div class="form-group">
                                            <label for="end_date">End Date</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                                   value="{{ $endDate }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="user_id">Staff</label>
                                            <select class="form-control" id="user_id" name="user_id">
                                                <option value="">Semua Staff</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->u_name }} ({{ $user->u_nip }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="division_id">Divisi</label>
                                            <select class="form-control" id="division_id" name="division_id">
                                                <option value="">Semua Divisi</option>
                                                @foreach($divisions as $division)
                                                    <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                                        {{ $division->ud_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="">Semua Status</option>
                                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="break_time_search">Search</label>
                                            <input type="text" class="form-control" id="break_time_search" 
                                                   placeholder="Search by name or NIP">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block" onclick="applyFilters()">
                                                <i class="fas fa-search"></i> Filter
                                            </button>
                                        </div>
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
                            <!-- <input type="search" class="form-control" style="width: 300px;" id="summary_search" placeholder="Search"/> -->
                            </div>

                                <!-- Export Buttons -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end">
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
                            </div>
                        </div>
                        <div class="card-body">
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

                            <table class="table table-hover table-checkable" id="breakTimeTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date</th>
                                        <th>Staff</th>
                                        <th>NIP</th>
                                        <th>Division</th>
                                                <th>Backup Type</th>
                                        <th>Backup Start</th>
                                        <th>Backup End</th>
                                                <th>Duration</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTables will populate this -->
                                </tbody>
                            </table>
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

@include('app._partials.js')
@include('app.break_time_backup.report_js')
