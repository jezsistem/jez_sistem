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
                    <h3 class="card-title">Edit Jadwal Harian</h3>
                    <div class="card-tools">
                        <a href="{{ route('daily-schedules.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ki-outline ki-left"></i> Kembali
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

                    <form action="{{ route('daily-schedules.update', $schedule->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">Karyawan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('user_id') is-invalid @enderror" 
                                            id="user_id" name="user_id" required>
                                        <option value="">Pilih Karyawan</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', $schedule->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->u_name }} ({{ $user->u_nip }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ud_id">Divisi</label>
                                    <select class="form-control @error('ud_id') is-invalid @enderror" 
                                            id="ud_id" name="ud_id">
                                        <option value="">Pilih Divisi</option>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}" {{ old('ud_id', $schedule->ud_id) == $division->id ? 'selected' : '' }}>
                                                {{ $division->ud_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ud_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sc_id">Shift Code <span class="text-danger">*</span></label>
                                    <select class="form-control @error('sc_id') is-invalid @enderror" 
                                            id="sc_id" name="sc_id" required>
                                        <option value="">Pilih Shift Code</option>
                                        @foreach($shiftCodes as $shiftCode)
                                            <option value="{{ $shiftCode->id }}" {{ old('sc_id', $schedule->sc_id) == $shiftCode->id ? 'selected' : '' }}>
                                                {{ $shiftCode->sc_code }} - {{ $shiftCode->sc_description }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sc_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ds_date">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('ds_date') is-invalid @enderror" 
                                           id="ds_date" name="ds_date" value="{{ old('ds_date', $schedule->ds_date ? $schedule->ds_date->format('Y-m-d') : '') }}" required>
                                    @error('ds_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ds_start_time">Waktu Mulai (Opsional)</label>
                                    <input type="time" class="form-control @error('ds_start_time') is-invalid @enderror" 
                                           id="ds_start_time" name="ds_start_time" 
                                           value="{{ old('ds_start_time', $schedule->ds_start_time ? \Carbon\Carbon::parse($schedule->ds_start_time)->format('H:i') : '') }}">
                                    @error('ds_start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kosongkan untuk menggunakan waktu default dari shift code</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ds_end_time">Waktu Berakhir (Opsional)</label>
                                    <input type="time" class="form-control @error('ds_end_time') is-invalid @enderror" 
                                           id="ds_end_time" name="ds_end_time" 
                                           value="{{ old('ds_end_time', $schedule->ds_end_time ? \Carbon\Carbon::parse($schedule->ds_end_time)->format('H:i') : '') }}">
                                    @error('ds_end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kosongkan untuk menggunakan waktu default dari shift code</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ds_notes">Catatan</label>
                                    <textarea class="form-control @error('ds_notes') is-invalid @enderror" 
                                              id="ds_notes" name="ds_notes" rows="3" 
                                              placeholder="Catatan tambahan untuk jadwal ini">{{ old('ds_notes', $schedule->ds_notes) }}</textarea>
                                    @error('ds_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <a href="{{ route('daily-schedules.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
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
});
</script>
@endsection 