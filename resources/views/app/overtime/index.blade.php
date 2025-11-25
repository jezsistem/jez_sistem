@extends('app.structure')

@section('content')
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
                                        <option value="">All</option>
                                        <option value="Pending">Pending</option>
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

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="overtimeTable" class="table table-bordered table-striped w-100">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Submission Date</th>
                                            <th>Department</th>
                                            <th>Assigned Staff</th>
                                            <th>Start</th>
                                            <th>End</th>
                                            <th>Duration</th>
                                            <th>Claim Type</th>
                                            <th>Requested By</th>
                                            <th>Approver</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th width="100">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    @include('app._partials.js')
    @include('app.overtime.overtime_js')
@endsection
