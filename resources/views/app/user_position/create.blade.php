@extends('app.structure')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $data['title'] }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user-positions.index') }}">User Positions</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Create New Position</h3>
                        </div>
                        <form action="{{ route('user-positions.store') }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="up_code">Position Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('up_code') is-invalid @enderror" 
                                                   id="up_code" name="up_code" value="{{ old('up_code') }}" 
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
                                                   id="up_name" name="up_name" value="{{ old('up_name') }}" 
                                                   placeholder="Enter position name" required>
                                            @error('up_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="up_description">Description</label>
                                    <textarea class="form-control @error('up_description') is-invalid @enderror" 
                                              id="up_description" name="up_description" rows="3" 
                                              placeholder="Enter description">{{ old('up_description') }}</textarea>
                                    @error('up_description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="up_level">Level <span class="text-danger">*</span></label>
                                            <select class="form-control @error('up_level') is-invalid @enderror" 
                                                    id="up_level" name="up_level" required>
                                                <option value="">Select Level</option>
                                                <option value="1" {{ old('up_level') == '1' ? 'selected' : '' }}>1 - Staff</option>
                                                <option value="2" {{ old('up_level') == '2' ? 'selected' : '' }}>2 - Supervisor</option>
                                                <option value="3" {{ old('up_level') == '3' ? 'selected' : '' }}>3 - Manager</option>
                                                <option value="4" {{ old('up_level') == '4' ? 'selected' : '' }}>4 - Director</option>
                                            </select>
                                            @error('up_level')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="up_color">Color <span class="text-danger">*</span></label>
                                            <input type="color" class="form-control @error('up_color') is-invalid @enderror" 
                                                   id="up_color" name="up_color" value="{{ old('up_color', '#3699FF') }}" required>
                                            @error('up_color')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="hidden" name="up_can_approve_leave" value="0">
                                                <input type="checkbox" class="custom-control-input" id="up_can_approve_leave" 
                                                       name="up_can_approve_leave" value="1" {{ old('up_can_approve_leave') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="up_can_approve_leave">
                                                    Can Approve Leave
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="hidden" name="up_is_active" value="0">
                                                <input type="checkbox" class="custom-control-input" id="up_is_active" 
                                                       name="up_is_active" value="1" {{ old('up_is_active', true) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="up_is_active">
                                                    Active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Create Position</button>
                                <a href="{{ route('user-positions.index') }}" class="btn btn-secondary">Cancel</a>
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