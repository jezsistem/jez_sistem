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
            <div class="row">
                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header h-auto align-items-center justify-content-between">
                            <!--begin::Title-->
                            <input type="hidden" id="report_date" value=""/>
                            <div class="card-title py-5">
                                <select class="form-control" id="pc_id" name="pc_id" required>
                                    <option value="">- Kategori -</option>
                                    @foreach ($data['pc_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="card-title py-5">
                                <select class="form-control" id="st_id" name="st_id" required>
                                    <option value="">- Store -</option>
                                    @foreach ($data['st_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="alert alert-custom alert-white alert-shadow fade show gutter-b bg-primary" role="alert">
                                <a href="#" class="btn btn-date-info font-weight-bold mr-2 col-12" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Filter Tanggal" data-placement="left">
                                    <span class="text-muted font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                                    <span class="text-primary font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                                </a>
                            </div>
                            <div class="alert alert-custom alert-white alert-shadow fade show gutter-b bg-primary" role="alert">
                                <a href="#" class="btn btn-sm btn-success font-weight-bold mr-2 col-12" id="update_btn" style="color:black;">
                                    Synchronize Data Terbaru
                                </a>
                            </div>
                            <div class="alert alert-custom alert-white alert-shadow fade show gutter-b bg-primary" role="alert">
                                <a href="#" class="btn btn-sm btn-success font-weight-bold mr-2 col-12" id="daily_update_btn" style="color:black;">
                                    Daily Update
                                </a>
                            </div>
                            <!--end::Title-->
                        </div>

                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <input type="search" class="form-control  col-6" id="ai_search" placeholder="Cari artikel"/><br/>
                            <table class="table table-hover table-checkable" id="AItb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Tgl</th>
                                        <th class="text-dark">Store</th>
                                        <th class="text-dark">Brand</th>
                                        <th class="text-dark">Artikel</th>
                                        <th class="text-dark">Warna</th>
                                        <th class="text-dark">Size</th>
                                        <th class="text-dark">Saldo Awal</th>
                                        <th class="text-dark">Pembelian</th> <!-- Penerimaan PO -->
                                        <th class="text-dark">Transfer Out</th>   <!-- Yang dipick dari store awal -->
                                        <th class="text-dark">Transfer In</th>   <!-- Yang dipick dari store awal -->
                                        <th class="text-dark">Adjustment</th> <!-- Dar Adjustment -->
                                        <th class="text-dark">Terjual</th> <!-- Dar Adjustment -->
                                        <th class="text-dark">Refund</th> <!-- Dar Adjustment -->
                                        <th class="text-dark">Stok Saat Ini</th> <!-- Dar Adjustment -->
                                        <th class="text-dark">HPP</th> <!-- Dar Adjustment -->
                                        <th class="text-dark">HJ</th> <!-- Dar Adjustment -->
                                        <th class="text-dark">Profit</th> <!-- Dar Adjustment -->
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
                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3">
                            <div class="card-title">
                                <h3 class="card-label">History</h3>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <input type="search" class="form-control  col-6" id="history_search" placeholder="Cari user"/><br/>
                            <table class="table table-hover table-checkable" id="Historytb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">User</th>
                                        <th class="text-dark">Aktifitas</th>
                                        <th class="text-dark">Waktu</th>
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
@include('app._partials.js')
@include('app.report.article_report.po_receive_js')
@endSection()
