@extends('app.structure')
@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
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
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3">
                            <div class="card-title">
                                <h3 class="card-label">Edit Backup Time</h3>
                            </div>
                            <div class="card-toolbar">
                                <a href="{{ route('break-times-backup.index') }}" class="btn btn-secondary btn-sm mr-2">
                                    <i class="ki-outline ki-left"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    {{ session('error') }}
                                </div>
                            @endif

                            <form action="{{ route('break-times-backup.update', $breakTime->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_id">Staff <span class="text-danger">*</span></label>
                                            <select class="form-control" id="user_id" name="user_id" required>
                                                <option value="">Pilih Staff</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}" {{ $breakTime->user_id == $user->id ? 'selected' : '' }}>
                                                        {{ $user->u_name }} ({{ $user->u_nip }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bt_date">Tanggal <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="bt_date" name="bt_date" 
                                                   value="{{ $breakTime->bt_date }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bt_start_time">Jam Mulai <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="bt_start_time" name="bt_start_time" 
                                                   value="{{ $breakTime->bt_start_time }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bt_end_time">Jam Selesai <span class="text-danger">*</span></label>
                                            <input type="time" class="form-control" id="bt_end_time" name="bt_end_time" 
                                                   value="{{ $breakTime->bt_end_time }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bt_type">Jenis Backup</label>
                                            <input type="text" class="form-control" id="bt_type" name="bt_type" 
                                                   value="{{ $breakTime->bt_type }}" 
                                                   placeholder="backup_1, backup_2, backup_3, etc."
                                                   readonly>
                                            <small class="text-muted">Backup type ditentukan otomatis oleh sistem</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bt_status">Status <span class="text-danger">*</span></label>
                                            <select class="form-control" id="bt_status" name="bt_status" required>
                                                <option value="active" {{ $breakTime->bt_status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="completed" {{ $breakTime->bt_status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                <option value="cancelled" {{ $breakTime->bt_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="bt_notes">Catatan</label>
                                    <textarea class="form-control" id="bt_notes" name="bt_notes" rows="3">{{ $breakTime->bt_notes }}</textarea>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Backup Time
                                    </button>
                                    <a href="{{ route('break-times-backup.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@endsection

@include('app._partials.js') 