@extends('app.structure')
@section('content')
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
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-4">
                    <div class="card card-custom rounded-lg bg-dark">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 symbol-primary mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-calendar-tick text-white"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-white font-weight-bold font-size-h6" id="totalSchedules">0</div>
                                    <div class="text-white-50">Total Schedules</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-lg-4 col-md-6">
                    <div class="card card-custom bg-warning">
                <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50 symbol-light-warning mr-4">
                                    <span class="symbol-label">
                                        <i class="fas fa-clock text-white"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-white font-weight-bold font-size-h6" id="scheduledSchedules">0</div>
                                    <div class="text-white-50">Scheduled</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
                <!-- <div class="col-lg-4 col-md-6">
                    <div class="card card-custom bg-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-50 symbol-light-success mr-4">
                                    <span class="symbol-label">
                                        <i class="fas fa-check text-white"></i>
                                    </span>
                        </div>
                                <div>
                                    <div class="text-white font-weight-bold font-size-h6" id="completedSchedules">0</div>
                                    <div class="text-white-50">Completed</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card card-custom">
                        <div class="card-header">
                            <h3 class="card-title">Filters</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2">
                                        <label for="start_date_filter">Start Date</label>
                                        <input type="date" class="form-control" id="start_date_filter" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-2">
                                        <label for="end_date_filter">End Date</label>
                                        <input type="date" class="form-control" id="end_date_filter" value="{{ date('Y-m-d', strtotime('+7 days')) }}">
                            </div>
                            <div class="col-md-2">
                                        <label for="employee_filter">Employee</label>
                                        <select class="form-control" id="employee_filter">
                                            <option value="">All Employees</option>
                                        @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->u_name }}</option>
                                        @endforeach
                                    </select>
                            </div>
                            <div class="col-md-2">
                                        <label for="division_filter">Division</label>
                                        <select class="form-control" id="division_filter">
                                            <option value="">All Divisions</option>
                                        @foreach($divisions as $division)
                                                <option value="{{ $division->id }}">{{ $division->ud_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                        <label for="shift_filter">Shift</label>
                                        <select class="form-control" id="shift_filter">
                                            <option value="">All Shifts</option>
                                            @foreach($shiftCodes as $shift)
                                                <option value="{{ $shift->id }}">{{ $shift->sc_code }}</option>
                                            @endforeach
                                        </select>
                            </div>
                            <div class="col-md-2">
                                        <label for="status_filter">Status</label>
                                        <select class="form-control" id="status_filter">
                                            <option value="">All Status</option>
                                            <option value="scheduled">Scheduled</option>
                                            <option value="completed">Completed</option>
                                    </select>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                            <div class="col-md-2">
                                    <button type="button" class="btn btn-primary btn-block" onclick="applyFilters()">
                                        <i class="ki-outline ki-filter-tick"></i> Apply Filters
                                    </button>
                                </div>
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
                                    <input type="search" class="form-control" style="width: 300px;" id="daily_schedule_search" placeholder="Search"/>
                                </div>
                                <div class="d-flex align-items-center">
                                    <!--begin::Button-->
                                    <a href="{{ route('daily-schedules.create') }}" class="btn btn-dark font-weight-bolder mr-2">
                                    <span class="svg-icon svg-icon-md">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24" />
                                                <circle fill="#000000" cx="9" cy="15" r="6" />
                                                <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>Data Baru</a>
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <a href="{{ route('daily-schedules.create-range') }}" class="btn btn-info font-weight-bolder mr-2">
                                    <span class="svg-icon svg-icon-md">
                                        <i class="ki-outline ki-calendar-add"></i>
                                    </span>Data Range</a>
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <a href="{{ route('daily-schedules.bulk-create') }}" class="btn btn-warning font-weight-bolder mr-2">
                                    <span class="svg-icon svg-icon-md">
                                        <i class="ki-outline ki-file-up"></i>
                                    </span>Bulk Create</a>
                                    <!--end::Button-->
                                    <!--begin::Button-->
                                    <a href="{{ route('daily-schedules.weekly') }}" class="btn btn-primary font-weight-bolder">
                                    <span class="svg-icon svg-icon-md">
                                        <i class="ki-outline ki-calendar-tick"></i>
                                    </span>Weekly Schedule</a>
                                    <a href="{{ route('daily-schedules.monthly-report') }}" class="btn btn-success font-weight-bolder">
                                    <span class="svg-icon svg-icon-md">
                                        <i class="ki-outline ki-calendar-8"></i>
                                    </span>Monthly Report</a>
                                    <!--end::Button-->
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <table class="table table-hover table-checkable" id="dailyScheduleTable">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Date</th>
                                        <th class="text-dark">Staff</th>
                                        <th class="text-dark">Division</th>
                                        <th class="text-dark">Shift</th>
                                        <th class="text-dark">Start Time</th>
                                        <th class="text-dark">End Time</th>
                                        <th class="text-dark">Status</th>
                                        <th class="text-dark">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will populate this -->
                            </tbody>
                        </table>
                            <!--end: Datatable-->
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
@include('app.daily_schedule.daily_schedule_js')

<style>
/* Action buttons styling */
.btn-group-vertical {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.btn-group-vertical .btn {
    border-radius: 4px !important;
    margin-bottom: 2px;
    font-size: 11px;
    padding: 4px 8px;
    min-width: 30px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-group-vertical .btn:last-child {
    margin-bottom: 0;
}

.btn-group-vertical .btn i {
    font-size: 10px;
}

/* Division grouping styling */
.division-subheader {
    background-color: #e6e8eb !important;
}

.division-header-cell {
    background-color:#e6e8eb !important;
    color: #000;
    font-weight: 600 !important;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    padding: 15px 8px !important;
    text-align: left;
    font-size: 15px;
    position: relative;
    padding-left: 1.5rem !important;
}


.table-secondary {
    background-color: #f8f9fa !important;
}

/* Table styling improvements */
#dailyScheduleTable th {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    cursor: default;
}

#dailyScheduleTable th:hover {
    background-color: #f8f9fa !important;
    color: #495057 !important;
}

#dailyScheduleTable td {
    font-size: 12px;
    vertical-align: middle;
}

/* Status badge improvements */
.badge {
    font-size: 10px;
    padding: 4px 8px;
    border-radius: 12px;
}

/* Filter card improvements */
.card-custom {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

/* Statistics card improvements */
.bg-primary, .bg-success, .bg-warning {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.symbol-50 {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.font-size-h6 {
    font-size: 1.5rem;
    font-weight: 700;
}

/* Division badge styling */
.badge-info {
    background-color: #17a2b8;
    color: white;
}

/* Hover effects */
#dailyScheduleTable tbody tr:hover {
    background-color: #e3f2fd !important;
}

/* Remove sorting indicators */
.sorting, .sorting_asc, .sorting_desc {
    background-image: none !important;
}

.sorting::after, .sorting_asc::after, .sorting_desc::after {
    content: "" !important;
}
</style>