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
                <div id="st_id_filter_parent"></div>
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Notice-->
            <div class="alert alert-custom alert-white alert-shadow fade show gutter-b bg-primary" role="alert">
                <input type="hidden" id="pos_summary_date" value=""/>
                <a href="#" class="btn btn-sm btn-light font-weight-bold mr-2 col-12" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Filter Tanggal" data-placement="left">
                    <span class="text-muted font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                    <span class="text-primary font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                </a>
            </div>
            <!--end::Notice-->

            <div class="row">
                <div class="col-lg-6 offline_chart">
                    <div class="card card-custom gutter-b">
                        <div class="card-header h-auto">
                            <div class="card-title py-5">
                                <h3 class="card-label">OFFLINE</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="offline_chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 cross_chart d-none">
                    <div class="card card-custom gutter-b">
                        <div class="card-header h-auto">
                            <div class="card-title py-5">
                                <h3 class="card-label">Cross Order</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="cross_chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 online_chart">
                    <div class="card card-custom gutter-b">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">ONLINE</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="online_chart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <!--begin::Header-->
                        <div class="card-header h-auto">
                            <!--begin::Title-->
                            <div class="card-title py-5">
                                <h3 class="card-label">ONLINE</h3>
                            </div>
                            <!--end::Title-->
                        </div>
                        <!--end::Header-->
                        <div class="card-body table-responsive">
                            <table class="table table-hover table-checkable" id="Onlinetb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Sales</th>
                                        <th class="text-light">Penjualan</th>
                                        <th class="text-light" style="white-space: nowrap;">Total Penjualan</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
                <div class="col-lg-6">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">OFFLINE</h3>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-hover table-checkable" id="Offlinetb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Sales</th>
                                        <th class="text-light">Penjualan</th>
                                        <th class="text-light" style="white-space: nowrap;">Total Penjualan</th>
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
            <div class="col-lg-12 col-xxl-12">
                <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                            <span class="btn-sm btn-primary font-weight-bolder font-size-h6" id="article_power_label">
                                Product Rating
                            </span>
                            <span class="btn-sm btn-primary font-weight-bolder font-size-h6" id="export_excel">
                                Excel
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                            <div class="d-flex flex-column text-right col-4">
                                <select class="form-control" id="st_filter">
                                    <option value=''>- Storage -</option>
                                    @foreach ($data['st_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex flex-column text-right col-4">
                                <select class="form-control" id="pc_filter">
                                    <option value=''>- Kategori -</option>
                                    @foreach ($data['pc_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex flex-column text-right col-4">
                                <select class="form-control" id="specific_filter">
                                    <option value=''>- Parameter (Max) -</option>
                                    <option value='sales'>Penjualan</option>
                                    <option value='profit'>Profit</option>
                                    <option value='aging'>Aging</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                        <table class="table table-hover table-checkable" id="ProductRatingtb">
                            <thead class="bg-primary text-light">
                                <tr>
                                    <th class="text-light">No</th>
                                    <th class="text-light" style="white-space:no-wrap;">Power (rating)</th>
                                    <th class="text-light">Brand</th>
                                    <th class="text-light">Artikel</th>
                                    <th class="text-light">Aging</th>
                                    <!-- <th class="text-light">HPP</th> -->
                                    <th class="text-light">HJ</th>
                                    <!-- <th class="text-light">Profit</th> -->
                                    <th class="text-light">Terjual</th>
                                    <!-- <th class="text-light" style="white-space:no-wrap;">Total Profit</th> -->
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
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
@include('app.pos_summary.pos_summary_modal')
@include('app._partials.js')
@include('app.pos_summary.pos_summary_js')
@endSection()