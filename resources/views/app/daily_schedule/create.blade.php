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
                        <a href="{{ route('daily-schedules.index') }}" class="btn btn-secondary btn-sm">
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

                    <form action="{{ route('daily-schedules.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">Staff <span class="text-danger">*</span></label>
                                    <select class="form-control" id="user_id" name="user_id" required>
                                        <option value="">Select Staff</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->u_nip }} - {{ $user->u_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sc_id">Shift <span class="text-danger">*</span></label>
                                    <select class="form-control" id="sc_id" name="sc_id" required>
                                        <option value="">Select Shift</option>
                                        @foreach($shiftCodes as $shift)
                                            <option value="{{ $shift->id }}" {{ old('sc_id') == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->sc_code }} - {{ $shift->sc_shift_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ds_date">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('ds_date') is-invalid @enderror" 
                                           id="ds_date" name="ds_date" value="{{ old('ds_date', date('Y-m-d')) }}" required>
                                    @error('ds_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ds_start_time">Waktu Mulai (Opsional)</label>
                                    <input type="time" class="form-control @error('ds_start_time') is-invalid @enderror" 
                                           id="ds_start_time" name="ds_start_time" value="{{ old('ds_start_time') }}">
                                    @error('ds_start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kosongkan untuk menggunakan waktu default dari shift code</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ds_end_time">Waktu Berakhir (Opsional)</label>
                                    <input type="time" class="form-control @error('ds_end_time') is-invalid @enderror" 
                                           id="ds_end_time" name="ds_end_time" value="{{ old('ds_end_time') }}">
                                    @error('ds_end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kosongkan untuk menggunakan waktu default dari shift code</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ds_notes">Catatan</label>
                                    <textarea class="form-control @error('ds_notes') is-invalid @enderror" 
                                              id="ds_notes" name="ds_notes" rows="3" 
                                              placeholder="Catatan tambahan untuk jadwal ini">{{ old('ds_notes') }}</textarea>
                                    @error('ds_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row float-right">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-3">
                                        Simpan
                                    </button>
                                    <a href="{{ route('daily-schedules.index') }}" class="btn btn-dark">
                                        Batal
                                    </a>
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

    // Auto populate start and end time when shift code is selected
    $('#sc_id').on('change', function() {
        var shiftCodeId = $(this).val();
        if (shiftCodeId) {
            // You can add AJAX call here to get shift code details
            // For now, we'll just clear the time fields
            $('#ds_start_time').val('');
            $('#ds_end_time').val('');
        }
    });
});
</script>
@endsection 