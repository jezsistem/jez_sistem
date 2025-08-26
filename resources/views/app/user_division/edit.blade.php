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
                                <a href="{{ route('user-divisions.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ki-outline ki-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <form id="userDivisionEditForm" action="{{ route('user-divisions.update', $division->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ud_code">Division Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('ud_code') is-invalid @enderror" 
                                                   id="ud_code" name="ud_code" value="{{ old('ud_code', $division->ud_code) }}" 
                                                   placeholder="Enter division code" required>
                                            @error('ud_code')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ud_name">Division Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('ud_name') is-invalid @enderror" 
                                                   id="ud_name" name="ud_name" value="{{ old('ud_name', $division->ud_name) }}" 
                                                   placeholder="Enter division name" required>
                                            @error('ud_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ud_status">Status <span class="text-danger">*</span></label>
                                            <select class="form-control @error('ud_status') is-invalid @enderror" 
                                                    id="ud_status" name="ud_status" required>
                                                <option value="">Select Status</option>
                                                <option value="active" {{ old('ud_status', $division->ud_status) == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('ud_status', $division->ud_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                            @error('ud_status')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <!-- Empty column for balance -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="ud_description">Description</label>
                                            <textarea class="form-control @error('ud_description') is-invalid @enderror" 
                                                      id="ud_description" name="ud_description" rows="3" 
                                                      placeholder="Enter description">{{ old('ud_description', $division->ud_description) }}</textarea>
                                            @error('ud_description')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <a href="{{ route('user-divisions.index') }}" class="btn btn-dark mr-2">Cancel</a>
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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Handle form submission
    $('#userDivisionEditForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        const udCode = $('#ud_code').val().trim();
        const udName = $('#ud_name').val().trim();
        const udStatus = $('#ud_status').val();
        
        if (!udCode || !udName || !udStatus) {
            showToast('Validation Error', 'Please fill in all required fields', 'error');
            return;
        }
        
        const updateBtn = $('#updateBtn');
        const originalText = updateBtn.text();
        
        // Disable button and show loading
        updateBtn.prop('disabled', true).text('Updating...');
        
        // Get form data
        const formData = {
            ud_code: udCode,
            ud_name: udName,
            ud_description: $('#ud_description').val(),
            ud_status: udStatus,
            _method: 'PUT'
        };
        
        // Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
                showToast('Success', 'User division updated successfully', 'success');
                
                // Redirect after a short delay
                setTimeout(function() {
                    window.location.href = "{{ route('user-divisions.index') }}";
                }, 1500);
            },
            error: function(xhr) {
                let errorMessage = 'Failed to update user division';
                
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    if (errors) {
                        const firstError = Object.values(errors)[0];
                        errorMessage = firstError[0];
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
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