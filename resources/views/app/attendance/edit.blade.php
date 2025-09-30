@extends('app.structure')

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
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title">Edit Absensi</h3>
                    <div class="card-tools">
                        <a href="{{ route('attendance.index') }}" class="btn btn-secondary btn-sm">
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

                    <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">Karyawan <span class="text-danger">*</span></label>
                                    <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                        <option value="">Pilih Karyawan</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ (old('user_id', $attendance->user_id) == $user->id) ? 'selected' : '' }}>
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
                                    <label for="at_date">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('at_date') is-invalid @enderror" 
                                           id="at_date" name="at_date" value="{{ old('at_date', $attendance->at_date) }}" required>
                                    @error('at_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="at_time_in">Jam Masuk</label>
                                    <input type="time" class="form-control @error('at_time_in') is-invalid @enderror" 
                                           id="at_time_in" name="at_time_in" 
                                           value="{{ old('at_time_in', $attendance->at_time_in ? \Carbon\Carbon::parse($attendance->at_time_in)->format('H:i') : '') }}">
                                    @error('at_time_in')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="at_time_out">Jam Keluar</label>
                                    <input type="time" class="form-control @error('at_time_out') is-invalid @enderror" 
                                           id="at_time_out" name="at_time_out" 
                                           value="{{ old('at_time_out', $attendance->at_time_out ? \Carbon\Carbon::parse($attendance->at_time_out)->format('H:i') : '') }}">
                                    @error('at_time_out')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="at_status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('at_status') is-invalid @enderror" id="at_status" name="at_status" required>
                                        <option value="">Pilih Status</option>
                                        <option value="present" {{ old('at_status', $attendance->at_status) == 'present' ? 'selected' : '' }}>Hadir</option>
                                        <option value="late" {{ old('at_status', $attendance->at_status) == 'late' ? 'selected' : '' }}>Terlambat</option>
                                        <option value="absent" {{ old('at_status', $attendance->at_status) == 'absent' ? 'selected' : '' }}>Tidak Hadir</option>
                                        <option value="early_leave" {{ old('at_status', $attendance->at_status) == 'early_leave' ? 'selected' : '' }}>Pulang Awal</option>
                                        <option value="scan_once" {{ old('at_status', $attendance->at_status) == 'scan_once' ? 'selected' : '' }}>Scan 1 Kali</option>
                                    </select>
                                    @error('at_status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="at_notes">Catatan</label>
                                    <textarea class="form-control @error('at_notes') is-invalid @enderror" 
                                              id="at_notes" name="at_notes" rows="3">{{ old('at_notes', $attendance->at_notes) }}</textarea>
                                    @error('at_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('attendance.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
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

<style>
/* Fix untuk sidebar scroll */
#kt_aside { overflow-y: auto !important; height: 100vh !important; }
#kt_aside_menu { overflow-y: auto !important; max-height: calc(100vh - 100px) !important; }
.aside-menu-wrapper { overflow-y: auto !important; height: calc(100vh - 100px) !important; }
.aside { overflow-y: auto !important; }
.aside-menu { overflow-y: auto !important; }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Reinitialize perfect scrollbar jika ada
    if (typeof PerfectScrollbar !== 'undefined') {
        const asideMenu = document.querySelector('#kt_aside_menu');
        if (asideMenu) {
            new PerfectScrollbar(asideMenu, { wheelPropagation: false });
        }
    }
    // Debug and call header functions
    console.log('Attendance edit page loaded');
    if (typeof loadStore === 'function') { console.log('loadStore function found, calling...'); loadStore(); } else { console.log('loadStore function not found'); }
    if (typeof clockUpdate === 'function') { console.log('clockUpdate function found, calling...'); clockUpdate(); setInterval(clockUpdate, 1000); } else { console.log('clockUpdate function not found'); }
});
</script>
@endsection 