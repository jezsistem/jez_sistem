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
                <a href="#" class="btn btn-date-info font-weight-bold mr-2" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Select dashboard daterange" data-placement="left">
                    <span class="font-size-base" id="kt_dashboard_daterangepicker_title">Today</span>
                    <span class="font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
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
                    <input type="search" class="form-control  col-6" id="user_search" placeholder="Cari kasir"/><br/>
                    <table class="table table-hover table-checkable" id="Ratingtb">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="text-dark">No</th>
                                <th class="text-dark">Kasir</th>
                                <th class="text-dark">Store</th>
                                <th class="text-dark">Divisi</th>
                                <th class="text-dark">Penilaian</th>
                                <th class="text-dark" style="white-space:no-wrap;">Total Rating</th>
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
                            <input type="search" class="form-control  col-6" id="user_history_search" placeholder="Cari kasir"/><br/>
                            <table class="table table-hover table-checkable" id="RatingHistorytb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Kasir</th>
                                        <th class="text-dark">Store</th>
                                        <th class="text-dark">Divisi</th>
                                        <th class="text-dark">Invoice</th>
                                        <th class="text-dark">Customer</th>
                                        <th class="text-dark">Value</th>
                                        <th class="text-dark">Reason</th>
                                        <th class="text-dark">Tanggal</th>
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