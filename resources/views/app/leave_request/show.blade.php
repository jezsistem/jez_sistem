@extends('app.structure')
@section('title', $data['title'])
@section('content')
    @include('app.leave_request.show_css')
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
                    <div class="col-8">
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

                    <div class="col-md-4">
                        <div class="card" style="border:1px solid #ddd;">
                            <div class="card-header">
                                <h5 class="mb-0">Comments</h5>
                            </div>

                            <div class="card-body" id="commentList" style="max-height:300px; overflow:auto;">

                                @forelse ($comments as $c)
                                    <div class="mb-3 p-2" style="border-bottom:1px solid #eee;">
                                        <strong>{{ $c->user->u_name }}</strong>
                                        <div style="font-size:12px; color:#888;">
                                            {{ date('d/m/Y H:i', strtotime($c->created_at)) }}
                                        </div>
                                        <div class="mt-2">{!! nl2br(e($c->comment)) !!}</div>
                                    </div>
                                @empty
                                    <div class="text-muted no-comment">Belum ada komentar.</div>
                                @endforelse

                            </div>

                            <div class="card-footer">
                                <textarea id="commentInput" class="form-control" rows="3" placeholder="Tulis komentar..."></textarea>
                                <button id="btnAddComment" class="btn btn-primary btn-sm mt-2" style="width:100%;">Tambah Komentar</button>
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

    <script>
        document.getElementById("btnAddComment").addEventListener("click", function () {
            let comment = document.getElementById("commentInput").value.trim();
            if (!comment) return;

            fetch("{{ route('comments.store', $leaveRequest->id) }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ comment: comment, identifier: 'leave-requests', key_id: {{ $leaveRequest->id }} })
            })
                .then(res => res.json())
                .then(res => {

                    if (res.status === "success") {

                        // hapus jika ada text kosong
                        let emptyText = document.querySelector(".no-comment");
                        if (emptyText) emptyText.remove();

                        // append komentar baru
                        document.getElementById("commentList").insertAdjacentHTML('afterbegin', `
                <div class="mb-3 p-2" style="border-bottom:1px solid #eee;">
                    <strong>${res.data.name}</strong>
                    <div style="font-size:12px; color:#888;">${res.data.datetime}</div>
                    <div class="mt-2">${res.data.comment}</div>
                </div>
            `);

                        document.getElementById("commentInput").value = "";
                    }
                });
        });
    </script>
@endsection



@include('app._partials.js')
@include('app.leave_request.leave_request_js')
