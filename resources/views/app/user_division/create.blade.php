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
                        <li class="breadcrumb-item"><a href="{{ route('user-divisions.index') }}">User Divisions</a></li>
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
                            <h3 class="card-title">Create New Division</h3>
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
                                
                                <div class="form-group">
                                    <label for="ud_description">Description</label>
                                    <textarea class="form-control @error('ud_description') is-invalid @enderror" 
                                              id="ud_description" name="ud_description" rows="3" 
                                              placeholder="Enter description">{{ old('ud_description') }}</textarea>
                                    @error('ud_description')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>

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
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Create Division</button>
                                <a href="{{ route('user-divisions.index') }}" class="btn btn-secondary">Cancel</a>
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