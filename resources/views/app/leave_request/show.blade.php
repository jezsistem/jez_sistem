@extends('app.structure')
@section('title', $data['title'])
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
                                <div class="card-toolbar d-flex justify-content-between align-items-center w-100">
                                    <!-- @if ($leaveRequest->lr_status == 'pending')
    <a href="{{ route('leave-requests.edit', $leaveRequest->id) }}" class="btn btn-warning">
                                            <i class="ki-outline ki-notepad-edit"></i> Edit
                                        </a>
    @endif -->
                                    <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary">
                                        <i class="ki-outline ki-left"></i> Back
                                    </a>

                                    <div>
                                        @if ($leaveRequest->lr_status == 'pending')
                                            <button type="button"
                                                style="background-color: #10b981; color: white; padding: 0.5rem 1rem; border-radius: 0.25rem; border: none; cursor: pointer;"
                                                onmouseover="this.style.backgroundColor='#059669'"
                                                onmouseout="this.style.backgroundColor='#10b981'"
                                                onclick="showApprovalModal({{ $leaveRequest->id }}, 'approve')">
                                                Approve
                                            </button>

                                            <button type="button"
                                                style="background-color: #ef4444; color: white; padding: 0.5rem 1rem; border-radius: 0.25rem; border: none; cursor: pointer;"
                                                onmouseover="this.style.backgroundColor='#dc2626'"
                                                onmouseout="this.style.backgroundColor='#ef4444'"
                                                onclick="showApprovalModal({{ $leaveRequest->id }}, 'reject')">
                                                Reject
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Employee Information</h5>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="150"><strong>Name:</strong></td>
                                                <td>{{ $leaveRequest->user->u_name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>NIP:</strong></td>
                                                <td>{{ $leaveRequest->user->u_nip }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Division:</strong></td>
                                                <td>{{ $leaveRequest->user->division->ud_name ?? '-' }}</td>
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
                                                        {{ $leaveRequest->leaveType->lt_name }}
                                                        ({{ $leaveRequest->leaveType->lt_code }})
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
                                                    @if ($leaveRequest->lr_unit == 'hours')
                                                        {{ $leaveRequest->lr_total_hours }} hours
                                                    @else
                                                        {{ $leaveRequest->lr_total_days }} days
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    @if ($leaveRequest->lr_status == 'pending')
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

                                @if ($leaveRequest->lr_admin_notes)
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <h5>Admin Notes</h5>
                                            <div class="alert alert-warning">
                                                {{ $leaveRequest->lr_admin_notes }}
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($leaveRequest->approver_name)
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
                                                    <td>{{ $leaveRequest->lr_approved_at ? date('d/m/Y H:i', strtotime($leaveRequest->lr_approved_at)) : '-' }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                @if ($dailySchedules->count() > 0)
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
                                                        @foreach ($dailySchedules as $schedule)
                                                            <tr>
                                                                <td>{{ date('d/m/Y', strtotime($schedule->ds_date)) }}</td>
                                                                <td>{{ $schedule->ud_name ?: '-' }}</td>
                                                                <td>{{ $schedule->sc_shift_name ?: $schedule->sc_code }}
                                                                </td>
                                                                <td>{{ $schedule->ds_start_time ?: '-' }}</td>
                                                                <td>{{ $schedule->ds_end_time ?: '-' }}</td>
                                                                <td>
                                                                    @if ($schedule->ds_status == 'active')
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

    <!-- Approval Modal -->
    <div id="approvalModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalModalLabel">Process Leave Request</h5>
                <button type="button" class="close" onclick="hideModal('approvalModal')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="approvalForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="approval_notes" id="notes_label">Notes (Optional)</label>
                        <textarea class="form-control" id="approval_notes" name="notes" rows="3"
                            placeholder="Enter approval/rejection notes..."></textarea>
                        <small class="text-muted" id="notes_help">Notes are required for rejection</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="hideModal('approvalModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="approvalSubmitBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <!--end::Entry-->
@endsection

@include('app._partials.js')
@include('app.leave_request.leave_request_js')
