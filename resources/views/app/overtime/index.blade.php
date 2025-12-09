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
            background-color: rgba(0,0,0,0.5);
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


    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    {{-- ========================= SUMMARY CARDS ========================= --}}
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-4">
                            <div class="card card-custom rounded-lg bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label text-dark">
                                            <i class="fa fa-list text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5">{{ $summary['total'] ?? 0 }}
                                        </div>
                                        <div class="text-dark-50">Total Requests</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4">
                            <div class="card card-custom rounded-lg bg-warning">
                                <div class="card-body d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label bg-white text-dark">
                                            <i class="fa fa-hourglass-half text-dark"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-dark font-weight-bold font-size-h5">{{ $summary['pending'] ?? 0 }}
                                        </div>
                                        <div class="text-dark-50">Pending</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4">
                            <div class="card card-custom rounded-lg bg-success text-white">
                                <div class="card-body d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label bg-white text-success">
                                            <i class="fa fa-check-circle text-success"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-white font-weight-bold font-size-h5">
                                            {{ $summary['approved'] ?? 0 }}</div>
                                        <div class="text-white-50">Approved</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4">
                            <div class="card card-custom rounded-lg bg-info text-white">
                                <div class="card-body d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                                        <span class="symbol-label bg-white text-info">
                                            <i class="fa fa-user-check text-info"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-white font-weight-bold font-size-h5">
                                            {{ $summary['hr_check'] ?? 0 }}</div>
                                        <div class="text-white-50">HR Check</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ========================= FILTER FORM ========================= --}}
                    <div class="card mb-5">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-filter"></i> Filters</h5>
                        </div>
                        <div class="card-body">
                            <form id="filterForm" class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="Pending">Pending</option>
                                        <option value="">All</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="HR Check">HR Check</option>
                                        <option value="Done">Done</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="division">Division</label>
                                    <select class="form-control" id="division" name="division">
                                        <option value="">All</option>
                                        @foreach ($data['divisions'] as $division)
                                            <option value="{{ $division->id }}">{{ $division->ud_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="end_date">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="staff">Staff</label>
                                    <input type="text" class="form-control" id="staff" name="staff"
                                        placeholder="Staff Name">
                                </div>
                                <div class="col-md-3 mb-3 align-self-end">
                                    <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search"></i>
                                        Apply</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Overtime Requests</h4>
                            <div>
                                <button type="button" class="btn btn-light-green font-weight-bolder mr-2" onclick="exportRequestToExcel()">
                                        <span class="svg-icon svg-icon-md">
                                            <i class="ki-outline ki-file-down"></i>
                                        </span>Export Excel</button>
                                <a href="{{ url('overtime/create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> New Request
                                </a>
                            </div>
                        </div>

                        <div class="card-body table-responsive">
                            <table id="overtimeTable" class="table table-hover table-checkable w-100">
                                <thead class="bg-light text-dark">
                                <tr>
                                    <th class="text-dark">#</th>
                                    <th class="text-dark">Submission Date</th>
                                    <th class="text-dark">Department</th>
                                    <th class="text-dark">Assigned Staff</th>
                                    <th class="text-dark">Start</th>
                                    <th class="text-dark">End</th>
                                    <th class="text-dark">Duration</th>
                                    <th class="text-dark">Claim Type</th>
                                    <th class="text-dark">Requested By</th>
                                    <th class="text-dark">Approver</th>
                                    <th class="text-dark">Status</th>
                                    <th class="text-dark">Created At</th>
                                    <th class="text-dark" width="100">Actions</th>
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
    </div>

    @include('app._partials.js')
    @include('app.overtime.overtime_js')
@endsection
