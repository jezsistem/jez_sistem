@extends('app.structure')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-2">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5 fs-3">{{ $data['title'] }}</h5>
                <!--end::Page Title-->
            </div>
            <!--end::Info-->
        </div>
    </div>
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
                    <form id="userDivisionForm" action="{{ route('user-divisions.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ud_code">Division Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('ud_code') is-invalid @enderror" 
                                                id="ud_code" name="ud_code" value="{{ old('ud_code') }}" 
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
                                                id="ud_name" name="ud_name" value="{{ old('ud_name') }}" 
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
                                            <option value="active" {{ old('ud_status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('ud_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('ud_status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="ud_status">Leader <span class="text-danger">*</span></label>
                                        <select class="form-control @error('ud_status') is-invalid @enderror"
                                                id="lead_id" name="lead_id" required>
                                            <option value="">Select Leader</option>
                                            @foreach($data['leader'] as $leader)
                                                <option value="{{ $leader->user_id }}" {{ old('lead_id') == $leader->user_id ? 'selected' : '' }}>
                                                    {{ $leader->u_name }}
                                                </option>
                                            @endforeach
                                            </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="manager_id">Manager <span class="text-danger">*</span></label>
                                        <select class="form-control @error('ud_status') is-invalid @enderror"
                                                id="manager_id" name="manager_id" required>
                                            <option value="">Select Manager</option>
                                            @foreach($data['manager'] as $manager)
                                                <option value="{{ $manager->user_id }}" {{ old('manager_id') == $manager->user_id ? 'selected' : '' }}>
                                                    {{ $manager->u_name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="ud_description">Description</label>
                                        <textarea class="form-control @error('ud_description') is-invalid @enderror" 
                                                    id="ud_description" name="ud_description" rows="3" 
                                                    placeholder="Enter description">{{ old('ud_description') }}</textarea>
                                        @error('ud_description')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer w-full d-flex justify-content-end">
                            <a href="{{ route('user-divisions.index') }}" class="btn btn-dark mr-2">Cancel</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
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
    $('#userDivisionForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        const udCode = $('#ud_code').val().trim();
        const udName = $('#ud_name').val().trim();
        const leadID = $('#lead_id').val().trim();
        const managerID = $('#manager_id').val().trim();
        const udStatus = $('#ud_status').val();
        
        if (!udCode || !udName || !udStatus) {
            showToast('Validation Error', 'Please fill in all required fields', 'error');
            return;
        }
        
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.text();
        
        // Disable button and show loading
        submitBtn.prop('disabled', true).text('Creating...');
        
        // Get form data
        const formData = {
            ud_code: udCode,
            ud_name: udName,
            lead_id: leadID,
            manager_id: managerID,
            ud_description: $('#ud_description').val(),
            ud_status: udStatus
        };
        
        // Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
                showToast('Success', 'User division created successfully', 'success');
                
                // Redirect after a short delay
                setTimeout(function() {
                    window.location.href = "{{ route('user-divisions.index') }}";
                }, 1500);
            },
            error: function(xhr) {
                let errorMessage = 'Failed to create user division';
                
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
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
@include('app._partials.js')
