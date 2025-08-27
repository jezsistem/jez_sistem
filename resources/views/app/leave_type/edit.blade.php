@extends('app.structure')
@section('title', $data['title'])
@section('content')


<!--begin::Content-->
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
        <!--begin::Container-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-tools">
                                <a href="{{ route('leave-types.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ki-outline ki-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <form id="leaveTypeEditForm" action="{{ route('leave-types.update', $leaveType->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lt_code">Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('lt_code') is-invalid @enderror" 
                                                id="lt_code" name="lt_code" value="{{ old('lt_code', $leaveType->lt_code) }}" required>
                                            @error('lt_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lt_name">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('lt_name') is-invalid @enderror" 
                                                id="lt_name" name="lt_name" value="{{ old('lt_name', $leaveType->lt_name) }}" required>
                                            @error('lt_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="lt_description">Description</label>
                                    <textarea class="form-control @error('lt_description') is-invalid @enderror" 
                                            id="lt_description" name="lt_description" rows="3">{{ old('lt_description', $leaveType->lt_description) }}</textarea>
                                    @error('lt_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="lt_default_days">Default Days</label>
                                            <input type="number" class="form-control @error('lt_default_days') is-invalid @enderror" 
                                                id="lt_default_days" name="lt_default_days" value="{{ old('lt_default_days', $leaveType->lt_default_days) }}" min="0">
                                            @error('lt_default_days')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="lt_default_hours">Default Hours</label>
                                            <input type="number" class="form-control @error('lt_default_hours') is-invalid @enderror" 
                                                id="lt_default_hours" name="lt_default_hours" value="{{ old('lt_default_hours', $leaveType->lt_default_hours) }}" min="0">
                                            @error('lt_default_hours')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="lt_unit">Unit <span class="text-danger">*</span></label>
                                            <select class="form-control @error('lt_unit') is-invalid @enderror" id="lt_unit" name="lt_unit" required>
                                                <option value="days" {{ old('lt_unit', $leaveType->lt_unit) == 'days' ? 'selected' : '' }}>Days</option>
                                                <option value="hours" {{ old('lt_unit', $leaveType->lt_unit) == 'hours' ? 'selected' : '' }}>Hours</option>
                                            </select>
                                            @error('lt_unit')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="hidden" name="lt_requires_approval" value="0">
                                                <input type="checkbox" class="custom-control-input" id="lt_requires_approval" 
                                                    name="lt_requires_approval" value="1" {{ old('lt_requires_approval', $leaveType->lt_requires_approval) == 1 ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="lt_requires_approval">Requires Approval</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="hidden" name="lt_is_active" value="0">
                                                <input type="checkbox" class="custom-control-input" id="lt_is_active" 
                                                    name="lt_is_active" value="1" {{ old('lt_is_active', $leaveType->lt_is_active) == 1 ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="lt_is_active">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    
                                    <!-- <div class="row"> -->
                                        <!-- <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lt_color">Color <span class="text-danger">*</span></label>
                                                <input type="color" class="form-control @error('lt_color') is-invalid @enderror" 
                                                    id="lt_color" name="lt_color" value="{{ old('lt_color', $leaveType->lt_color) }}" required>
                                                @error('lt_color')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div> -->
                                        
                                    <!-- </div> -->
                                    
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <a href="{{ route('leave-types.index') }}" class="btn btn-dark mr-2">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary" id="updateBtn">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!--end::Container-->
            </div>
        </div>
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@endsection 

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
    console.log('Leave Type Edit - Document Ready');
    
    // Check if jQuery is available
    if (typeof $ === 'undefined') {
        console.error('jQuery is not available!');
        return;
    }
    
    console.log('jQuery version:', $.fn.jquery);
    
    loadStore();
    clockUpdate();
    
    // Set up CSRF token
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || 
                      $('input[name="_token"]').val() || 
                      "{{ csrf_token() }}";
    
    console.log('CSRF Token found:', csrfToken);
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });
    
    // Handle form submission
    $('#leaveTypeEditForm').on('submit', function(e) {
        console.log('Form submission triggered');
        e.preventDefault();
        
        // Validate required fields
        const ltCode = $('#lt_code').val().trim();
        const ltName = $('#lt_name').val().trim();
        const ltUnit = $('#lt_unit').val();
        
        console.log('Form values:', { ltCode, ltName, ltUnit });
        
        if (!ltCode || !ltName || !ltUnit) {
            console.log('Validation failed, showing toast');
            showToast('Validation Error', 'Please fill in all required fields', 'error');
            return;
        }
        
        const updateBtn = $('#updateBtn');
        const originalText = updateBtn.text();
        
        // Disable button and show loading
        updateBtn.prop('disabled', true).text('Updating...');
        
        // Get form data
        const formData = {
            lt_code: ltCode,
            lt_name: ltName,
            lt_description: $('#lt_description').val(),
            lt_default_days: $('#lt_default_days').val() || 0,
            lt_default_hours: $('#lt_default_hours').val() || 0,
            lt_unit: ltUnit,
            lt_requires_approval: $('#lt_requires_approval').is(':checked') ? 1 : 0,
            lt_is_active: $('#lt_is_active').is(':checked') ? 1 : 0,
            _method: 'PUT',
            _token: csrfToken
        };
        
        // Debug logging
        console.log('Form Data:', formData);
        console.log('CSRF Token:', csrfToken);
        
        // Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
                showToast('Success', 'Leave type updated successfully', 'success');
                
                // Redirect after a short delay
                setTimeout(function() {
                    window.location.href = "{{ route('leave-types.index') }}";
                }, 1500);
            },
            error: function(xhr) {
                console.log('Error Response:', xhr);
                console.log('Status:', xhr.status);
                console.log('Response Text:', xhr.responseText);
                
                let errorMessage = 'Failed to update leave type';
                
                if (xhr.status === 419) {
                    // CSRF token mismatch - try regular form submission
                    errorMessage = 'CSRF token issue. Trying regular form submission...';
                    showToast('Warning', errorMessage, 'error');
                    
                    // Submit form traditionally after short delay
                    setTimeout(function() {
                        $('#leaveTypeEditForm')[0].submit();
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

@include('app._partials.js')
