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

                    <form action="{{ route('daily-schedules.bulk-store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_ids">Select Staff <span class="text-danger">*</span></label>
                                    <select class="form-control @error('user_ids') is-invalid @enderror" 
                                            id="user_ids" name="user_ids[]" multiple required 
                                            style="min-height: 120px; max-height: 200px; overflow-y: auto;">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->u_name }} ({{ $user->u_nip }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Gunakan Ctrl/Cmd + klik untuk memilih multiple staff</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sc_id">Shift Code <span class="text-danger">*</span></label>
                                    <select class="form-control @error('sc_id') is-invalid @enderror" 
                                            id="sc_id" name="sc_id" required>
                                        <option value="">Pilih Shift Code</option>
                                        @foreach($shiftCodes as $shiftCode)
                                            <option value="{{ $shiftCode->id }}" {{ old('sc_id') == $shiftCode->id ? 'selected' : '' }}>
                                                {{ $shiftCode->sc_code }} - {{ $shiftCode->sc_description }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sc_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Tanggal Akhir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+7 days'))) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ds_start_time">Waktu Mulai</label>
                                    <input type="time" class="form-control @error('ds_start_time') is-invalid @enderror" 
                                           id="ds_start_time" name="ds_start_time" value="{{ old('ds_start_time') }}">
                                    @error('ds_start_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ds_end_time">Waktu Selesai</label>
                                    <input type="time" class="form-control @error('ds_end_time') is-invalid @enderror" 
                                           id="ds_end_time" name="ds_end_time" value="{{ old('ds_end_time') }}">
                                    @error('ds_end_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                         
                        <!-- <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Informasi Bulk Create</h6>
                                    <ul class="mb-0">
                                        <li>Sistem akan membuat jadwal untuk setiap karyawan yang dipilih</li>
                                        <li>Jadwal akan dibuat untuk setiap hari dalam rentang tanggal yang ditentukan</li>
                                        <li>Jika jadwal sudah ada untuk tanggal tertentu, sistem akan melewatinya</li>
                                        <li>Pastikan rentang tanggal tidak terlalu besar untuk menghindari timeout</li>
                                    </ul>
                                </div>
                            </div>
                        </div> -->
                        <div class="card-footer float-right">
                        <div class="row">
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
/* Ensure Select2 is visible */
.select2-container {
    display: block !important;
    width: 100% !important;
}

.select2-container--default .select2-selection--multiple {
    border: 1px solid #E4E6EF;
    border-radius: 6px;
    min-height: 38px;
}

.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #3699FF;
    box-shadow: 0 0 0 0.2rem rgba(54, 153, 255, 0.25);
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: #3699FF;
    border: 1px solid #3699FF;
    color: white;
    border-radius: 4px;
    padding: 2px 8px;
    margin: 2px;
}

.select2-dropdown {
    border: 1px solid #E4E6EF;
    border-radius: 6px;
    box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075);
}
</style>

<script>
$(document).ready(function() {
    // Auto hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Initialize Select2 for user_ids
    setTimeout(function() {
        if ($('#user_ids').length) {
            $('#user_ids').select2({
                placeholder: 'Pilih Staff...',
                allowClear: true,
                width: '100%'
            });
        }
    }, 100);

    // Validate date range
    $('#end_date').on('change', function() {
        var startDate = new Date($('#start_date').val());
        var endDate = new Date($(this).val());
        
        if (endDate < startDate) {
            alert('Tanggal akhir tidak boleh lebih kecil dari tanggal mulai');
            $(this).val($('#start_date').val());
        }
        
        // Calculate days difference
        var diffTime = Math.abs(endDate - startDate);
        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        if (diffDays > 31) {
            alert('Rentang tanggal tidak boleh lebih dari 31 hari');
            var newEndDate = new Date(startDate);
            newEndDate.setDate(startDate.getDate() + 30);
            $(this).val(newEndDate.toISOString().split('T')[0]);
        }
    });

    $('#start_date').on('change', function() {
        var startDate = new Date($(this).val());
        var endDate = new Date($('#end_date').val());
        
        if (endDate < startDate) {
            $('#end_date').val($(this).val());
        }
    });
});
</script>

@endsection 