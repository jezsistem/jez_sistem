@extends('app.structure')
@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column bg-primary" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-2 bg-primary" style="border-radius:10px;">
                <!--begin::Page Title-->
                <h5 class="text-white font-weight-bold mt-2 mb-2 mr-5 ml-5">{{ $data['user']->u_name }}</h5>
                <!--end::Page Title-->
            </div>
            <!--end::Info-->
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <!--begin::Daterange-->
                <input type="hidden" id="dashboard_date" value=""/>
                <input type="hidden" id="st_id" value=""/>
                <a href="#" class="btn btn-sm btn-light font-weight-bold mr-2 bg-success" data-toggle="modal" data-target="#ChangePasswordModal">
                    <span class="font-size-base">Ganti Password</span>
                </a>
                <a href="{{ url('logout') }}" class="btn btn-sm btn-light font-weight-bold mr-2 bg-danger" data-toggle="tooltip" data-placement="left">
                    <span class="font-size-base">Logout</span>
                </a>
                <!--end::Daterange-->
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Dashboard-->
            <!--begin::Row-->
            <div class="row">
                <div class="col-lg-12 col-xxl-12" id="user_activity_reload">
                    <!--begin::List Widget 9-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header align-items-center border-0 mt-4">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="data_stok_btn">Data stok</span>
                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                            </h3>
                            <h3 class="card-title align-items-start flex-column">
                                <span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="out_btn">Keluar Rak</span>
                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                            </h3>
{{--                            <h3 class="card-title align-items-start flex-column">--}}
{{--                                <span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="scan_out_btn">Scan Keluar Rak</span>--}}
{{--                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->--}}
{{--                            </h3>--}}
                            <h3 class="card-title align-items-start flex-column">
                                <span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="in_btn">Masuk Rak</span>
                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                            </h3>
                            <h3 class="card-title align-items-start flex-column">
                                <span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="scan_in_btn">Scan Masuk Rak</span>
                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                            </h3>
                            <h3 class="card-title align-items-start flex-column">
                                <span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="invoice_take_btn">Scan Ambil</span>
                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                            </h3>
                            <h3 class="card-title align-items-start flex-column">
                                <span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="invoice_pack_btn">Scan Packing</span>
                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                            </h3>
                            <h3 class="card-title align-items-start flex-column">
                                <span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="take_cross_order_btn">Ambil Cross Order</span>
                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                            </h3>
                        </div>
                        <!--end::Header-->
                    </div>
                    <!--end: List Widget 9-->
                </div>
                <div class="col-lg-12 col-xxl-12" id="user_activity_reload">
                    <!--begin::List Widget 9-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header align-items-center border-0 mt-4">
                            <h3 class="card-title align-items-start flex-column">
                                <a href="{{ url('cross_order') }}">
                                    <span class="font-weight-bolder font-size-h3 btn bg-dark text-white">Cross Order</span>
                                    <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                                </a>
                            </h3>
                            <h3 class="card-title align-items-start flex-column">
                                <a href="{{ url('setup_lokasi_stok_v2') }}"><span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="mutation_btn">Mutasi</span></a>
                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                            </h3>
                            <h3 class="card-title align-items-start flex-column">
                                <a href="{{ url('setup_lokasi_stok_v2') }}"><span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="article_btn">Data Artikel</span></a>
                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                            </h3>
                            <h3 class="card-title align-items-start flex-column">
                                <span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="transfer_btn">Ambil Item Transfer</span>
                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                            </h3>
                            <h3 class="card-title align-items-start flex-column">
                                <span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="transfer_invoice_btn">Check Transfer Invoice</span>
                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                            </h3>
                        </div>
                        <!--end::Header-->
                    </div>
                    <!--end: List Widget 9-->
                </div>

                <div class="col-lg-12 col-xxl-12" id="user_activity_reload">
                    <!--begin::List Widget 9-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header align-items-center border-0 mt-4">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="font-weight-bolder font-size-h3 btn bg-dark text-white"  id="scan_transfer_btn">Scan Ambil Item Transfer</span>
                                <!-- <span class="text-muted mt-3 font-weight-bold font-size-sm">112 Aktifitas terbaru</span> -->
                            </h3>
                        </div>
                        <!--end::Header-->
                    </div>
                    <!--end: List Widget 9-->
                </div>
                <!-- INCOMING STOCK -->
            </div>
            <!--end::Row-->
            <!--end::Dashboard-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@include('app.dashboard.helper.dashboard_modal')
@include('app._partials.js')
@include('app.dashboard.helper.dashboard_js')
@endSection()
