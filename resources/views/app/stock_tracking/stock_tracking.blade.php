@extends('app.structure')
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
                    <option value="">- Storage -</option>
                    @foreach ($data['st_id'] as $key => $value)
                        @if ($key == $data['user']->st_id)
                        <option value="{{ $key }}" selected>{{ $value }}</option>
                        @else
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endif
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
            <div class="col-lg-12 col-xxl-12">
                <!--begin::Card-->
                <div class="card card-custom gutter-b">
                    <div class="col-12 row">
                        <!--begin::Button-->
                        <select class="form-control col-sm-6 col-lg-2 bg-primary text-white" id="br_id" name="br_id">
                            <option value="">- Semua Brand -</option>
                            @foreach ($data['br_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <select class="form-control col-sm-6 col-lg-2 bg-primary text-white" id="psc_id" name="psc_id">
                            <option value="">- Semua Sub Kategori -</option>
                            @foreach ($data['psc_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <select class="form-control col-sm-6 col-lg-2 bg-light-success text-dark" id="std_id" name="std_id">
                            <option value="">- Divisi Penjualan -</option>
                            @foreach ($data['std_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <div class="col-sm-6 col-lg-4">
                            <a class="btn btn-sm btn-light font-weight-bold bg-inventory" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Select dashboard daterange" data-placement="left" style="cursor:pointer;">
                                <span class="font-size-base" id="kt_dashboard_daterangepicker_title">Today</span>
                                <span class="font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                            </a>
                            <a class="btn btn-sm btn-danger" id="problem_btn" data-notice="">0</a>
                            <a class="btn btn-sm btn-light-warning" id="waiting_online_btn" data-notice="">0</a>
                            <a class="btn btn-sm btn-warning" id="waiting_offline_btn" data-notice="">0</a>
                            <a class="btn btn-sm btn-info" style="background:#007bff;" id="graph_btn"><i class="fa fa-bar-chart"></i></a>
                        </div>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <select class="form-control col-sm-6 col-lg-2 bg-primary text-white" id="status_filter">
                            <option value="">- Semua Status -</option>
                            <option value="WAITING OFFLINE">WAITING OFFLINE</option>
                            <option value="WAITING ONLINE">WAITING ONLINE</option>
                            <option value="WAITING FOR PACKING">WAITING FOR PACKING</option>
                            <option value="WAITING FOR CHECKOUT">WAITING FOR CHECKOUT</option>
                            <option value="WAITING TO TAKE">WAITING TO TAKE</option>
                            <option value="WAITING FOR NAMESET">WAITING FOR NAMESET</option>
                            <option value="DONE">DONE</option>
                            <option value="REJECT">REJECT</option>
                            <option value="INSTOCK">INSTOCK</option>
                            <option value="INSTOCK APPROVAL">INSTOCK APPROVAL</option>
                            <option value="REFUND">REFUND</option>
                            <option value="EXCHANGE">EXCHANGE</option>
                            <option value="COMPLAINT">COMPLAINT</option>
                        </select>
                        <!--end::Button-->
                    </div>
                    <div class="card-body table-responsive">
                        <!--begin: Datatable-->
                        <input type="search" class="form-control  col-6" id="stock_tracking_search" placeholder="Cari artikel / customer / invoice (ketik minimal 5 karakter)"/><br/>
                        <table class="table table-hover table-checkable" id="StockTrackingtb">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th class="text-dark">No</th>
                                    <th class="text-dark">Tanggal <i><small>* pergerakan stock</small></i></th>
                                    <th class="text-dark">Artikel <i><small>* silahkan klik item dibawah untuk melihat detail user terlibat</small></i></th>
                                    <th class="text-dark">Invoice</th>
                                    <th class="text-dark">Cust</th>
                                    <th class="text-dark">Qty</th>
                                    <th class="text-dark">Lokasi</th>
                                    <th class="text-dark">Status</th>
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
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@include('app.stock_tracking.stock_tracking_modal')
@include('app._partials.js')
@include('app.stock_tracking.stock_tracking_js')
@endSection()
