@extends('app.structure')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Leave Request</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('leave-requests.update', $leaveRequest->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="leave_type_id">Leave Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('leave_type_id') is-invalid @enderror" id="leave_type_id" name="leave_type_id" required>
                                        <option value="">Select Leave Type</option>
                                        @foreach($leaveTypes as $leaveType)
                                            <option value="{{ $leaveType->id }}" {{ old('leave_type_id', $leaveRequest->leave_type_id) == $leaveType->id ? 'selected' : '' }}>
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
                                        <option value="days" {{ old('lr_unit', $leaveRequest->lr_unit) == 'days' ? 'selected' : '' }}>Days</option>
                                        <option value="hours" {{ old('lr_unit', $leaveRequest->lr_unit) == 'hours' ? 'selected' : '' }}>Hours</option>
                                    </select>
                                    @error('lr_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lr_start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('lr_start_date') is-invalid @enderror" 
                                           id="lr_start_date" name="lr_start_date" 
                                           value="{{ old('lr_start_date', $leaveRequest->lr_start_date ? $leaveRequest->lr_start_date->format('Y-m-d') : '') }}" required>
                                    @error('lr_start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lr_end_date">End Date</label>
                                    <input type="date" class="form-control @error('lr_end_date') is-invalid @enderror" 
                                           id="lr_end_date" name="lr_end_date" 
                                           value="{{ old('lr_end_date', $leaveRequest->lr_end_date ? $leaveRequest->lr_end_date->format('Y-m-d') : '') }}">
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
                                           id="lr_start_time" name="lr_start_time" 
                                           value="{{ old('lr_start_time', $leaveRequest->lr_start_time ? $leaveRequest->lr_start_time->format('H:i') : '') }}">
                                    @error('lr_start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lr_end_time">End Time</label>
                                    <input type="time" class="form-control @error('lr_end_time') is-invalid @enderror" 
                                           id="lr_end_time" name="lr_end_time" 
                                           value="{{ old('lr_end_time', $leaveRequest->lr_end_time ? $leaveRequest->lr_end_time->format('H:i') : '') }}">
                                    @error('lr_end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="lr_reason">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('lr_reason') is-invalid @enderror" 
                                      id="lr_reason" name="lr_reason" rows="4" required>{{ old('lr_reason', $leaveRequest->lr_reason) }}</textarea>
                            @error('lr_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Leave Request
                            </button>
                            <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary">
                                <i class="ki-outline ki-left"></i> Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitSelect = document.getElementById('lr_unit');
    const timeFields = document.getElementById('time_fields');
    
    function toggleTimeFields() {
        if (unitSelect.value === 'hours') {
            timeFields.style.display = 'block';
        } else {
            timeFields.style.display = 'none';
        }
    }
    
    unitSelect.addEventListener('change', toggleTimeFields);
    toggleTimeFields(); // Initial call
});
</script>

@include('app._partials.js')

