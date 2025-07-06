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
                        <div class="card-header flex-wrap py-3">
                            <div class="card-toolbar">
                                <!--begin::Dropdown-->
                                <div class="dropdown dropdown-inline mr-2">
                                    <button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="svg-icon svg-icon-md">
                                        <!-- SVG ICON -->
                                    </span>Export</button>
                                    <!--begin::Dropdown Menu-->
                                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                        <ul class="navi flex-column navi-hover py-2">
                                            <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">Tentukan Tanggal :</li>
                                            <li class="navi-item mb-4">
                                                <center><input type="date" id="start_date" class="form-control form-control-sm"/></center>
                                            </li>
                                            <li class="navi-item">
                                                <center><input type="date" id="end_date" class="form-control form-control-sm"/></center>
                                            </li>
                                            <li class="navi-item">
                                                <a href="#" class="navi-link" id="std_export_all_btn">
                                                    <span class="navi-icon">
                                                        <i class="la la-copy"></i>
                                                    </span>
                                                    <span class="btn btn-primary btn-xs">Export All</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!--end::Dropdown Menu-->
                                </div>
                                <!--end::Dropdown-->
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <div class="row mb-4">
                                <div class="col-12 col-md-6 mb-2 mb-md-0">
                                    <input type="search" class="form-control w-100" id="stock_transfer_search" placeholder="Cari user / Artikel id / Kode Transfer"/>
                                </div>
                                <div class="col-6 col-md-2 mb-2 mb-md-0">
                                    <select class="form-control w-100" id="st_id_start" name="st_id_start" required>
                                        <option value="">- Pilih Store Awal -</option>
                                        @foreach ($data['st_id'] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <div id="st_id_start_parent"></div>
                                </div>
                                <div class="col-6 col-md-2 mb-2 mb-md-0">
                                    <select class="form-control w-100" id="st_id_end" name="st_id_end" required>
                                        <option value="">- Pilih Store Tujuan -</option>
                                        @foreach ($data['st_id'] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <div id="st_id_end_parent"></div>
                                </div>
                                <div class="col-12 col-md-2">
                                    <a href="#" class="btn btn-date-info font-weight-bold w-100"
                                        id="kt_dashboard_daterangepicker" data-toggle="tooltip"
                                        title="Tanggal Invoice" data-placement="left">
                                        <span class="font-size-base"
                                            id="kt_dashboard_daterangepicker_title">Today</span>
                                        <span class="font-size-base font-weight-bolder"
                                            id="kt_dashboard_daterangepicker_date"></span>
                                        <input type="hidden" id="transfer_receive_date" />
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-checkable" id="StockTransferDatatb">
                                    <thead class="bg-light text-dark">
                                        <tr>
                                            <th class="text-dark">No</th>
                                            <th class="text-dark">Kode</th>
                                            <th class="text-dark">Pengirim</th>
                                            <th class="text-dark" style="white-space: nowrap;">Qty</th>
                                            <th class="text-dark" style="white-space: nowrap;">Store Awal</th>
                                            <th class="text-dark" style="white-space: nowrap;">Store Tujuan</th>
                                            <th class="text-dark">Penerima</th>
                                            <th class="text-dark" style="white-space: nowrap;">Tanggal</th>
                                            <th class="text-dark">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <!--end: Datatable-->
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
                <div class="col-lg-12 col-xxl-12">
                    <div class="card card-custom gutter-b">
                        <div class="card-body table-responsive">
                            <h5>History Penerimaan</h5>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-2">
                                    <input type="search" class="form-control w-100" id="history_search" placeholder="Cari kode / artikel"/><br/>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-checkable" id="Historytb">
                                    <thead class="bg-light text-dark">
                                        <tr>
                                            <th class="text-dark">No</th>
                                            <th class="text-dark">Kode</th>
                                            <th class="text-dark">Store Awal</th>
                                            <th class="text-dark">Store Tujuan</th>
                                            <th class="text-dark">Brand</th>
                                            <th class="text-dark">Artikel</th>
                                            <th class="text-dark">Warna</th>
                                            <th class="text-dark">Size</th>
                                            <th class="text-dark">Qty Terima</th>
                                            <th class="text-dark">HB</th>
                                            <th class="text-dark">HJ</th>
                                            <th class="text-dark">Penerima</th>
                                            <th class="text-dark">Tanggal</th>
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
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@include('app.stock_transfer_data.stock_transfer_data_modal')
@include('app._partials.js')
@include('app.stock_transfer_data.stock_transfer_data_js')

<style>
@media (max-width: 767.98px) {
    .card-header, .card-body {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    .table-responsive {
        overflow-x: auto;
    }
    th, td {
        white-space: nowrap;
    }
}
</style>
@endSection()