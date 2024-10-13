@extends('app.structure')
@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex align-items-baseline flex-wrap mr-5">
                        <!--begin::Page Title-->
                        <h3 class="card-label">{{ $data['subtitle'] }}</h3>
                        <!--end::Page Title-->
                    </div>
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
                        <div class="card card-custom gutter-b">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xxl-8 col-xl-8">
                                        <input type="text" placeholder="Input nomor order / nomor Resi" id="pos_invoice_online"
                                               class="bg-light-success form-control"/>
                                    </div>
                                    <div class="col-xxl-4 col-xl-4">
                                        <a class="btn btn-primary col-12" id="exec_online_btn">Tampilkan</a>
                                    </div>
                                    <div class="col-xxl-8 col-xl-8 mt-4">
                                        <input type="text" placeholder="Catatan Tambahan" id="note"
                                               class="bg-light-success form-control d-none editor_panel"/>
                                    </div>
                                    <div class="col-xxl-4 col-xl-4 mt-4">
                                        <a class="btn btn-primary col-12 d-none editor_panel" id="done_btn">Selesai</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-xxl-12 d-none editor_panel">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b">
                            <div class="card-body table-responsive">
                                <h6>Data Invoice</h6>
                                <table class="table table-hover table-checkable" id="Invoicetb">
                                    <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Order Number</th>
                                        <th class="text-dark">No Resi</th>
                                        <th class="text-dark">Store</th>
                                        <th class="text-dark">PIC Transaksi</th>
                                        <th class="text-dark">Platform</th>
                                        <th class="text-dark">Order Date</th>
                                        <th class="text-dark">Payment Date</th>
                                        <th class="text-dark">Payment Method</th>
                                        <th class="text-dark">Shipping Fee</th>
                                        <th class="text-dark">Total Payment</th>
                                        <th class="text-dark">Time Print</th>
                                        <th class="text-dark">Tindakan</th>
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
                    <div class="col-lg-6 col-xxl-6 d-none editor_panel">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b">
                            <div class="card-body table-responsive">
                                <h6>Detail Invoice</h6>
                                <table class="table table-hover table-checkable" id="InvoiceDetailtb">
                                    <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Artikel</th>
                                        <th class="text-dark">Qty</th>
                                        <th class="text-dark">Harga</th>
                                        <th class="text-dark">Nameset</th>
                                        <th class="text-dark">Total</th>
                                        <th class="text-dark">Tindakan</th>
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
                    <div class="col-lg-6 col-xxl-6 d-none editor_panel">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b">
                            <div class="card-body table-responsive">
                                <h6>Detail Tracking</h6>
                                <table class="table table-hover table-checkable" id="Trackingtb">
                                    <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Lokasi</th>
                                        <th class="text-dark">Artikel</th>
                                        <th class="text-dark">Qty</th>
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
                    @if ($data['user']->g_name == 'administrator')
                        <div class="col-lg-12 col-xxl-12">
                            <!--begin::Card-->
                            <div class="card card-custom gutter-b">
                                <div class="card-body table-responsive">
                                    <h6>History Edit</h6>
                                    <input type="search" class="form-control  col-6" id="history_search"
                                           placeholder="Cari invoice / user"/><br/>
                                    <table class="table table-hover table-checkable" id="Historytb">
                                        <thead class="bg-light text-dark">
                                        <tr>
                                            <th class="text-dark">No</th>
                                            <th class="text-dark">Invoice</th>
                                            <th class="text-dark">User</th>
                                            <th class="text-dark">Aktifitas</th>
                                            <th class="text-dark">Catatan</th>
                                            <th class="text-dark">Tgl Edit</th>
                                            <th class="text-dark">Tgl Update</th>
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
                    @endif
                </div>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
    <!--end::Content-->
    @include('app.invoice_editor_online.invoice_editor_online_modal')
    @include('app._partials.js')
    @include('app.invoice_editor_online.invoice_editor_online_js')
@endSection()
