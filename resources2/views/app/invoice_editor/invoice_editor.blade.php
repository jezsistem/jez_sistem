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
                @if ($data['user']->g_name == 'administrator')
                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3">
                            <div class="card-toolbar">
                                <!--begin::Button-->
                                <a href="#" class="btn btn-primary font-weight-bolder" id="add_btn">
                                <span class="svg-icon svg-icon-md">
                                    <!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24" />
                                            <circle fill="#000000" cx="9" cy="15" r="6" />
                                            <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3" />
                                        </g>
                                    </svg>
                                    <!--end::Svg Icon-->
                                </span>Data Baru</a>
                                <!--end::Button-->
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <input type="search" class="form-control form-control-sm col-6" id="data_search" placeholder="Cari store / penanggung jawab"/><br/>
                            <table class="table table-hover table-checkable" id="PermissionDatatb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Store</th>
                                        <th class="text-light">Divisi</th>
                                        <th class="text-light">Penanggung Jawab</th>
                                        <th class="text-light"></th>
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
                <div class="col-lg-12 col-xxl-12">
                    <div class="card card-custom gutter-b">
                        <div class="card-body">
                            <div class="row"> 
                                <div class="col-xxl-8 col-xl-8">
                                    <input type="text" placeholder="Input nomor invoice INVxxx" id="pos_invoice" class="bg-light-success form-control"/>
                                </div>
                                <div class="col-xxl-4 col-xl-4">
                                    <a class="btn btn-primary col-12" id="exec_btn">Tampilkan</a>
                                </div>
                                <div class="col-xxl-8 col-xl-8 mt-4">
                                    <input type="text" placeholder="Catatan Tambahan" id="note" class="bg-light-success form-control d-none editor_panel"/>
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
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Invoice</th>
                                        <th class="text-light">Kasir</th>
                                        <th class="text-light">Divisi</th>
                                        <th class="text-light">SubDivisi</th>
                                        <th class="text-light">Metode</th>
                                        <th class="text-light">Admin</th>
                                        <th class="text-light">Total<br/><span style='white-space:nowrap;'>(-admin)</span></th>
                                        <th class="text-light">Status</th>
                                        <th class="text-light">Tgl<br/>(Thn-Bln-Tgl Jam-Mnt-Dtk)</th>
                                        <th class="text-light">Tindakan</th>
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
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Artikel</th>
                                        <th class="text-light">Qty</th>
                                        <th class="text-light">Harga</th>
                                        <th class="text-light">Nameset</th>
                                        <th class="text-light">Total</th>
                                        <th class="text-light">Tindakan</th>
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
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Lokasi</th>
                                        <th class="text-light">Artikel</th>
                                        <th class="text-light">Qty</th>
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
                @if ($data['user']->g_name == 'administrator')
                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-body table-responsive">
                            <h6>History Edit</h6>
                            <input type="search" class="form-control form-control-sm col-6" id="history_search" placeholder="Cari invoice / user"/><br/>
                            <table class="table table-hover table-checkable" id="Historytb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Invoice</th>
                                        <th class="text-light">User</th>
                                        <th class="text-light">Aktifitas</th>
                                        <th class="text-light">Catatan</th>
                                        <th class="text-light">Tgl Edit</th>
                                        <th class="text-light">Tgl Update</th>
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
@include('app.invoice_editor.invoice_editor_modal')
@include('app._partials.js')
@include('app.invoice_editor.invoice_editor_js')
@endSection()
