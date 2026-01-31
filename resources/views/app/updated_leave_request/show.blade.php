@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Leave Request Detail</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('leave-requests_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
            @if($leaveRequest->lr_status == 'pending' && $leaveRequest->user_id == auth()->user()->id)
                @php
                    $canUpdate = function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'update');
                @endphp
                @if($canUpdate)
                <button onclick="editLeaveRequestFromDetail({{ $leaveRequest->id }})" class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700">
                    <i class="fas fa-edit mr-2"></i> Edit
                </button>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Employee Information Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Employee Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="text-sm font-medium text-gray-700">Name:</label>
            <p class="mt-1 text-sm text-gray-900">{{ $leaveRequest->user->u_name }}</p>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">NIP:</label>
            <p class="mt-1 text-sm text-gray-900">{{ $leaveRequest->user->u_nip }}</p>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700">Division:</label>
            <p class="mt-1 text-sm text-gray-900">{{ $leaveRequest->user->division->ud_name ?? '-' }}</p>
        </div>
    </div>
</div>

<!-- Leave Information Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Leave Information</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3">
            <div>
                <label class="text-sm font-medium text-gray-700">Leave Type:</label>
                <p class="mt-1 text-sm text-gray-900">{{ $leaveRequest->leaveType->lt_name }} ({{ $leaveRequest->leaveType->lt_code }})</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Start Date:</label>
                <p class="mt-1 text-sm text-gray-900">{{ date('d/m/Y', strtotime($leaveRequest->lr_start_date)) }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">End Date:</label>
                <p class="mt-1 text-sm text-gray-900">{{ date('d/m/Y', strtotime($leaveRequest->lr_end_date)) }}</p>
            </div>
            @if($leaveRequest->lr_start_time)
            <div>
                <label class="text-sm font-medium text-gray-700">Start Time:</label>
                <p class="mt-1 text-sm text-gray-900">{{ date('H:i', strtotime($leaveRequest->lr_start_time)) }}</p>
            </div>
            @endif
            @if($leaveRequest->lr_end_time)
            <div>
                <label class="text-sm font-medium text-gray-700">End Time:</label>
                <p class="mt-1 text-sm text-gray-900">{{ date('H:i', strtotime($leaveRequest->lr_end_time)) }}</p>
            </div>
            @endif
        </div>
        <div class="space-y-3">
            <div>
                <label class="text-sm font-medium text-gray-700">Duration:</label>
                <p class="mt-1 text-sm text-gray-900">
                    @if($leaveRequest->lr_unit == 'hours')
                        {{ $leaveRequest->lr_total_hours }} hours
                    @else
                        {{ $leaveRequest->lr_total_days }} days
                    @endif
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Status:</label>
                <p class="mt-1">
                    @if($leaveRequest->lr_status == 'pending')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @elseif($leaveRequest->lr_status == 'approved')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                    @elseif($leaveRequest->lr_status == 'rejected')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Cancelled</span>
                    @endif
                </p>
            </div>
            @if($leaveRequest->approver)
            <div>
                <label class="text-sm font-medium text-gray-700">Approved By:</label>
                <p class="mt-1 text-sm text-gray-900">{{ $leaveRequest->approver->u_name }}</p>
            </div>
            @endif
            @if($leaveRequest->lr_approved_at)
            <div>
                <label class="text-sm font-medium text-gray-700">Approved At:</label>
                <p class="mt-1 text-sm text-gray-900">{{ date('d/m/Y H:i', strtotime($leaveRequest->lr_approved_at)) }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reason Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Reason</h3>
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <p class="text-sm text-gray-900">{{ $leaveRequest->lr_reason }}</p>
    </div>
</div>

<!-- Admin Notes Card -->
@if($leaveRequest->lr_admin_notes)
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Notes</h3>
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <p class="text-sm text-gray-900">{{ $leaveRequest->lr_admin_notes }}</p>
    </div>
</div>
@endif

<!-- Attachments Card -->
@if($leaveRequest->attachments && $leaveRequest->attachments->count() > 0)
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Attachments</h3>
    <div class="space-y-2">
        @foreach($leaveRequest->attachments as $attachment)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center gap-3">
                    @if(strpos($attachment->file_type, 'pdf') !== false)
                        <i class="fas fa-file-pdf text-red-500 text-xl"></i>
                    @elseif(strpos($attachment->file_type, 'image') !== false)
                        <i class="fas fa-file-image text-blue-600 text-xl"></i>
                    @elseif(strpos($attachment->file_type, 'word') !== false)
                        <i class="fas fa-file-word text-blue-600 text-xl"></i>
                    @else
                        <i class="fas fa-file text-gray-600 text-xl"></i>
                    @endif
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $attachment->original_name }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($attachment->file_size / 1024, 2) }} KB</p>
                    </div>
                </div>
                <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="px-3 py-1 text-sm font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200">
                    <i class="fas fa-download mr-1"></i> Download
                </a>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Related Daily Schedules Card -->
@if($dailySchedules && $dailySchedules->count() > 0)
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Daily Schedules</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-3 py-3">Date</th>
                    <th scope="col" class="px-3 py-3">Division</th>
                    <th scope="col" class="px-3 py-3">Shift</th>
                    <th scope="col" class="px-3 py-3">Start Time</th>
                    <th scope="col" class="px-3 py-3">End Time</th>
                    <th scope="col" class="px-3 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dailySchedules as $schedule)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-3 py-4">{{ date('d/m/Y', strtotime($schedule->ds_date)) }}</td>
                        <td class="px-3 py-4">{{ $schedule->ud_name ?: '-' }}</td>
                        <td class="px-3 py-4">{{ $schedule->sc_shift_name ?: $schedule->sc_code }}</td>
                        <td class="px-3 py-4">{{ $schedule->ds_start_time ?: '-' }}</td>
                        <td class="px-3 py-4">{{ $schedule->ds_end_time ?: '-' }}</td>
                        <td class="px-3 py-4">
                            @if($schedule->ds_status == 'active')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@push('scripts')
    <script>
    function editLeaveRequestFromDetail(id) {
        window.location.href = "{{ url('leave-requests_v2') }}?edit=" + id;
    }
    
    // Check if edit parameter exists
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const editId = urlParams.get('edit');
        if (editId) {
            // Trigger edit modal
            setTimeout(function() {
                if (typeof editLeaveRequest === 'function') {
                    editLeaveRequest(editId);
                } else {
                    // Redirect to index with edit parameter
                    window.location.href = "{{ url('leave-requests_v2') }}?edit=" + editId;
                }
            }, 100);
        }
    });
    </script>
@endpush
@endsection
