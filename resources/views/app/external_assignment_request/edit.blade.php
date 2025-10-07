@extends('app.structure')
@section('content')

<style>
/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
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
</style>
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
                        
                        <div class="row">
                            <div class="col-md-6">
                        <div class="form-group">
                            <label for="lr_reason">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('lr_reason') is-invalid @enderror" 
                                      id="lr_reason" name="lr_reason" rows="4" required>{{ old('lr_reason', $leaveRequest->lr_reason) }}</textarea>
                            @error('lr_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Current Attachments Display -->
                                @if($leaveRequest->attachments && $leaveRequest->attachments->count() > 0)
                        <div class="form-group">
                                    <label>Current Attachments</label>
                                    @foreach($leaveRequest->attachments as $attachment)
                                    <div class="alert alert-info mb-2">
                                <div class="d-flex align-items-center">
                                    @php
                                                $fileType = strtolower($attachment->file_type ?? '');
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
                                                <strong>{{ $attachment->original_name }}</strong><br>
                                        <small class="text-muted">
                                                    @if($attachment->file_size)
                                                        {{ number_format($attachment->file_size / 1024, 2) }} KB
                                            @endif
                                        </small>
                                    </div>
                                    <div class="ml-auto">
                                                <button type="button" class="btn btn-sm btn-info" onclick="viewAttachment({{ $leaveRequest->id }}, '{{ $attachment->file_path }}', '{{ $attachment->original_name }}', '{{ $attachment->file_type }}')">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="removeAttachment({{ $attachment->id }})">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                        </div>
                        @endif
                        
                                <!-- New Attachments Upload -->
                        <div class="form-group">
                                    <label for="lr_attachments">Add More Attachments (Optional)</label>
                                <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('lr_attachments') is-invalid @enderror" 
                                               id="lr_attachments" name="lr_attachments[]" multiple
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                        <label class="custom-file-label" for="lr_attachments">Choose files...</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Supported formats: PDF, JPG, JPEG, PNG, DOC, DOCX (Max: 10MB per file)<br>
                                        <strong>Note:</strong> New files will be added to existing attachments
                                    </small>
                                    <div id="attachment-preview" class="mt-3"></div>
                                    @error('lr_attachments')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
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

<!-- Attachment View Modal -->
<div id="attachmentModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h5 class="modal-title" id="attachmentModalLabel">View Attachment</h5>
            <button type="button" class="close" onclick="hideModal('attachmentModal')" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div id="attachmentContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideModal('attachmentModal')">Close</button>
            <a href="#" id="downloadAttachment" class="btn btn-primary" download>Download</a>
        </div>
    </div>
</div>

@include('app.leave_request.leave_request_js')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const unitSelect = document.getElementById('lr_unit');
    const timeFields = document.getElementById('time_fields');
    const fileInput = document.getElementById('lr_attachments');
    const fileLabel = document.querySelector('.custom-file-label');
    
    function toggleTimeFields() {
        if (unitSelect.value === 'hours') {
            timeFields.style.display = 'block';
        } else {
            timeFields.style.display = 'none';
        }
    }
    
    // Multiple file input handling
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            const previewContainer = document.getElementById('attachment-preview');
            
            // Update label
            if (files.length > 0) {
                fileLabel.textContent = files.length + ' file(s) selected';
            } else {
                fileLabel.textContent = 'Choose files...';
            }
            
            // Clear preview
            if (previewContainer) {
                previewContainer.innerHTML = '';
                
                // Show preview for each file
                files.forEach(function(file, index) {
                // Validate file size (10MB = 10 * 1024 * 1024 bytes)
                const maxSize = 10 * 1024 * 1024;
                if (file.size > maxSize) {
                        alert(`File "${file.name}" exceeds 10MB limit. Please choose a smaller file.`);
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!allowedTypes.includes(file.type)) {
                        alert(`File "${file.name}" type not supported. Please choose PDF, JPG, PNG, DOC, or DOCX file.`);
                    return;
                }
                    
                    const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                    const isImage = file.type.startsWith('image/');
                    
                    const filePreview = document.createElement('div');
                    filePreview.className = 'd-flex align-items-center mb-2 p-2 border rounded';
                    
                    filePreview.innerHTML = 
                        '<div class="mr-3">' +
                            (isImage ? 
                                '<i class="fas fa-image text-primary"></i>' : 
                                '<i class="fas fa-file text-secondary"></i>') +
                        '</div>' +
                        '<div class="flex-grow-1">' +
                            '<div class="font-weight-bold">' + file.name + '</div>' +
                            '<small class="text-muted">' + fileSize + ' MB</small>' +
                        '</div>' +
                        '<button type="button" class="btn btn-sm btn-danger" onclick="removeFile(' + index + ')">' +
                            '<i class="fas fa-times"></i>' +
                        '</button>';
                    
                    previewContainer.appendChild(filePreview);
                });
            }
        });
    }
    
    unitSelect.addEventListener('change', toggleTimeFields);
    toggleTimeFields(); // Initial call
});

// Global function for removing files
window.removeFile = function(index) {
    const fileInput = document.getElementById('lr_attachments');
    if (!fileInput) return;
    
    const dt = new DataTransfer();
    const files = Array.from(fileInput.files);
    
    files.forEach(function(file, i) {
        if (i !== index) {
            dt.items.add(file);
        }
    });
    
    fileInput.files = dt.files;
    
    // Trigger change event to update preview
    const event = new Event('change', { bubbles: true });
    fileInput.dispatchEvent(event);
};

// Global function for removing existing attachments
window.removeAttachment = function(attachmentId) {
    if (confirm('Are you sure you want to remove this attachment?')) {
        // Create a hidden input to mark this attachment for deletion
        const form = document.querySelector('form');
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'remove_attachments[]';
        hiddenInput.value = attachmentId;
        form.appendChild(hiddenInput);
        
        // Remove the attachment from display
        const attachmentElement = document.querySelector(`[onclick*="removeAttachment(${attachmentId})"]`).closest('.alert');
        if (attachmentElement) {
            attachmentElement.remove();
        }
    }
};
</script>

@include('app._partials.js')

