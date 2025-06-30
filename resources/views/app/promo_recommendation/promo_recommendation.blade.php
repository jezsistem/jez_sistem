@extends('app.structure')
@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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

                            <div class="card-header">
                                <div class="card-title">
                                    <div class="d-flex align-items-center">
                                        <!--begin::Search-->
                                        <button type="button" class="btn btn-light-primary font-weight-bolder mr-2"
                                            data-toggle="modal" data-target="#ImportModal" aria-haspopup="true"
                                            aria-expanded="false">
                                            <span class="svg-icon svg-icon-md">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                            fill="#000000" opacity="0.3" />
                                                        <path
                                                            d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                            fill="#000000" />
                                                    </g>
                                                </svg>
                                            </span>Import
                                        </button>

                                        <div class="position-relative mr-3">
                                            <input type="search" class="form-control form-control-sm"
                                                id="threshold_promo_search" placeholder="Cari Threshold Promo"
                                                style="width: 250px;" />
                                        </div>
                                        <!--end::Search-->

                                        <!--begin::Channel Filter-->
                                        <div class="mr-3">
                                            <select class="form-control form-control-sm border"
                                                id="channel_threshold_promo" style="width: 150px;">
                                                <option value="">Pilih Channel</option>
                                                <option value="ONLINE">ONLINE</option>
                                                <option value="OFFLINE">OFFLINE</option>
                                            </select>
                                        </div>
                                        <!--end::Channel Filter-->

                                        <!--begin::Date Range-->
                                        <div>
                                            <a href="#" class="btn btn-sm btn-light-info font-weight-bold"
                                                id="kt_dashboard_daterangepicker" data-toggle="tooltip"
                                                title="Tanggal PO untuk diexport" data-placement="left">
                                                <span class="font-size-sm"
                                                    id="kt_dashboard_daterangepicker_title">All Days</span>
                                                <span class="font-size-sm font-weight-bolder"
                                                    id="kt_dashboard_daterangepicker_date"></span>
                                                <input type="hidden" id="threshold_promo_date_start" />
                                            </a>
                                        </div>
                                        <!--end::Date Range-->
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive">
                                <!--begin: Datatable-->

                                <br />
                                <a class="btn btn-primary ml-auto mr-2" id="export_btn">Export
                                    Excel</a>
                                <table class="table table-hover table-checkable" id="PromoRecommendationtb">
                                    <thead class="bg-light text-dark">
                                        <tr>
                                            <th class="text-dark">No</th>
                                            <th class="text-dark">Code</th>
                                            <th class="text-dark">Channel</th>
                                            <th class="text-dark">Created At</th>
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
    @include('app.promo_recommendation.promo_recommendation_modal')
    @include('app._partials.js')
    @include('app.promo_recommendation.promo_recommendation_js')
@endSection()
