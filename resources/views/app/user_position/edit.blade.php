@extends('app.structure')

@section('content')
<div class="content-wrapper">
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

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-tools">
                                <!-- <a href="{{ route('user-positions.edit', $position->id) }}" class="btn btn-warning btn-sm">
                                    <i class="ki-outline ki-notepad-edit"></i> Edit
                                </a> -->
                                <a href="{{ route('user-positions.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ki-outline ki-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <form id="userPositionEditForm" action="{{ route('user-positions.update', $position->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="up_code">Position Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('up_code') is-invalid @enderror" 
                                                   id="up_code" name="up_code" value="{{ old('up_code', $position->up_code) }}" 
                                                   placeholder="Enter position code" required>
                                            @error('up_code')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="up_name">Position Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('up_name') is-invalid @enderror" 
                                                   id="up_name" name="up_name" value="{{ old('up_name', $position->up_name) }}" 
                                                   placeholder="Enter position name" required>
                                            @error('up_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="up_description">Description</label>
                                            <textarea class="form-control @error('up_description') is-invalid @enderror" 
                                                    id="up_description" name="up_description" rows="3" 
                                                    placeholder="Enter description">{{ old('up_description', $position->up_description) }}</textarea>
                                            @error('up_description')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="up_level">Level <span class="text-danger">*</span></label>
                                            <select class="form-control @error('up_level') is-invalid @enderror" 
                                                    id="up_level" name="up_level" required>
                                                <option value="">Select Level</option>
                                                <option value="1" {{ old('up_level', $position->up_level) == '1' ? 'selected' : '' }}>1 - Staff</option>
                                                <option value="2" {{ old('up_level', $position->up_level) == '2' ? 'selected' : '' }}>2 - Supervisor</option>
                                                <option value="3" {{ old('up_level', $position->up_level) == '3' ? 'selected' : '' }}>3 - Manager</option>
                                                <option value="4" {{ old('up_level', $position->up_level) == '4' ? 'selected' : '' }}>4 - Director</option>
                                            </select>
                                            @error('up_level')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="up_level">Level <span class="text-danger">*</span></label>
                                            <select class="form-control @error('up_level') is-invalid @enderror" 
                                                    id="up_level" name="up_level" required>
                                                <option value="">Select Level</option>
                                                <option value="1" {{ old('up_level', $position->up_level) == '1' ? 'selected' : '' }}>1 - Staff</option>
                                                <option value="2" {{ old('up_level', $position->up_level) == '2' ? 'selected' : '' }}>2 - Supervisor</option>
                                                <option value="3" {{ old('up_level', $position->up_level) == '3' ? 'selected' : '' }}>3 - Manager</option>
                                                <option value="4" {{ old('up_level', $position->up_level) == '4' ? 'selected' : '' }}>4 - Director</option>
                                            </select>
                                            @error('up_level')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> -->
                                    <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="up_color">Color <span class="text-danger">*</span></label>
                                            <input type="color" class="form-control @error('up_color') is-invalid @enderror" 
                                                   id="up_color" name="up_color" value="{{ old('up_color', $position->up_color) }}" required>
                                            @error('up_color')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div> -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="up_can_approve_leave" 
                                                       name="up_can_approve_leave" {{ old('up_can_approve_leave', $position->up_can_approve_leave) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="up_can_approve_leave">
                                                    Can Approve Leave
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="up_is_active" 
                                                       name="up_is_active" {{ old('up_is_active', $position->up_is_active) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="up_is_active">
                                                    Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer w-full d-flex justify-content-end">
                                <a href="{{ route('user-positions.index') }}" class="btn btn-dark mr-2">Cancel</a>
                                <button type="submit" class="btn btn-primary" id="updateBtn">Update</button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .content-wrapper {
        overflow-y: auto;
    }
</style>

<script>
// Custom toast function
function showToast(title, message, type) {
    showSimpleToast(title, message, type);
}

// Simple custom toast function
function showSimpleToast(title, message, type) {
    const toastHtml = `
        <div style="position: fixed; top: 20px; right: 20px; z-index: 9999; 
                    background: ${type === 'success' ? '#1BC5BD' : '#dc3545'}; 
                    color: white; padding: 15px 20px; border-radius: 2px; 
                    box-shadow: 0 4px 8px rgba(0,0,0,0.2); max-width: 300px;" 
         id="customToast">
            <strong>${title}</strong><br>
            ${message}
        </div>
    `;
    
    $('body').append(toastHtml);
    
    // Auto remove after 3 seconds
    setTimeout(function() {
        $('#customToast').fadeOut(function() {
            $(this).remove();
        });
    }, 3000);
}

$(document).ready(function() {
    loadStore();
    clockUpdate();
    
    // Set up CSRF token
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || 
                      $('input[name="_token"]').val() || 
                      "{{ csrf_token() }}";
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });
    
    // Handle form submission
    $('#userPositionEditForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        const upCode = $('#up_code').val().trim();
        const upName = $('#up_name').val().trim();
        const upLevel = $('#up_level').val();
        
        if (!upCode || !upName || !upLevel) {
            showToast('Validation Error', 'Please fill in all required fields', 'error');
            return;
        }
        
        const updateBtn = $('#updateBtn');
        const originalText = updateBtn.text();
        
        // Disable button and show loading
        updateBtn.prop('disabled', true).text('Updating...');
        
        // Get form data
        const formData = {
            up_code: upCode,
            up_name: upName,
            up_description: $('#up_description').val(),
            up_level: upLevel,
            up_can_approve_leave: $('#up_can_approve_leave').is(':checked') ? 'on' : '',
            up_is_active: $('#up_is_active').is(':checked') ? 'on' : '',
            _method: 'PUT',
            _token: csrfToken
        };
        
        // Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
                showToast('Success', 'User position updated successfully', 'success');
                
                // Redirect after a short delay
                setTimeout(function() {
                    window.location.href = "{{ route('user-positions.index') }}";
                }, 1500);
            },
            error: function(xhr) {
                console.log('Error Response:', xhr);
                console.log('Status:', xhr.status);
                console.log('Response Text:', xhr.responseText);
                
                let errorMessage = 'Failed to update user position';
                
                if (xhr.status === 419) {
                    // CSRF token mismatch - try regular form submission
                    errorMessage = 'CSRF token issue. Trying regular form submission...';
                    showToast('Warning', errorMessage, 'error');
                    
                    // Submit form traditionally after short delay
                    setTimeout(function() {
                        $('#userPositionEditForm')[0].submit();
                    }, 1000);
                    return;
                } else if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        const firstError = Object.values(errors)[0];
                        errorMessage = firstError[0];
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 500) {
                    errorMessage = 'Internal server error. Please contact administrator.';
                }
                
                showToast('Error', errorMessage, 'error');
            },
            complete: function() {
                // Re-enable button
                updateBtn.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
@endsection 
@include('app._partials.js')
