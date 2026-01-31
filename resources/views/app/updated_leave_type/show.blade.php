@extends('layouts.app_v2')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl font-semibold text-gray-900">Leave Type Detail</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('leave-types_v2') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
            @php
                $canUpdate = function_exists('hasAccess') && hasAccess(auth()->user()->up_id ?? 0, 'update');
            @endphp
            @if($canUpdate)
            <button onclick="editLeaveTypeFromDetail({{ $leaveType->id }})" class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700">
                <i class="fas fa-edit mr-2"></i> Edit
            </button>
            @endif
        </div>
    </div>
</div>

<!-- Detail Card -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-700">Code:</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $leaveType->lt_code }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Name:</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $leaveType->lt_name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Description:</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $leaveType->lt_description ?: '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Default Days:</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $leaveType->lt_default_days ?: '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Default Hours:</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $leaveType->lt_default_hours ?: '-' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Unit:</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($leaveType->lt_unit) }}</p>
                </div>
            </div>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Settings</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-700">Requires Approval:</label>
                    <p class="mt-1">
                        @if($leaveType->lt_requires_approval)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Yes</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">No</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Status:</label>
                    <p class="mt-1">
                        @if($leaveType->lt_is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
    function editLeaveTypeFromDetail(id) {
        window.location.href = "{{ url('leave-types_v2') }}?edit=" + id;
    }
    
    // Check if edit parameter exists
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const editId = urlParams.get('edit');
        if (editId) {
            // Trigger edit modal
            setTimeout(function() {
                if (typeof editLeaveType === 'function') {
                    editLeaveType(editId);
                } else {
                    // Redirect to index with edit parameter
                    window.location.href = "{{ url('leave-types_v2') }}?edit=" + editId;
                }
            }, 100);
        }
    });
    </script>
@endpush
@endsection
