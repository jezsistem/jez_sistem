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
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <!--begin::Notice-->
            <div class="alert alert-custom alert-white alert-shadow fade show gutter-b bg-primary" role="alert">
                <input id="pl_id_hidden" type="hidden" value = ""/>
                <input id="_mode" type="hidden" value = ""/>
                <select class="form-control" id="pl_id" name="pl_id" required>
                    <option value="">- Pilih BIN -</option>
                    @foreach ($data['pl_id'] as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                <div id="pl_id_parent"></div>
            </div>
            <!--end::Notice-->

            <div class="row">
                <div class="col-lg-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <!--begin::Header-->
                        <div class="card-header h-auto align-items-center justify-content-between">
                            <!--begin::Title-->
                            <input type="hidden" id="adjustment_date" value=""/>
                            <div class="card-title py-5">
                                <h3 class="card-label">History Adjustment</h3>
                            </div>
                            <div class="card-title py-5">
                                <select class="form-control" id="st_id_filter" name="st_id_filter" required>
                                    <option value="">- Storage -</option>
                                    @foreach ($data['st_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="alert alert-custom alert-white alert-shadow fade show gutter-b bg-primary" role="alert">
                                <input type="hidden" id="stock_report_date" value=""/>
                                <a href="#" class="btn btn-sm btn-light font-weight-bold mr-2 col-12" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Filter Tanggal" data-placement="left">
                                    <span class="text-muted font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                                    <span class="text-primary font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                                </a>
                            </div>
                            <!--end::Title-->
                        </div>
                        <!--end::Header-->
                        <div class="card-body table-responsive">
                            <center><input type="search" class="form-control form-control-sm col-6" id="history_search" placeholder="Cari kode / nama artikel / warna / brand / size"/></center><br/>
                            <table class="table table-hover table-checkable" id="AdjustmentHistorytb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Code</th>
                                        <th class="text-light">Store</th>
                                        <th class="text-light">BIN</th>
                                        <th class="text-light">User</th>
                                        <th class="text-light">Brand</th>
                                        <th class="text-light">Artikel</th>
                                        <th class="text-light">Warna</th>
                                        <th class="text-light">Size</th>
                                        <th class="text-light">Datetime</th>
                                        <th class="text-light">Old Qty</th>
                                        <th class="text-light">New Qty</th>
                                        <th class="text-light">Adjust</th>
                                        <th class="text-light">Note</th>
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
                <div class="col-lg-6">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <!--begin::Header-->
                        <div class="card-header h-auto">
                            <!--begin::Title-->
                            <div class="card-title py-5">
                                <h3 class="card-label">Sudah Validasi</h3>
                            </div>
                            <!--end::Title-->
                        </div>
                        <!--end::Header-->
                        <div class="card-body table-responsive">
                            <a class="btn btn-sm btn-primary" id="adjustment_finish_btn" title="akan generate kode adjustment terbaru, dan menghilangkan reminder BIN yang sudah divalidasi">Selesai Adjustment</a><br/>
                            <table class="table table-hover table-checkable" id="Validatedtb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">BIN</th>
                                        <th class="text-light">User</th>
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
                                <h3 class="card-label">Belum Validasi</h3>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-hover table-checkable" id="NotValidatedtb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">BIN</th>
                                        <th class="text-light">Lokasi</th>
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
@include('app.adjustment.adjustment_modal')
@include('app._partials.js')
@include('app.adjustment.adjustment_js')
@endSection()