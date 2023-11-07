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
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <input type="search" class="form-control form-control-sm col-6" id="wt_search" placeholder="Cari transaksi"/><br/>
                            <select class="form-control col-6" id="status_filter">
                                <option value="">- Filter Status -</option>
                                <option value="PAID">PAID</option>
                                <option value="UNPAID">UNPAID</option>
                                <option value="CANCEL">CANCEL</option>
                                <option value="SHIPPING NUMBER">SHIPPING NUMBER</option>
                                <option value="IN DELIVERY">IN DELIVERY</option>
                            </select><br/>
                            <select class="form-control col-6" id="payment_filter">
                                <option value="">- Filter Tipe Pembayaran -</option>
                                <option value="Bank BCA">Bank BCA</option>
                                <option value="Bank BRI">Bank BRI</option>
                                <option value="Midtrans">Midtrans</option>
                            </select><br/>
                            <table class="table table-hover table-checkable" id="Wttb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Tanggal</th>
                                        <th class="text-light">Batas(Jam)</th>
                                        <th class="text-light">Invoice</th>
                                        <th class="text-light">Customer</th>
                                        <th class="text-light">Total</th>
                                        <th class="text-light">Kode Unik</th>
                                        <th class="text-light">Kurir</th>
                                        <th class="text-light">Ongkir</th>
                                        <th class="text-light">Resi</th>
                                        <th class="text-light">Item</th>
                                        <th class="text-light">Metode</th>
                                        <th class="text-light">Status</th>
                                        <th class="text-light">Note</th>
                                        <th class="text-light">FN</th>
                                        <th class="text-light">R1</th>
                                        <th class="text-light">R2</th>
                                        <th class="text-light">R3</th>
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
@include('app.web_transaction.web_transaction_modal')
@include('app._partials.js')
@include('app.web_transaction.web_transaction_js')
@endSection()