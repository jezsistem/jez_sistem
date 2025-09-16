@extends('app.structure')
@section('title', $data['title'])
@section('content')
<!--begin::Subheader-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-2">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5 fs-3">{{ $data['subtitle'] }}</h5>
                <!--end::Page Title-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->

    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-toolbar">
                                <!-- @if($leaveRequest->lr_status == 'pending')
                                    <a href="{{ route('leave-requests.edit', $leaveRequest->id) }}" class="btn btn-warning">
                                        <i class="ki-outline ki-notepad-edit"></i> Edit
                                    </a>
                                @endif -->
                                <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary">
                                    <i class="ki-outline ki-left"></i> Back
                                </a>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Employee Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name</strong></td>
                                            <td><strong>:</strong></td>
                                            <td>{{ $leaveRequest->user->u_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>NIP:</strong></td>
                                            <td><strong>:</strong></td>
                                            <td>{{ $leaveRequest->user->u_nip }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Division</strong></td>
                                            <td><strong>:</strong></td>
                                            <td>{{ $leaveRequest->ud_name ?: '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5>Leave Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Leave Type:</strong></td>
                                            <td>
                                                <span>
                                                    {{ $leaveRequest->leaveType->lt_name }} ({{ $leaveRequest->leaveType->lt_code }})
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Start Date:</strong></td>
                                            <td>{{ date('d/m/Y', strtotime($leaveRequest->lr_start_date)) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>End Date:</strong></td>
                                            <td>{{ date('d/m/Y', strtotime($leaveRequest->lr_end_date)) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Duration:</strong></td>
                                            <td>
                                                @if($leaveRequest->lr_unit == 'hours')
                                                    {{ $leaveRequest->lr_total_hours }} hours
                                                @else
                                                    {{ $leaveRequest->lr_total_days }} days
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if($leaveRequest->lr_status == 'pending')
                                                    <span class="badge badge-danger">Pending</span>
                                                @elseif($leaveRequest->lr_status == 'approved')
                                                    <span class="badge badge-success">Approved</span>
                                                @elseif($leaveRequest->lr_status == 'rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                @else
                                                    <span class="badge badge-secondary">Cancelled</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-6">
                                    <h5>Reason</h5>
                                    <div class="alert alert-danger">
                                        {{ $leaveRequest->lr_reason }}
                                    </div>
                                </div>
                            </div>
                            
                            @if($leaveRequest->lr_admin_notes)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5>Admin Notes</h5>
                                        <div class="alert alert-warning">
                                            {{ $leaveRequest->lr_admin_notes }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            @if($leaveRequest->approver_name)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5>Approval Information</h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="150"><strong>Approved By:</strong></td>
                                                <td>{{ $leaveRequest->approver_name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Approved At:</strong></td>
                                                <td>{{ $leaveRequest->lr_approved_at ? date('d/m/Y H:i', strtotime($leaveRequest->lr_approved_at)) : '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            
                            @if($dailySchedules->count() > 0)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5>Related Daily Schedules</h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Division</th>
                                                        <th>Shift</th>
                                                        <th>Start Time</th>
                                                        <th>End Time</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($dailySchedules as $schedule)
                                                        <tr>
                                                            <td>{{ date('d/m/Y', strtotime($schedule->ds_date)) }}</td>
                                                            <td>{{ $schedule->ud_name ?: '-' }}</td>
                                                            <td>{{ $schedule->sc_shift_name ?: $schedule->sc_code }}</td>
                                                            <td>{{ $schedule->ds_start_time ?: '-' }}</td>
                                                            <td>{{ $schedule->ds_end_time ?: '-' }}</td>
                                                            <td>
                                                                @if($schedule->ds_status == 'active')
                                                                    <span class="badge badge-success">Active</span>
                                                                @else
                                                                    <span class="badge badge-secondary">Inactive</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end::Entry-->
@endsection 

@include('app._partials.js')
