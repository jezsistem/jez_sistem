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
                <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5 fs-3">Detail {{ $data['subtitle'] }}</h5>
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
                        <!-- <a href="{{ route('shift-codes.edit', $shiftCode->id) }}" class="btn btn-warning btn-sm">
                            <i class="ki-outline ki-notepad-edit"></i> Edit
                        </a> -->
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Kode Shift</strong></td>
                                    <td width="5%">:</td>
                                    <td><span class="badge badge-primary">{{ $shiftCode->sc_code }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Keterangan</strong></td>
                                    <td>:</td>
                                    <td>{{ $shiftCode->sc_description }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Shift</strong></td>
                                    <td>:</td>
                                    <td>{{ $shiftCode->sc_shift_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipe Shift</strong></td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge badge-{{ $shiftCode->sc_type == 'ALL' ? 'secondary' : ($shiftCode->sc_type == 'FULL TIME' ? 'primary' : ($shiftCode->sc_type == 'PART FULL' ? 'warning' : ($shiftCode->sc_type == 'PART TIME' ? 'info' : ($shiftCode->sc_type == 'CASUAL' ? 'success' : 'secondary')))) }}">
                                            {{ $shiftCode->sc_type }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Waktu Mulai</strong></td>
                                    <td width="5%">:</td>
                                    <td>
                                        @if($shiftCode->sc_start_time)
                                            {{ \Carbon\Carbon::parse($shiftCode->sc_start_time)->format('H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu Berakhir</strong></td>
                                    <td>:</td>
                                    <td>
                                        @if($shiftCode->sc_end_time)
                                            {{ \Carbon\Carbon::parse($shiftCode->sc_end_time)->format('H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge badge-{{ $shiftCode->sc_status == 'active' ? 'success' : 'danger' }}">
                                            {{ ucfirst($shiftCode->sc_status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Oleh</strong></td>
                                    <td>:</td>
                                    <td>{{ $shiftCode->created_by ?? 'System' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Pada</strong></td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($shiftCode->created_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                                @if($shiftCode->updated_by)
                                <tr>
                                    <td><strong>Diupdate Oleh</strong></td>
                                    <td>:</td>
                                    <td>{{ $shiftCode->updated_by }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Diupdate Pada</strong></td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($shiftCode->updated_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    @if($shiftCode->dailySchedules->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Jadwal yang Menggunakan Shift Code Ini</h5>
                            <div class="table-responsive mt-5">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Staff</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shiftCode->dailySchedules->take(10) as $index => $schedule)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($schedule->ds_date)->format('d/m/Y') }}</td>
                                            <td>{{ $schedule->user->u_name ?? 'N/A' }}</td>
                                            <td>
                                                @if($schedule->ds_status)
                                                    <span class="badge badge-{{ $schedule->ds_status == 'scheduled' ? 'primary' : ($schedule->ds_status == 'completed' ? 'success' : ($schedule->ds_status == 'absent' ? 'danger' : ($schedule->ds_status == 'late' ? 'warning' : 'info'))) }}">
                                                        {{ ucfirst($schedule->ds_status) }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">Not Set</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($shiftCode->dailySchedules->count() > 10)
                                <p class="text-muted">Menampilkan 10 dari {{ $shiftCode->dailySchedules->count() }} jadwal</p>
                            @endif
                        </div>
                    </div>
                    @endif
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
@include('app._partials.js')