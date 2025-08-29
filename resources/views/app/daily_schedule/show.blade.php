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
                    <h3 class="card-title">Detail Jadwal Harian</h3>
                    <div class="card-tools">
                        <a href="{{ route('daily-schedules.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ki-outline ki-left"></i> Kembali
                        </a>
                        <a href="{{ route('daily-schedules.edit', $schedule->id) }}" class="btn btn-warning btn-sm">
                            <i class="ki-outline ki-notepad-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Nama Karyawan</strong></td>
                                    <td width="5%">:</td>
                                    <td>{{ $schedule->user->u_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NIP</strong></td>
                                    <td>:</td>
                                    <td>{{ $schedule->user->u_nip ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Divisi</strong></td>
                                    <td>:</td>
                                    <td>{{ $schedule->userDivision->ud_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal</strong></td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->ds_date)->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Shift Code</strong></td>
                                    <td width="5%">:</td>
                                    <td><span class="badge badge-primary">{{ $schedule->shiftCode->sc_code ?? 'N/A' }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Shift</strong></td>
                                    <td>:</td>
                                    <td>{{ $schedule->shiftCode->sc_shift_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu Mulai</strong></td>
                                    <td>:</td>
                                    <td>
                                        @if($schedule->ds_start_time)
                                            {{ \Carbon\Carbon::parse($schedule->ds_start_time)->format('H:i') }}
                                        @elseif($schedule->shiftCode && $schedule->shiftCode->sc_start_time)
                                            {{ \Carbon\Carbon::parse($schedule->shiftCode->sc_start_time)->format('H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu Berakhir</strong></td>
                                    <td>:</td>
                                    <td>
                                        @if($schedule->ds_end_time)
                                            {{ \Carbon\Carbon::parse($schedule->ds_end_time)->format('H:i') }}
                                        @elseif($schedule->shiftCode && $schedule->shiftCode->sc_end_time)
                                            {{ \Carbon\Carbon::parse($schedule->shiftCode->sc_end_time)->format('H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge badge-{{ $schedule->ds_status == 'scheduled' ? 'primary' : ($schedule->ds_status == 'completed' ? 'success' : ($schedule->ds_status == 'absent' ? 'danger' : ($schedule->ds_status == 'late' ? 'warning' : 'info'))) }}">
                                            {{ ucfirst($schedule->ds_status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($schedule->ds_notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Catatan</h6>
                                </div>
                                <div class="card-body">
                                    <p>{{ $schedule->ds_notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="card-title">Informasi Shift</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>Kode Shift</strong></td>
                                                    <td width="5%">:</td>
                                                    <td>{{ $schedule->shiftCode->sc_code ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Keterangan</strong></td>
                                                    <td>:</td>
                                                    <td>{{ $schedule->shiftCode->sc_description ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Nama Shift</strong></td>
                                                    <td>:</td>
                                                    <td>{{ $schedule->shiftCode->sc_shift_name ?? 'N/A' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>Jenis Karyawan</strong></td>
                                                    <td width="5%">:</td>
                                                    <td>
                                                        @if($schedule->shiftCode && $schedule->shiftCode->userTypes && $schedule->shiftCode->userTypes->count() > 0)
                                                            @foreach($schedule->shiftCode->userTypes as $userType)
                                                                <span class="badge badge-{{ $userType->ut_name == 'FULL TIME' ? 'primary' : ($userType->ut_name == 'PART FULL' ? 'warning' : ($userType->ut_name == 'PART TIME' ? 'info' : ($userType->ut_name == 'CASUAL' ? 'success' : 'secondary'))) }} mr-1">
                                                                    {{ $userType->ut_name }}
                                                                </span>
                                                            @endforeach
                                                        @else
                                                            <span class="badge badge-secondary">{{ $schedule->shiftCode->sc_type ?? 'N/A' }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Waktu Default Mulai</strong></td>
                                                    <td>:</td>
                                                    <td>
                                                        @if($schedule->shiftCode && $schedule->shiftCode->sc_start_time)
                                                            {{ \Carbon\Carbon::parse($schedule->shiftCode->sc_start_time)->format('H:i') }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Waktu Default Berakhir</strong></td>
                                                    <td>:</td>
                                                    <td>
                                                        @if($schedule->shiftCode && $schedule->shiftCode->sc_end_time)
                                                            {{ \Carbon\Carbon::parse($schedule->shiftCode->sc_end_time)->format('H:i') }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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