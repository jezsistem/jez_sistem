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
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <!--begin::Daterange-->
                <input type="hidden" id="user_rating_date" value=""/>
                <a href="#" class="btn btn-sm btn-light font-weight-bold mr-2 bg-primary" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Select dashboard daterange" data-placement="left">
                    <span class="text-white font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                    <span class="text-white font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                </a>
                <!--end::Daterange-->
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Body-->
    <div class="card-body p-0">
        <!--begin::Stats-->
        <div class="card-spacer" style="padding-top: 0px;">
            <!--begin::Row-->
            <div class="row m-0">
                <div class="col bg-white px-3 py-8 rounded-xl mr-7 mb-7 table-responsive">
                    <input type="search" class="form-control form-control-sm col-6" id="user_search" placeholder="Cari kasir"/><br/>
                    <table class="table table-hover table-checkable" id="Ratingtb">
                        <thead class="bg-primary text-light">
                            <tr>
                                <th class="text-light">No</th>
                                <th class="text-light">Kasir</th>
                                <th class="text-light">Store</th>
                                <th class="text-light">Divisi</th>
                                <th class="text-light">Penilaian</th>
                                <th class="text-light" style="white-space:no-wrap;">Total Rating</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Stats-->
    </div>
    <!--end::Body-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <input type="search" class="form-control form-control-sm col-6" id="user_history_search" placeholder="Cari kasir"/><br/>
                            <table class="table table-hover table-checkable" id="RatingHistorytb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Kasir</th>
                                        <th class="text-light">Store</th>
                                        <th class="text-light">Divisi</th>
                                        <th class="text-light">Invoice</th>
                                        <th class="text-light">Customer</th>
                                        <th class="text-light">Value</th>
                                        <th class="text-light">Reason</th>
                                        <th class="text-light">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <!--end: Datatable-->
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
@include('app.user_rating.user_rating_modal')
@include('app._partials.js')
@include('app.user_rating.user_rating_js')
@endSection()