@extends('app.structure')
@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-2">
                <!--begin::Page Title-->
                <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5">{{ $data['subtitle'] }}</h5>
                <!--end::Page Title-->
            </div>
            <!--end::Info-->
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
                <div class="col-lg-6 col-xxl-4" id="user_activity_reload">
                    <!--begin::List Widget 9-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header align-items-center border-0 mt-4">
                            <h3 class="card-title align-items-start flex-column">
                                <a href="{{ url('dashboard') }}" class="font-weight-bolder btn-sm btn-primary text-white"><i class="fa fa-arrow-left"></i> Dashboard V1</a>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-4">
                            <!--begin::Timeline-->
                            <a class="btn btn-sm btn-light font-weight-bold mr-2 bg-primary" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Select dashboard daterange" data-placement="left" style="cursor:pointer;">
                                <span class="text-white font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                                <span class="text-white font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                            </a>
                            <select class="form-control mt-2 bg-light-success text-dark" id="info_filter">
                                <option value="brand">Grafik Brand</option>
                                <option value="store">Grafik Store</option>
                            </select>
                            <select class="form-control mt-2 bg-primary text-white" id="division_filter">
                                <option value='all'>- Semua Divisi -</option>
                                <option value="online">Online</option>
                                <option value="offline">Offline</option>
                            </select>
                            <div class="row mt-2">
                                @foreach ($data['st_id'] as $key => $value)
                                    <a class="btn btn-sm btn-primary col-4 st_selection" data-id="{{ $key }}">{{ $value }}</a>
                                @endforeach
                            </div>
                            <!--end::Timeline-->
                        </div>
                        <!--end: Card Body-->
                    </div>
                    <!--end: List Widget 9-->
                </div>
                <div class="col-lg-4 col-xxl-4" style="min-height: 315px;">
                    <!--begin::Stats Widget 11-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary" id="nett_sales_btn">
                                    <div class="font-weight-bolder font-size-h6" id="adm_nett_sales_label"></div>
                                    <div>Non Admin</div>
                                    <div class="font-weight-bolder font-size-h6" id="nett_sales_label"></div>
                                    <div>Jual Bersih</div>
                                </span>
                                <span class="btn-sm btn-primary" id="profits_btn">
                                    <div class="font-weight-bolder font-size-h6" id="adm_profits_label"></div>
                                    <div>Non Admin</div>
                                    <div class="font-weight-bolder font-size-h6" id="profits_label"></div>
                                    <div>Profit</div>
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary" id="cross_nett_sales_btn">
                                    <div class="font-weight-bolder font-size-h6" id="adm_cross_nett_sales_label"></div>
                                    <div>Non Admin</div>
                                    <div class="font-weight-bolder font-size-h6" id="cross_nett_sales_label"></div>
                                    <div>Cross Order</div>
                                </span>
                                <span class="btn-sm btn-primary" id="cross_profits_btn">
                                    <div class="font-weight-bolder font-size-h6" id="adm_cross_profits_label"></div>
                                    <div>Non Admin</div>
                                    <div class="font-weight-bolder font-size-h6" id="cross_profits_label"></div>
                                    <div>Cross Order Profit</div>
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary" id="cc_assets_btn">
                                    <div class="font-weight-bolder font-size-h6" id="cc_assets_label"></div>
                                    <div>CC Assets</div>
                                </span>
                                <span class="btn-sm btn-primary" id="c_assets_btn">
                                    <div class="font-weight-bolder font-size-h6" id="c_assets_label"></div>
                                    <div>C Assets</div>
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary" id="purchases_btn">
                                    <div class="font-weight-bolder font-size-h6"  id="purchases_label"></div>
                                    <div>Pembelian</div>
                                </span>
                                <span class="btn-sm btn-primary" id="debts_btn">
                                    <div class="font-weight-bolder font-size-h6" id="debts_label"></div>
                                    <div>Hutang</div>
                                </span>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 11-->
                </div>
                <div class="col-lg-4 col-xxl-4" style="min-height: 115px;">
                    <!--begin::Stats Widget 11-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary" id="cc_exc_assets_btn">
                                    <div class="font-weight-bolder font-size-h6" id="cc_exc_assets_label"></div>
                                    <div>CC Exception Assets</div>
                                </span>
                                <span class="btn-sm btn-primary" id="c_exc_assets_btn">
                                    <div class="font-weight-bolder font-size-h6" id="c_exc_assets_label"></div>
                                    <div>C Exception Assets</div>
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary">
                                    <div class="font-weight-bolder font-size-h6" id="gid_label"></div>
                                    <div>Goods In Draft</div>
                                </span>
                                <span class="btn-sm btn-primary">
                                    <div class="font-weight-bolder font-size-h6" id="git_label"></div>
                                    <div>Goods In Transit</div>
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary" id="detail_activity_btn">
                                    <div>AKTIFITAS USER</div>
                                </span>
                                <a href="{{ url('asset_detail') }}" class="btn-sm btn-info">
                                    Lihat Detail Asset/Penjualan
                                </a>
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 11-->
                </div>
                <div class="col-lg-12 col-xxl-12 d-none" style="min-height: 315px;" id="graph_panel">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bolder">
                                    Graph
                                </span>
                            </div>
                            <div id="chart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>
                <div class="col-lg-12 col-xxl-12" id="loadTable"></div>
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
@include('app.updated_dashboard.dashboard_modal')
@include('app._partials.js')
@include('app.updated_dashboard.dashboard_js')
@endSection()
