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
                        <form action="{{ route('leave-types.store') }}" method="POST">
                                @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lt_code">Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('lt_code') is-invalid @enderror" 
                                                id="lt_code" name="lt_code" value="{{ old('lt_code') }}" required>
                                            @error('lt_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="lt_name">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('lt_name') is-invalid @enderror" 
                                                id="lt_name" name="lt_name" value="{{ old('lt_name') }}" required>
                                            @error('lt_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="lt_description">Description</label>
                                    <textarea class="form-control @error('lt_description') is-invalid @enderror" 
                                            id="lt_description" name="lt_description" rows="3">{{ old('lt_description') }}</textarea>
                                    @error('lt_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="lt_default_days">Default Days</label>
                                            <input type="number" class="form-control @error('lt_default_days') is-invalid @enderror" 
                                                id="lt_default_days" name="lt_default_days" value="{{ old('lt_default_days', 0) }}" min="0">
                                            @error('lt_default_days')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="lt_default_hours">Default Hours</label>
                                            <input type="number" class="form-control @error('lt_default_hours') is-invalid @enderror" 
                                                id="lt_default_hours" name="lt_default_hours" value="{{ old('lt_default_hours', 0) }}" min="0">
                                            @error('lt_default_hours')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="lt_unit">Unit <span class="text-danger">*</span></label>
                                            <select class="form-control @error('lt_unit') is-invalid @enderror" id="lt_unit" name="lt_unit" required>
                                                <option value="days" {{ old('lt_unit') == 'days' ? 'selected' : '' }}>Days</option>
                                                <option value="hours" {{ old('lt_unit') == 'hours' ? 'selected' : '' }}>Hours</option>
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
                                                    name="lt_requires_approval" value="1" {{ old('lt_requires_approval') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="lt_requires_approval">Requires Approval</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="hidden" name="lt_is_active" value="0">
                                                <input type="checkbox" class="custom-control-input" id="lt_is_active" 
                                                    name="lt_is_active" value="1" {{ old('lt_is_active', true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="lt_is_active">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                

                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <a href="{{ route('leave-types.index') }}" class="btn btn-dark mr-2">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Submit
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
@include('app._partials.js')
