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
                        <form action="{{ route('user-positions.update', $position->id) }}" method="POST">
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
                                <button type="submit" class="btn btn-primary">Update</button>

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
$(document).ready(function() {
    loadStore();
    clockUpdate();
});
</script>
@endsection 
@include('app._partials.js')
