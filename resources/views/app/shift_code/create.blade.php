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
                                    <label for="sc_code">Kode Shift <span class="text-danger">*</span></label>
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
                                    <label for="sc_shift_name">Nama Shift <span class="text-danger">*</span></label>
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
                                    <label for="sc_type">Tipe Shift <span class="text-danger">*</span></label>
                                    <select class="form-control @error('sc_type') is-invalid @enderror" 
                                            id="sc_type" name="sc_type" required>
                                        <option value="">Pilih Tipe Shift</option>
                                        @foreach($shiftTypes as $type)
                                            <option value="{{ $type }}" {{ old('sc_type') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sc_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sc_description">Keterangan <span class="text-danger">*</span></label>
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
                                    <label for="sc_start_time">Jam Mulai</label>
                                    <input type="time" class="form-control @error('sc_start_time') is-invalid @enderror" 
                                           id="sc_start_time" name="sc_start_time" value="{{ old('sc_start_time') }}">
                                    @error('sc_start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kosongkan jika tidak ada waktu spesifik</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sc_end_time">Jam Berakhir</label>
                                    <input type="time" class="form-control @error('sc_end_time') is-invalid @enderror" 
                                           id="sc_end_time" name="sc_end_time" value="{{ old('sc_end_time') }}">
                                    @error('sc_end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kosongkan jika tidak ada waktu spesifik</small>
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
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Auto uppercase for shift code
    $('#sc_code').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
});
</script>
@endsection 
@include('app._partials.js')