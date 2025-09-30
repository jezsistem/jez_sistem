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
                                <h3 class="card-label">Detail Break Time</h3>
                            </div>
                            <div class="card-toolbar">
                                <a href="{{ route('break-times.index') }}" class="btn btn-secondary btn-sm mr-2">
                                    <i class="ki-outline ki-left"></i> Kembali
                                </a>
                                <a href="{{ route('break-times.edit', $breakTime->id) }}" class="btn btn-warning btn-sm mr-2">
                                    <i class="ki-outline ki-notepad-edit"></i> Edit
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

                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="30%"><strong>ID</strong></td>
                                            <td width="5%">:</td>
                                            <td>{{ $breakTime->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal</strong></td>
                                            <td>:</td>
                                            <td>{{ date('d/m/Y', strtotime($breakTime->bt_date)) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nama Karyawan</strong></td>
                                            <td>:</td>
                                            <td>{{ $breakTime->u_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>NIP</strong></td>
                                            <td>:</td>
                                            <td>{{ $breakTime->u_nip }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Divisi</strong></td>
                                            <td>:</td>
                                            <td>{{ $breakTime->ud_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jam Mulai</strong></td>
                                            <td>:</td>
                                            <td>{{ $breakTime->bt_start_time ? date('H:i', strtotime($breakTime->bt_start_time)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jam Selesai</strong></td>
                                            <td>:</td>
                                            <td>{{ $breakTime->bt_end_time ? date('H:i', strtotime($breakTime->bt_end_time)) : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Durasi</strong></td>
                                            <td>:</td>
                                            <td>
                                                @if($breakTime->bt_duration_minutes)
                                                    @php
                                                        $hours = floor($breakTime->bt_duration_minutes / 60);
                                                        $minutes = $breakTime->bt_duration_minutes % 60;
                                                    @endphp
                                                    {{ sprintf('%02d:%02d', $hours, $minutes) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jenis Break</strong></td>
                                            <td>:</td>
                                            <td>
                                                @if($breakTime->bt_type === 'break_1')
                                                    <span class="badge badge-primary">Break 1</span>
                                                @else
                                                    <span class="badge badge-info">Break 2</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status</strong></td>
                                            <td>:</td>
                                            <td>
                                                @switch($breakTime->bt_status)
                                                    @case('active')
                                                        <span class="badge badge-warning">Active</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge badge-success">Completed</span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge badge-danger">Cancelled</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-secondary">{{ ucfirst($breakTime->bt_status) }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Catatan</strong></td>
                                            <td>:</td>
                                            <td>{{ $breakTime->bt_notes ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dibuat Oleh</strong></td>
                                            <td>:</td>
                                            <td>{{ $breakTime->created_by }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Dibuat</strong></td>
                                            <td>:</td>
                                            <td>{{ date('d/m/Y H:i', strtotime($breakTime->created_at)) }}</td>
                                        </tr>
                                        @if($breakTime->updated_by)
                                        <tr>
                                            <td><strong>Diupdate Oleh</strong></td>
                                            <td>:</td>
                                            <td>{{ $breakTime->updated_by }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Update</strong></td>
                                            <td>:</td>
                                            <td>{{ date('d/m/Y H:i', strtotime($breakTime->updated_at)) }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
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