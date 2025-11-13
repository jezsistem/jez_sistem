@extends('app.structure')
@section('content')

    <style>
        /* Custom CSS for Metronic dropdown menu */
        /* Modal styles using vanilla CSS (like staff) */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 0;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 15px;
        }

        .modal-footer {
            padding: 15px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
        }

        .close:hover {
            color: #000;
        }

        .modal-open {
            overflow: hidden;
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
            right: 0 !important;
            margin-top: 5px !important;
            min-width: 150px !important;
            background: white !important;
            border: 1px solid #e4e6ef !important;
            border-radius: 0.475rem !important;
            box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075) !important;
        }

        /* Ensure proper positioning for DataTables */
        .dataTables_wrapper .dataTables_processing {
            z-index: 9998;
        }

        /* Fix for menu positioning in table cells */
        #leaveRequestTable td {
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

        .menu-item .menu-link.text-success {
            color: #1bc5bd !important;
        }

        .menu-item .menu-link.text-success:hover {
            background-color: #e1f0ff !important;
            color: #1bc5bd !important;
        }

        .bg-all {
            background-color: #FFEBEB;
        }

        .bg-other {
            background-color: #F1F1F4;
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
                                    @if ($data['dateFilter'] && $data['dateFilter'] !== 'custom')
                                        {{ date('d M Y', strtotime($data['startDate'])) }} to
                                        {{ date('d M Y', strtotime($data['endDate'])) }}
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
                    <div class="col-lg-3 col-md-4 mb-4">
                        <div class="card card-custom rounded-lg bg-all">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label text-dark">
                                            <i class="ki-outline ki-calendar-tick text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5" id="total-requests">
                                            {{ $assignmentRequestData->count() }}</div>
                                        <div class="text-dark-50">Total Requests</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 mb-4">
                        <div class="card card-custom rounded-lg bg-other">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label bg-white text-dark">
                                            <i class="ki-outline ki-loading text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5" id="pending-requests">
                                            {{ $assignmentRequestData->where('ear_status', 'Pending Approval')->count() }}</div>
                                        <div class="text-dark-50">Pending</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 mb-4">
                        <div class="card card-custom rounded-lg bg-other">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label bg-white text-dark">
                                            <i class="ki-outline ki-check-circle text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5" id="approved-requests">
                                            {{ $assignmentRequestData->where('ear_status', 'Approved')->count() }}</div>
                                        <div class="text-dark-50">Approved</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 mb-4">
                        <div class="card card-custom rounded-lg bg-other">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40  mr-4">
                                        <span class="symbol-label bg-white text-dark">
                                            <i class="ki-outline ki-cross-circle text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5" id="rejected-requests">
                                            {{ $assignmentRequestData->where('ear_status', 'Rejected')->count() }}</div>
                                        <div class="text-dark-50">Rejected</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 mb-4">
                        <div class="card card-custom rounded-lg bg-other">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40  mr-4">
                                        <span class="symbol-label bg-white text-dark">
                                            <i class="ki-outline ki-magnifier text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5" id="hr-check-requests">
                                            {{ $assignmentRequestData->where('ear_status', 'HR Check')->count() }}</div>
                                        <div class="text-dark-50">HR Check</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 mb-4">
                        <div class="card card-custom rounded-lg bg-other">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40  mr-4">
                                        <span class="symbol-label bg-white text-dark">
                                            <i class="ki-outline ki-dollar text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5" id="finance-requests">
                                            {{ $assignmentRequestData->where('ear_status', 'Finance Process')->count() }}</div>
                                        <div class="text-dark-50">Finance Process</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 mb-4">
                        <div class="card card-custom rounded-lg" style="background-color: #C9F7F5;">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-40  mr-4">
                                        <span class="symbol-label bg-white text-dark">
                                            <i class="ki-outline ki-check-circle text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5" id="rejected-requests">
                                            {{ $assignmentRequestData->where('ear_status', 'DONE')->count() }}</div>
                                        <div class="text-dark-50">Done</div>
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
                                <form method="GET" action="{{ route('leave-requests.index') }}" id="filterForm">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="date_filter">Date Filter</label>
                                            <select class="form-control" id="date_filter" name="date_filter"
                                                onchange="handleDateFilterChange(this.value)">
                                                <option value="this_week"
                                                    {{ $dateFilter == 'this_week' ? 'selected' : '' }}>This Week</option>
                                                <option value="past_week"
                                                    {{ $dateFilter == 'past_week' ? 'selected' : '' }}>Past Week</option>
                                                <option value="next_week"
                                                    {{ $dateFilter == 'next_week' ? 'selected' : '' }}>Next Week</option>
                                                <option value="this_month"
                                                    {{ $dateFilter == 'this_month' ? 'selected' : '' }}>This Month</option>
                                                <option value="last_month"
                                                    {{ $dateFilter == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                                <option value="next_month"
                                                    {{ $dateFilter == 'next_month' ? 'selected' : '' }}>Next Month</option>
                                                <option value="custom" {{ $dateFilter == 'custom' ? 'selected' : '' }}>
                                                    Custom Range</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2" id="start_date_container"
                                            style="display: {{ $dateFilter == 'custom' ? 'block' : 'none' }};">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date"
                                                value="{{ $startDate }}">
                                        </div>
                                        <div class="col-md-2" id="end_date_container"
                                            style="display: {{ $dateFilter == 'custom' ? 'block' : 'none' }};">
                                            <label for="end_date">End Date</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date"
                                                value="{{ $endDate }}">
                                        </div>
                                        {{-- <div class="col-md-2">
                                        <label for="user_id">Staff</label>
                                        <select class="form-control" id="user_id" name="user_id">
                                            <option value="">All Staffs</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                                    {{ $user->u_name }} ({{ $user->u_nip }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div> --}}
                                        <div class="col-md-2">
                                            <label for="ea_type_id">External Assign Type</label>
                                            <select class="form-control" id="ea_type_id" name="ea_type_id">
                                                <option value="">All Types</option>
                                                @foreach ($externalAssignType as $type)
                                                    <option value="{{ $type->id }}"
                                                        {{ $leaveTypeId == $type->id ? 'selected' : '' }}>
                                                        {{ $type->ea_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="" {{ empty($status) ? 'selected' : '' }}>All Status
                                                </option>
                                                <option value="Pending Approval"
                                                    {{ $status == 'Pending Approval' ? 'selected' : '' }}>Pending Approval
                                                </option>
                                                <option value="Approved" {{ $status == 'Approved' ? 'selected' : '' }}>
                                                    Approved</option>
                                                <option value="Rejected" {{ $status == 'Rejected' ? 'selected' : '' }}>
                                                    Rejected</option>
                                                <option value="HR Check" {{ $status == 'HR Check' ? 'selected' : '' }}>HR
                                                    Check</option>
                                                <option value="Finance Process"
                                                    {{ $status == 'Finance Process' ? 'selected' : '' }}>Finance Process
                                                </option>
                                                <option value="DONE" {{ $status == 'DONE' ? 'selected' : '' }}>Done
                                                </option>
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
                                <input type="search" class="form-control" style="width: 300px;" id="summary_search"
                                    placeholder="Search" />
                            </div>
                            <div class="d-flex align-items-center">
                                <!--begin::Button-->
                                <button type="button" class="btn btn-light-green font-weight-bolder mr-2"
                                    onclick="exportToExcel()">
                                    <span class="svg-icon svg-icon-md">
                                        <i class="ki-outline ki-file-down"></i>
                                    </span>Export Excel</button>
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
                                        <th>Requester</th>
                                        <th>Division</th>
                                        <th>Assignment Type</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Location</th>
                                        <th>Cash Advance</th>
                                        <th>Cash Detail</th>
                                        <th>Cash Report</th>
                                        <th>SPV Approve</th>
                                        <th>HR Approve</th>
                                        <th>Finance</th>
                                        <th>Finance File</th>
                                        <th>Status</th>
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
@include('app.external_assignment_request.summary_report_js')
