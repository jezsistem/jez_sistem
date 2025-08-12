@extends('app.structure')
@section('title', $data['title'])
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
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
                <div class="card-body">
                    <form action="{{ route('leave-requests.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="leave_type_id">Leave Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('leave_type_id') is-invalid @enderror" id="leave_type_id" name="leave_type_id" required>
                                        <option value="">Select Leave Type</option>
                                        @foreach($leaveTypes as $leaveType)
                                            <option value="{{ $leaveType->id }}" {{ old('leave_type_id') == $leaveType->id ? 'selected' : '' }}>
                                                {{ $leaveType->lt_name }} ({{ $leaveType->lt_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('leave_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lr_unit">Unit <span class="text-danger">*</span></label>
                                    <select class="form-control @error('lr_unit') is-invalid @enderror" id="lr_unit" name="lr_unit" required>
                                        <option value="days" {{ old('lr_unit') == 'days' ? 'selected' : '' }}>Days</option>
                                        <option value="hours" {{ old('lr_unit') == 'hours' ? 'selected' : '' }}>Hours</option>
                                    </select>
                                    @error('lr_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Leave Balance Information -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-primary">
                                    <h6><i class="ki-outline ki-information-3"></i> Leave Balance Information</h6>
                                    <div id="leaveBalanceInfo">
                                        <p>Select a leave type to see your balance information.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lr_start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('lr_start_date') is-invalid @enderror" 
                                           id="lr_start_date" name="lr_start_date" value="{{ old('lr_start_date') }}" required>
                                    @error('lr_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lr_end_date">End Date</label>
                                    <input type="date" class="form-control @error('lr_end_date') is-invalid @enderror" 
                                           id="lr_end_date" name="lr_end_date" value="{{ old('lr_end_date') }}">
                                    @error('lr_end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="time_fields" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lr_start_time">Start Time</label>
                                    <input type="time" class="form-control @error('lr_start_time') is-invalid @enderror" 
                                           id="lr_start_time" name="lr_start_time" value="{{ old('lr_start_time') }}">
                                    @error('lr_start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lr_end_time">End Time</label>
                                    <input type="time" class="form-control @error('lr_end_time') is-invalid @enderror" 
                                           id="lr_end_time" name="lr_end_time" value="{{ old('lr_end_time') }}">
                                    @error('lr_end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="lr_reason">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('lr_reason') is-invalid @enderror" 
                                      id="lr_reason" name="lr_reason" rows="4" required>{{ old('lr_reason') }}</textarea>
                            @error('lr_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group float-right">
                            <button type="submit" class="btn btn-primary mr-3">
                                Submit Leave Request
                            </button>
                            <a href="{{ route('leave-requests.index') }}" class="btn btn-dark">
                                Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    </div></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitSelect = document.getElementById('lr_unit');
    const timeFields = document.getElementById('time_fields');
    const leaveTypeSelect = document.getElementById('leave_type_id');
    const leaveBalanceInfo = document.getElementById('leaveBalanceInfo');
    
    function toggleTimeFields() {
        if (unitSelect.value === 'hours') {
            timeFields.style.display = 'block';
        } else {
            timeFields.style.display = 'none';
        }
    }
    
    function loadLeaveBalance() {
        const leaveTypeId = leaveTypeSelect.value;
        if (!leaveTypeId) {
            leaveBalanceInfo.innerHTML = '<p>Select a leave type to see your balance information.</p>';
            return;
        }
        
        // Show loading
        leaveBalanceInfo.innerHTML = '<p><i class="fas fa-spinner fa-spin"></i> Loading balance information...</p>';
        
        // Fetch leave balance from server
        fetch(`/leave-requests/balance/${leaveTypeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    leaveBalanceInfo.innerHTML = `
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Initial Balance:</strong> ${data.initial_balance} days
                            </div>
                            <div class="col-md-4">
                                <strong>Used:</strong> ${data.used_balance} days
                            </div>
                            <div class="col-md-4">
                                <strong>Remaining:</strong> ${data.remaining_balance} days
                            </div>
                        </div>
                    `;
                } else {
                    leaveBalanceInfo.innerHTML = '<p class="text-warning">No balance information available for this leave type.</p>';
                }
            })
            .catch(error => {
                console.error('Error loading leave balance:', error);
                leaveBalanceInfo.innerHTML = '<p class="text-danger">Error loading balance information.</p>';
            });
    }
    
    unitSelect.addEventListener('change', toggleTimeFields);
    leaveTypeSelect.addEventListener('change', loadLeaveBalance);
    
    toggleTimeFields(); // Initial call
    loadLeaveBalance(); // Initial call if leave type is pre-selected
});
</script>
@endsection 