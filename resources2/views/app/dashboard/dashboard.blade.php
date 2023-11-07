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
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <!--begin::Daterange-->
                <input type="hidden" id="dashboard_date" value=""/>
                <a href="https://report.jez.co.id" target="_blank" class="btn-sm btn-inventory" id="urban_panel_btn" style="font-weight:bold;">Report Dashboard</a>
                <input type="hidden" id="urban_panel" value="" />
                <a class="btn btn-sm btn-light font-weight-bold mr-2 bg-primary" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Select dashboard daterange" data-placement="left" style="cursor:pointer;">
                    <span class="text-white font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                    <span class="text-white font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                </a>
                <!--end::Daterange-->
            </div>
            <!--end::Toolbar-->
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <input type="hidden" id="c_first_load" value=""/>
    <input type="hidden" id="ca_first_load" value=""/>
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Dashboard-->
            <!--begin::Row-->
            <div class="row d-flex align-items-center pl-4">
            <a href="{{ url('dashboards') }}" class="btn-sm btn-primary" id="del_activity_btn">Dashboard V2</a>
            <a href="#" class="btn-sm btn-primary" id="detail_activity_btn">Aktifitas User</a>
            </div>
            <div class="row">
                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 11-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="row" style="padding:10px; padding-top:20px;">
                                <div class="col-6">
                                    <a class="btn-sm btn-primary font-weight-bolder font-size-h6">
                                        Jual
                                    </a><br/><br/>
                                    <select class="ml-2 bg-light-primary" id="scross_filter">
                                        <option value="nocross">Tanpa Cross</option>
                                        <option value="cross">Dengan Cross</option>
                                    </select>
                                </div>
                                <div class="text-right col-6">
                                    <a class="text-white font-weight-bolder font-size-h5 btn-sm btn-primary" id="nett_sales_label"><span id="nett_sales_label_reload" style="white-space:nowrap;"></span></a>
                                </div>
                            </div>
                            <center>
                                <a class="btn btn-primary" id="ns_show_btn">Tampilkan</a>
                                <img class="d-none" id="ns_loading" src="{{ asset('upload/loading/loading.gif') }}" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="nettsaleChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 11-->
                </div>
                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="row" style="padding:10px; padding-top:20px;">
                                <div class="col-6">
                                    <a class="btn-sm btn-primary font-weight-bolder font-size-h6">
                                        Profit
                                    </a><br/><br/>
                                    <select class="ml-2 bg-light-primary" id="pcross_filter">
                                        <option value="nocross">Tanpa Cross</option>
                                        <option value="cross">Dengan Cross</option>
                                    </select>
                                </div>
                                <div class="text-right col-6">
                                    <span class="text-white font-weight-bolder font-size-h5 btn-sm btn-primary" id="profit_label"><span id="profit_label_reload" style="white-space:nowrap;"></span></span>
                                </div>
                            </div>
                            <center>
                                <a class="btn btn-primary" id="pr_show_btn">Tampilkan</a>
                                <img class="d-none" id="pr_loading" src="{{ asset('upload/loading/loading.gif') }}" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="profitChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>

                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 11-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bolder font-size-h6">
                                    Cross Sales
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="text-white font-weight-bolder font-size-h5 btn-sm btn-primary" id="cnett_sales_label"><span id="cnett_sales_label_reload"></span></span>
                                </div>
                            </div>
                            <center>
                                <a class="btn btn-primary" id="cns_show_btn">Tampilkan</a>
                                <img class="d-none" id="cns_loading" src="{{ asset('upload/loading/loading.gif') }}" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="cnettsaleChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 11-->
                </div>
                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bolder font-size-h6">
                                    Cross Profit
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="text-white font-weight-bolder font-size-h5 btn-sm btn-primary" id="cprofit_label"><span id="cprofit_label_reload"></span></span>
                                </div>
                            </div>
                            <center>
                                <a class="btn btn-primary" id="cpr_show_btn">Tampilkan</a>
                                <img class="d-none" id="cpr_loading" src="{{ asset('upload/loading/loading.gif') }}" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="cprofitChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>

                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 11-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bolder font-size-h6">
                                    Pembelian
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="text-white font-weight-bolder font-size-h5 btn-sm btn-primary" id="purchase_label"><span id="purchase_label_reload"></span></span>
                                </div>
                            </div>
                            <center>
                                <a class="btn btn-primary" id="pc_show_btn">Tampilkan</a>
                                <img class="d-none" id="pc_loading" src="{{ asset('upload/loading/loading.gif') }}" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="purchaseChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 11-->
                </div>
                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bolder font-size-h6" id="cash_credit_asset_label">
                                    Aset Cash/Credit
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="text-white font-weight-bolder font-size-h5 btn-sm btn-primary" id="assets_label"></span>
                                </div>
                            </div>
                            <center>
                                <a class="btn btn-primary" id="a_show_btn">Tampilkan</a>
                                <img class="d-none" id="a_loading" src="{{ asset('upload/loading/loading.gif') }}" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="assetChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>
                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bolder font-size-h6" id="consignment_asset_label">
                                    Consignment
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="text-white font-weight-bolder font-size-h5 btn-sm btn-primary" id="consign_assets_label"></span>
                                </div>
                            </div>
                            <center>
                                <a class="btn btn-primary" id="ca_show_btn">Tampilkan</a>
                                <img class="d-none" id="ca_loading" src="{{ asset('upload/loading/loading.gif') }}" style="width:20%; padding-bottom:20px;"/></center>
                            <div class="d-none" id="consignAssetChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>
                <div class="col-lg-6 col-xxl-6" style="min-height: 315px;">
                    <!--begin::Stats Widget 12-->
                    <div class="card card-custom card-stretch gutter-b" style="border-radius:15px;">
                        <!--begin::Body-->
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                <span class="btn-sm btn-primary font-weight-bolder font-size-h6">
                                    Hutang
                                </span>
                                <div class="d-flex flex-column text-right">
                                    <span class="text-white font-weight-bolder font-size-h5 btn-sm btn-primary" id="debt_label"></span>
                                </div>
                            </div>
                            <center>
                                <a class="btn btn-primary" id="d_show_btn">Tampilkan</a>
                                <img class="d-none" id="d_loading" src="{{ asset('upload/loading/loading.gif') }}" style="width:20%; padding-bottom:20px;"/>
                            </center>
                            <div class="d-none" id="debtChart"></div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 12-->
                </div>
            </div>
            <!--end::Row-->
            <!--end::Dashboard-->
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@include('app.dashboard.dashboard_modal')
@include('app._partials.js')
@include('app.dashboard.dashboard_js')
@endSection()
