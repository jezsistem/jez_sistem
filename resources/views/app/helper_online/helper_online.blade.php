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
                        <div>
                            <input type="hidden" name="st_id" id="st_id" value={{ $data['st_id'] }}>
                            <h5 class="text-dark font-weight-bold my-1 mr-5">{{ $data['subtitle'] }}</h5>
                            {{-- <h5 class="text-dark font-weight-bold my-1 mr-5">Warehouse : {{$data['warehouse']}}</h5> --}}
                        </div>

                        <!--end::Page Title-->
                    </div>
                    <!--end::Page Heading-->
                </div>
                <!--end::Info-->
            </div>
        </div>
        <!--end::Subheader-->
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid col-lg-12">
            <!--begin::Container-->
            <div class="container">
                <div class="">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-2 col-sm-6 mb-4">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0" id="waiting_online_count">0</h3>
                                            <small>WAITING ONLINE</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0" id="under_review_count">0</h3>
                                            <small>UNDER REVIEW</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-4">
                                    <div class="card text-white" style="background-color: blue">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0" id="waiting_receipt_count">0</h3>
                                            <small>WAITING RECEIPT</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-4">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0" id="waiting_packing_count">0</h3>
                                            <small>WAITING PACKING</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-6 mb-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3 class="mb-0" id="done_online_count">0</h3>
                                            <small>DONE ONLINE</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-6 align-items-end">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <button type="button" class="btn btn-primary" id="open_modal_scan_manifest_btn">
                                        <i class="fas fa-file-alt"></i> Data Manifest
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <div class="row align-items-end">
                                        <div class="col-md-6">
                                            <label for="filter_date_type" class="form-label">Date Type</label>
                                            <select name="filter_date_type" id="filter_date_type" class="form-control">
                                                <option value="pick_date">Pick Date</option>
                                                <option value="transaction_date">Transaction Date</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="kt_dashboard_daterangepicker" class="form-label">Date Range</label>
                                            <a href="#" class="btn btn-light-primary btn-block font-weight-bold"
                                                id="kt_dashboard_daterangepicker" data-toggle="tooltip"
                                                title="Tanggal Filter" data-placement="left">
                                                <span class="font-size-base" id="kt_dashboard_daterangepicker_title">Today</span>
                                                <span class="font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                                                <input type="hidden" id="filter_date" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end: Datatable-->
                        </div>
                    </div>
                </div>
                <!--end::Card-->
                <div class="">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-body">
                            <!--begin: Datatable-->
                            <div class="form-group mb-1 pb-1">
                                <label for="order_number">Order Number/No Resi</label>
                                <input type="text" class="form-control" id="order_number" name="order_number"
                                    placeholder="Enter Order Number/No Resi/SKU">

                                <label for="status_filter" class="mt-3">Status</label>
                                <select class="form-control" id="status_filter" name="status_filter" required>
                                    <option value="WAITING ONLINE">WAITING ONLINE</option>
                                    <option value="UNDER REVIEW">UNDER REVIEW</option>
                                    <option value="WAITING RECEIPT">WAITING RECEIPT</option>
                                    <option value="WAITING PACKING">WAITING PACKING</option>
                                    <option value="DONE ONLINE">DONE ONLINE</option>
                                    <option value="DONE">DONE</option>
                                    <option value="">SEMUA STATUS</option>
                                </select>
                                <div id="status_filter_parent"></div>

                                <label for="status_pick" class="mt-3">Status Pick</label>
                                <select class="form-control" id="status_pick" name="status_pick" required>
                                    <option value="">SEMUA STATUS PICK</option>
                                    <option value="PICKED">PICKED</option>
                                    <option value="NOT PICKED">NOT PICKED</option>
                                </select>
                                <div id="status_pick_parent"></div>

                                <label for="platform" class="mt-3">Platform</label>
                                <select class="form-control" id="platform" name="platform" required>
                                    <option value="">SEMUA PLATFORM</option>
                                    @foreach ($data['platforms'] as $platform)
                                        <option value="{{ $platform->platform_name }}">{{ $platform->platform_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="platform_parent"></div>

{{--                                <label for="status_pick" class="mt-3">Status Pick</label>--}}
{{--                                <select class="form-control" id="status_pick" name="status_pick" required>--}}
{{--                                    <option value="">SEMUA STATUS PICK</option>--}}
{{--                                    <option value="PICKED">PICKED</option>--}}
{{--                                    <option value="NOT PICKED">NOT PICKED</option>--}}
{{--                                </select>--}}
{{--                                <div id="status_pick_parent"></div>--}}
                            </div>

                            <!--end: Datatable-->
                        </div>
                    </div>
                </div>

                <div class="">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-body">
                            <div class="row mt-6">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary mr-2" id="open_modal_resi_btn">
                                        Resi Massal
                                    </button>
                                </div>
                            </div>
                            <!--begin: Datatable-->
                            {{-- <table class="table table-bordered table-hover" id="helper_online_table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Transaksi</th>
                                        <th>Platform</th>
                                        <th>Store</th>
                                        <th>SKU</th>
                                        <th>Tanggal Pesanan</th>
                                        <th>Status Transaksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table> --}}
                            <!--end: Datatable-->
                            <center>
                                <div class="picked_online_trx row mt-12" id="picked_online_trx"></div>
                            </center>

                        </div>
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
    @include('app.helper_online.helper_online_modal')
    @include('app._partials.js')
    @include('app.helper_online.helper_online_js')
@endSection()
