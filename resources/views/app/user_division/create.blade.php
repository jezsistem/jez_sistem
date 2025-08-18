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
                    <form action="{{ route('user-divisions.store') }}" method="POST">
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
                                <label for="ud_description">Description</label>
                                <textarea class="form-control @error('ud_description') is-invalid @enderror" 
                                            id="ud_description" name="ud_description" rows="3" 
                                            placeholder="Enter description">{{ old('ud_description') }}</textarea>
                                @error('ud_description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                                </div>
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
                            </div>
                        </div>
                        <div class="card-footer w-full d-flex justify-content-end">
                            <a href="{{ route('user-divisions.index') }}" class="btn btn-dark mr-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
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
$(document).ready(function() {
    loadStore();
    clockUpdate();
});
</script>
@include('app._partials.js')
