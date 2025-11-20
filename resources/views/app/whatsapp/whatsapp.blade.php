@extends('app.structure')

@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex align-items-baseline flex-wrap mr-5">
                        <h5 class="text-dark font-weight-bold my-1 mr-5">{{ $data['subtitle'] }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <div class="container">

                <div class="row">
                    <div class="col-lg-12 col-xxl-12">

                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <h3 class="card-title">WhatsApp Connection</h3>
                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <!-- ===================== KIRI (QR CODE) ===================== -->
                                    <div class="col-md-6 text-center">

                                        <h5>Scan Barcode WhatsApp Anda</h5>
                                        <div id="qr_box" class="p-5 border rounded" style="min-height:300px;">
                                            <span class="text-muted">Menunggu QR...</span>
                                        </div>

                                        <button class="btn btn-secondary mt-5" id="wa_action_btn">
                                            Reload Barcode
                                        </button>
                                    </div>

                                    <!-- ===================== KANAN (INFO AKUN) ===================== -->
                                    <div class="col-md-6">


                                        <div id="wa-info-section">
                                            <div class="form-group">
                                                <label>Nama WhatsApp</label>
                                                <input type="text" id="wa-name" class="form-control" disabled>
                                            </div>

                                            <div class="form-group">
                                                <label>Nomor WhatsApp</label>
                                                <input type="text" id="wa-number" class="form-control" disabled>
                                            </div>

                                            <div class="form-group">
                                                <label>Status</label>
                                                <input type="text" id="wa-status-text" class="form-control" disabled>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <!--begin::Card - LIST JOB BROADCAST-->

        <div class="d-flex flex-column-fluid">
            <div class="container">
                <div class="card card-custom gutter-b mt-10">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Daftar Broadcast Job</h3>

                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addJobModal">
                            + Tambah Job
                        </button>
                    </div>

                    <div class="card-body">

                        <table id="waJobTable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Job</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Interval (Jam)</th>
                                <th>Batch Size</th>
                                <th>Status</th>
                                <th style="width: 100px">Aksi</th>
                            </tr>
                            </thead>
                        </table>

                    </div>
                </div>
                <!--end::Card-->
            </div>
        </div>
    </div>
    <!--end::Content-->

    @include('app.whatsapp.whatsapp_modal')
    @include('app._partials.js')
    @include('app.whatsapp.whatsapp_js')

@endsection
