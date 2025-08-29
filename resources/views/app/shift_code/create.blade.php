@extends('app.structure')

<style>
.checkbox-group {
    border: 1px solid #e4e6ef;
    border-radius: 0.475rem;
    padding: 1rem;
    background-color: #f8f9fa;
}

.custom-control.custom-checkbox {
    margin-bottom: 0.5rem;
}

.custom-control.custom-checkbox:last-child {
    margin-bottom: 0;
}

.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #3699FF;
    border-color: #3699FF;
}

.custom-control-input:checked ~ .custom-control-label::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26 2.974 7.25 8 2.193z'/%3e%3c/svg%3e");
}

#select_all {
    border-color: #3699FF;
}

#select_all:checked ~ .custom-control-label::before {
    background-color: #3699FF;
    border-color: #3699FF;
}
</style>

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
                        <a href="{{ route('shift-codes.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ki-outline ki-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('shift-codes.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="sc_code">Shift Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sc_code') is-invalid @enderror" 
                                           id="sc_code" name="sc_code" value="{{ old('sc_code') }}" 
                                           placeholder="Contoh: FS1, PS2, L" maxlength="10" required>
                                    @error('sc_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="sc_shift_name">Shift Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sc_shift_name') is-invalid @enderror" 
                                           id="sc_shift_name" name="sc_shift_name" value="{{ old('sc_shift_name') }}" 
                                           placeholder="Contoh: Shift 1, Full, Libur" required>
                                    @error('sc_shift_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>User Type <span class="text-danger">*</span></label>
                                    
                                    <!-- Select All Checkbox -->
                                    <div class="mb-3">
                                        <div class="custom-control custom-checkbox" style="background-color: #f8f9fa; padding: 8px; border-radius: 5px; border: 2px solid #232323;">
                                            <input type="checkbox" class="custom-control-input" id="selectAllCheckbox" onchange="toggleAllUserTypes()">
                                            <label class="custom-control-label font-weight-bold text-primary" for="selectAllCheckbox">
                                                Select/Clear All
                                            </label>
                                            <small class="text-muted d-block mt-1">
                                                <span id="selectedCount">0</span> from {{ count($userTypes) }} User Type Selected
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="checkbox-group border p-3 rounded bg-light" style="max-height: 200px; overflow-y: auto;">
                                        @foreach($userTypes as $userType)
                                            <div class="custom-control custom-checkbox mb-2">
                                                <input type="checkbox" 
                                                       class="custom-control-input user-type-checkbox @error('user_type_ids') is-invalid @enderror" 
                                                       id="user_type_{{ $userType->id }}" 
                                                       name="user_type_ids[]" 
                                                       value="{{ $userType->id }}" 
                                                       {{ in_array($userType->id, old('user_type_ids', [])) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="user_type_{{ $userType->id }}">
                                                    {{ $userType->ut_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($errors->has('user_type_ids'))
                                        <div class="invalid-feedback">{{ $errors->first('user_type_ids') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sc_description">Description <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('sc_description') is-invalid @enderror" 
                                           id="sc_description" name="sc_description" value="{{ old('sc_description') }}" 
                                           placeholder="Contoh: Fulltime Shift 1" required>
                                    @error('sc_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sc_start_time">Start Time</label>
                                    <input type="time" class="form-control @error('sc_start_time') is-invalid @enderror" 
                                           id="sc_start_time" name="sc_start_time" value="{{ old('sc_start_time') }}">
                                    @error('sc_start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <!-- <small class="form-text text-muted">Leave empty if not specific time</small> -->
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sc_end_time">End Time</label>
                                    <input type="time" class="form-control @error('sc_end_time') is-invalid @enderror" 
                                           id="sc_end_time" name="sc_end_time" value="{{ old('sc_end_time') }}">
                                    @error('sc_end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <!-- <small class="form-text text-muted">Leave empty if not specific time</small> -->
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row float-right">
                                <div class="col-12">
                                    <a href="{{ route('shift-codes.index') }}" class="btn btn-dark mr-2">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

<script>
// Global function for toggle all functionality
function toggleAllUserTypes() {
    var selectAllCheckbox = document.getElementById('selectAllCheckbox');
    var checkboxes = document.querySelectorAll('.user-type-checkbox');
    
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = selectAllCheckbox.checked;
    });
    
    updateSelectedCount();
    updateSelectAllCheckbox();
}

function updateSelectedCount() {
    var checkedCheckboxes = document.querySelectorAll('.user-type-checkbox:checked');
    var selectedCountSpan = document.getElementById('selectedCount');
    
    if (selectedCountSpan) {
        selectedCountSpan.textContent = checkedCheckboxes.length;
    }
    
    updateSelectAllCheckbox();
}

function updateSelectAllCheckbox() {
    var selectAllCheckbox = document.getElementById('selectAllCheckbox');
    var checkboxes = document.querySelectorAll('.user-type-checkbox');
    var checkedCheckboxes = document.querySelectorAll('.user-type-checkbox:checked');
    
    if (checkboxes.length === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    } else if (checkedCheckboxes.length === checkboxes.length) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    } else if (checkedCheckboxes.length > 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
    } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Set up change listeners for individual checkboxes
    var checkboxes = document.querySelectorAll('.user-type-checkbox');
    
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Initial count update
    updateSelectedCount();
    
    // Auto uppercase for shift code
    var scCodeInput = document.getElementById('sc_code');
    if (scCodeInput) {
        scCodeInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.display = 'none';
        });
    }, 5000);
});
</script>

@endsection 
@include('app._partials.js')