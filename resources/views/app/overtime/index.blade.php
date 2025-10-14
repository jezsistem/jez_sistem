@extends('app.structure')

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Overtime Requests</h4>
                            <a href="{{ url('overtime/create') }}" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> New Request
                            </a>
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
                                        <th>Claim (Rp)</th>
                                        <th>Requested By</th>
                                        <th>Approval</th>
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
