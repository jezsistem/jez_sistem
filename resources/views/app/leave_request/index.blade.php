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
                                    <div class="text-white font-weight-bold font-size-h6">{{ $leaveRequests->count() }}</div>
                                    <div class="text-white-50">Total Requests</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="card card-custom rounded-lg bg-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 symbol-warning mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-clock text-white"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-white font-weight-bold font-size-h6">{{ $leaveRequests->where('lr_status', 'pending')->count() }}</div>
                                    <div class="text-white-50">Pending</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="card card-custom rounded-lg bg-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 symbol-success mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-check text-white"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-white font-weight-bold font-size-h6">{{ $leaveRequests->where('lr_status', 'approved')->count() }}</div>
                                    <div class="text-white-50">Approved</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="card card-custom rounded-lg bg-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40 symbol-danger mr-4">
                                    <span class="symbol-label">
                                        <i class="ki-outline ki-cross text-white"></i>
                                    </span>
                                </div>
                                <div>
                                    <div class="text-white font-weight-bold font-size-h6">{{ $leaveRequests->where('lr_status', 'rejected')->count() }}</div>
                                    <div class="text-white-50">Rejected</div>
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
                            <form method="GET" action="{{ route('leave-requests.index') }}">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" 
                                               value="{{ $startDate }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" 
                                               value="{{ $endDate }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="user_id">Staff</label>
                                        <select class="form-control" id="user_id" name="user_id">
                                            <option value="">All Staffs</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                                    {{ $user->u_name }} ({{ $user->u_nip }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="leave_type_id">Leave Type</label>
                                        <select class="form-control" id="leave_type_id" name="leave_type_id">
                                            <option value="">All Types</option>
                                            @foreach($leaveTypes as $type)
                                                <option value="{{ $type->id }}" {{ $leaveTypeId == $type->id ? 'selected' : '' }}>
                                                    {{ $type->lt_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="status">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">All Status</option>
                                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="ki-outline ki-filter-tick"></i> Apply Filters
                                        </button>
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
                                    <input type="search" class="form-control" style="width: 300px;" id="leave_request_search" placeholder="Search"/>
                                </div>
                                <div class="d-flex align-items-center">
                                    <!--begin::Button-->
                                    <a href="{{ route('leave-requests.create') }}" class="btn btn-dark font-weight-bolder">
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
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
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

                            <!--begin: Datatable-->
                            <table class="table table-hover table-checkable" id="leaveRequestTable">
                                <thead class="bg-light text-dark">
                                <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Date</th>
                                        <th class="text-dark">Staff</th>
                                        <th class="text-dark">Division</th>
                                        <th class="text-dark">Leave Type</th>
                                        <th class="text-dark">Start Date</th>
                                        <th class="text-dark">End Date</th>
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
@include('app.leave_request.leave_request_js') 