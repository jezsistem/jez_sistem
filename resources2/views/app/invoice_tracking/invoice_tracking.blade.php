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
                    <div class="col-12 row justify-content-between">
                        <!--begin::Button-->
                        <select class="form-control col-sm-6 col-lg-2 bg-primary text-white" id="std_id" name="std_id">
                            <option value="">- Semua Divisi -</option>
                            @foreach ($data['std_id'] as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <!--end::Button-->
                        <!--begin::Button-->
                        <select class="form-control col-sm-6 col-lg-2 bg-primary text-white" id="status_filter">
                            <option value="">- Semua Status -</option>
                            <option value="DONE">DONE</option>
                            <option value="NAMESET">NAMESET</option>
                            <option value="EXCHANGE">EXCHANGE</option>
                            <option value="REFUND">REFUND</option>
                            <option value="SHIPPING NUMBER">SHIPPING NUMBER</option>
                            <option value="IN DELIVERY">IN DELIVERY</option>
                        </select>
                        <div class="col-sm-6 col-lg-4">
                            <a class="btn btn-sm btn-light font-weight-bold bg-inventory" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Select dashboard daterange" data-placement="left" style="cursor:pointer;">
                                <span class="text-white font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                                <span class="text-white font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                            </a>
                        </div>
                        <!--end::Button-->
                        <a href="#" class="col-sm-6 col-lg-2 btn-sm btn-primary font-weight-bold" style="padding-top:14px;" id="check_invoice_btn">Check Invoice Baru Refund</a>
                    </div>
                    <div class="card-body table-responsive">
                        <!--begin: Datatable-->
                        <input type="search" class="form-control form-control-sm col-6" id="invoice_tracking_search" placeholder="Cari invoice / nomor resi / customer (ketik minimal 4 karakter)"/><br/>
                        <table class="table table-hover table-checkable" id="InvoiceTrackingtb">
                            <thead class="bg-primary text-light">
                                <tr>
                                    <th class="text-light">No</th>
                                    <th class="text-light">Invoice</th>
                                    <th class="text-light">Kasir</th>
                                    <th class="text-light">Customer</th>
                                    <th class="text-light">Divisi</th>
                                    <th class="text-light">Tanggal</th>
                                    <th class="text-light">Item</th>
                                    <th class="text-light">Total</th>
                                    <th class="text-light">Resi</th>
                                    <th class="text-light">Info</th>
                                    <th class="text-light">Status</th>
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
@include('app.invoice_tracking.invoice_tracking_modal')
@include('app._partials.js')
@include('app.invoice_tracking.invoice_tracking_js')
@endSection()
