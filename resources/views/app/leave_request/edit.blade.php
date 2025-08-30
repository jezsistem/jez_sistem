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
                    <form action="{{ route('leave-requests.update', $leaveRequest->id) }}" method="POST" enctype="multipart/form-data">
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
                        
                        <!-- Current Attachment Display -->
                        @if($leaveRequest->lr_attachment_path)
                        <div class="form-group">
                            <label>Current Attachment</label>
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    @php
                                        $fileType = strtolower($leaveRequest->lr_attachment_type ?? '');
                                        if (strpos($fileType, 'pdf') !== false) {
                                            $fileIcon = '<i class="fas fa-file-pdf text-danger fa-2x mr-3"></i>';
                                        } elseif (strpos($fileType, 'image') !== false) {
                                            $fileIcon = '<i class="fas fa-file-image text-primary fa-2x mr-3"></i>';
                                        } elseif (strpos($fileType, 'word') !== false) {
                                            $fileIcon = '<i class="fas fa-file-word text-info fa-2x mr-3"></i>';
                                        } else {
                                            $fileIcon = '<i class="fas fa-file text-secondary fa-2x mr-3"></i>';
                                        }
                                    @endphp
                                    {!! $fileIcon !!}
                                    <div>
                                        <strong>{{ $leaveRequest->lr_attachment_name }}</strong><br>
                                        <small class="text-muted">
                                            @if($leaveRequest->lr_attachment_size)
                                                {{ number_format($leaveRequest->lr_attachment_size / 1024, 2) }} KB
                                            @endif
                                        </small>
                                    </div>
                                    <div class="ml-auto">
                                        <button type="button" class="btn btn-sm btn-info" onclick="viewAttachment({{ $leaveRequest->id }}, '{{ $leaveRequest->lr_attachment_path }}', '{{ $leaveRequest->lr_attachment_name }}', '{{ $leaveRequest->lr_attachment_type }}')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- New Attachment Upload -->
                        <div class="form-group">
                            <label for="lr_attachment">Update Attachment (Optional)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('lr_attachment') is-invalid @enderror" 
                                           id="lr_attachment" name="lr_attachment" 
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <label class="custom-file-label" for="lr_attachment">Choose new file (leave empty to keep current)</label>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Supported formats: PDF, JPG, JPEG, PNG, DOC, DOCX (Max: 10MB)<br>
                                <strong>Note:</strong> If you upload a new file, it will replace the current attachment
                            </small>
                            @error('lr_attachment')
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
    const fileInput = document.getElementById('lr_attachment');
    const fileLabel = document.querySelector('.custom-file-label');
    
    function toggleTimeFields() {
        if (unitSelect.value === 'hours') {
            timeFields.style.display = 'block';
        } else {
            timeFields.style.display = 'none';
        }
    }
    
    // File input handling
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Update label with filename
                fileLabel.textContent = file.name;
                
                // Validate file size (10MB = 10 * 1024 * 1024 bytes)
                const maxSize = 10 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File size exceeds 10MB limit. Please choose a smaller file.');
                    this.value = '';
                    fileLabel.textContent = 'Choose new file (leave empty to keep current)';
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(file.type)) {
                    alert('File type not supported. Please choose PDF, JPG, PNG, DOC, or DOCX file.');
                    this.value = '';
                    fileLabel.textContent = 'Choose new file (leave empty to keep current)';
                    return;
                }
            } else {
                fileLabel.textContent = 'Choose new file (leave empty to keep current)';
            }
        });
    }
    
    unitSelect.addEventListener('change', toggleTimeFields);
    toggleTimeFields(); // Initial call
});
</script>

@include('app._partials.js')

