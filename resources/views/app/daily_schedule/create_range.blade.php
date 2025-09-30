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
                <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5 fs-3">Buat Jadwal (Range Tanggal)</h5>
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

                    <form action="{{ route('daily-schedules.store-range') }}" method="POST">
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
                                    <label for="ds_notes">Catatan</label>
                                    <textarea class="form-control @error('ds_notes') is-invalid @enderror" 
                                              id="ds_notes" name="ds_notes" rows="3">{{ old('ds_notes') }}</textarea>
                                    @error('ds_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start_date">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end_date">Tanggal Akhir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+6 days'))) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- <div class="row"> -->
                            
                            <!-- <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="skip_weekends" name="skip_weekends" value="1" {{ old('skip_weekends') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="skip_weekends">
                                            Lewati Weekend (Sabtu & Minggu)
                                        </label>
                                    </div>
                                </div>
                            </div> -->
                        <!-- </div> -->

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ds_start_time">Jam Mulai (Opsional)</label>
                                    <input type="time" class="form-control @error('ds_start_time') is-invalid @enderror" 
                                           id="ds_start_time" name="ds_start_time" value="{{ old('ds_start_time') }}">
                                    @error('ds_start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kosongkan untuk menggunakan jam dari shift</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ds_end_time">Jam Selesai (Opsional)</label>
                                    <input type="time" class="form-control @error('ds_end_time') is-invalid @enderror" 
                                           id="ds_end_time" name="ds_end_time" value="{{ old('ds_end_time') }}">
                                    @error('ds_end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kosongkan untuk menggunakan jam dari shift</small>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Informasi:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Sistem akan membuat jadwal untuk setiap tanggal dalam range yang dipilih</li>
                                <li>Jika ada jadwal yang sudah ada, tanggal tersebut akan dilewati</li>
                                <li>Opsi "Lewati Weekend" akan melewati hari Sabtu dan Minggu</li>
                                <li>Jam mulai dan selesai bersifat opsional, jika kosong akan menggunakan jam dari shift</li>
                            </ul>
                        </div> -->
                        <div class="card-footer">
                            <div class="row float-right">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary mr-3">
                                        Buat Jadwal
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
    console.log('Daily schedule create range page loaded');
    if (typeof loadStore === 'function') { console.log('loadStore function found, calling...'); loadStore(); } else { console.log('loadStore function not found'); }
    if (typeof clockUpdate === 'function') { console.log('clockUpdate function found, calling...'); clockUpdate(); setInterval(clockUpdate, 1000); } else { console.log('clockUpdate function not found'); }
});
</script>
@endsection 