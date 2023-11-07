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
            
            <div class="form-group" style="padding-top:22px;">
                <select class="form-control bg-primary text-white" id="st_id_filter" name="st_id_filter" required>
                    <option value="">- Pilih Store -</option>
                    @foreach ($data['st_id'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <div class="form-group mb-1 pb-1">
                                <div id="start"></div>
                            </div>
                            <!--end: Datatable-->
                            <div class="row col-12">
                            <input type="search" class="form-control form-control-sm col-6" id="article_search" placeholder="Cari artikel / brand / warna"/>
                            <input type="text" class="form-control form-control-sm col-3 ml-auto" id="total_start_article" readonly/>
                            </div>
                            <table class="table table-hover table-checkable pr-4" id="StartBintb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Artikel</th>
                                        <th class="text-light">Stok</th>
                                        <th class="text-light">Mutasi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                            <a class="btn btn-sm btn-success" style="float:right; font-weight:bold;" id="mutation_btn">Mutasi</a>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
                <div class="col-lg-6">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <div class="form-group mb-1 pb-1">
                                <div id="end"></div>
                            </div>
                            <!--end: Datatable-->
                            <div class="row col-12">
                            <input type="search" class="form-control form-control-sm col-6" id="article_end_search" placeholder="Cari artikel / brand / warna"/>
                            <input type="text" class="form-control form-control-sm col-3 ml-auto" id="total_end_article" readonly/>
                            </div>
                            <table class="table table-hover table-checkable pr-4" id="EndBintb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Artikel</th>
                                        <th class="text-light">Stok</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <!--begin::Header-->
                        <div class="card-header h-auto">
                            <!--begin::Title-->
                            <div class="card-title py-5">
                                <h3 class="card-label">History Setup</h3>
                            </div>
                            <!--end::Title-->
                            <div class="card-toolbar">
                                <a class="btn btn-sm btn-light font-weight-bold mr-2 bg-primary" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Select dashboard daterange" data-placement="left" style="cursor:pointer;">
                                    <span class="text-white font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                                    <span class="text-white font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                                </a>
                                <button type="button" class="btn btn-primary font-weight-bolder" id="export_btn">
                                <span class="svg-icon svg-icon-md">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
                                            <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>Export Data</button>
                            </div>
                        </div>
                        <!--end::Header-->
                        <div class="card-body table-responsive">
                            <center><input type="search" class="form-control form-control-sm col-6" id="history_search" placeholder="Cari artikel / BIN Tujuan"/></center><br/>
                            <table class="table table-hover table-checkable" id="BinHistorytb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">User</th>
                                        <th class="text-light">Artikel</th>
                                        <th class="text-light">Store</th>
                                        <th class="text-light" style="white-space: nowrap;">BIN Awal</th>
                                        <th class="text-light" style="white-space: nowrap;">QBIN Awal</th>
                                        <th class="text-light" style="white-space: nowrap;">Qty Mts</th>
                                        <th class="text-light" style="white-space: nowrap;">NQBIN Awal</th>
                                        <th class="text-light" style="white-space: nowrap;">BIN Tujuan</th>
                                        <th class="text-light">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
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
@include('app.product_location_setup_v2.product_location_setup_v2_modal')
@include('app._partials.js')
@include('app.product_location_setup_v2.product_location_setup_v2_js')
@endSection()