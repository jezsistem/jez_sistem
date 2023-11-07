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
                <div class="col-lg-6">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-header h-auto">
                            <!--begin::Title-->
                            <div class="card-title py-5">
                                <h3 class="card-label">By Manual</h3>
                            </div>
                            <!--end::Title-->
                        </div>
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <div class="row">
                                <div class="form-group mb-1 pb-1 col-4">
                                    <!-- <input type="hidden" id="_pc_id" value="" /> -->
                                    <select class="form-control" id="st_id_start" name="st_id_start" required>
                                        <option value="">- Store Awal -</option>
                                        @foreach ($data['st_id'] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <div id="st_id_start_parent"></div>
                                </div>
                                <div class="form-group mb-1 pb-1 col-4">
                                    <!-- <input type="hidden" id="_pc_id" value="" /> -->
                                    <select class="form-control" id="st_id_end" name="st_id_end" required>
                                        <option value="">- Store Tujuan -</option>
                                        @foreach ($data['st_id'] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <div id="st_id_end_parent"></div>
                                </div>
                                <div class="form-group mb-1 pb-1 col-4">
                                    <a class="btn btn-primary col-12" id="transfer_btn" style="white-space:nowrap;">Transfer</a>
                                </div>
                            </div>
                            <div class="form-group mb-1 pb-1">
                                <select class="form-control" id="pl_id" name="pl_id" required>
                                    <option value="">- BIN Untuk Ditransfer -</option>
                                </select>
                                <div id="pl_id_parent"></div>
                            </div>
                            <!--end: Datatable-->
                            <div class="row col-12">
                            <input type="search" class="form-control form-control-sm col-6" id="article_search" placeholder="Cari artikel / brand / warna"/>
                            </div>
                            <table class="table table-hover table-checkable pr-4" id="TransferBintb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Brand</th>
                                        <th class="text-light">Artikel</th>
                                        <th class="text-light">[Size] [Qty]</th>
                                        <th class="text-light">Transfer</th>
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
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <div class="row">
                                <div class="form-group mb-1 pb-1 col-4">
                                    <a class="btn btn-primary col-12" id="stf_code" style="white-space:nowrap;"></a>
                                </div>
                                <div class="form-group mb-1 pb-1 col-6 row ml-auto">
                                    <a class="btn btn-danger col-4" id="transfer_cancel_btn" style="white-space:nowrap;">DELETE</a>
                                    <a class="btn btn-warning col-4" id="transfer_draft_btn" style="white-space:nowrap;">DRAFT</a>
                                    <a class="btn btn-success col-4" id="transfer_done_btn" style="white-space:nowrap;">DONE</a>
                                </div>
                            </div>
                            <!--end: Datatable-->
                            <div class="row col-12">
                            <input type="search" class="form-control form-control-sm col-12" id="in_transfer_search" placeholder="Cari artikel / brand / warna"/>
                            </div>
                            <table class="table table-hover table-checkable pr-4" id="InTransferBintb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">Artikel</th>
                                        <th class="text-light">BIN</th>
                                        <th class="text-light">Qty</th>
                                        <th class="text-light">Asal</th>
                                        <th class="text-light">Tujuan</th>
                                        <th class="text-light"></th>
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
                <div class="col-lg-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <!--begin::Header-->
                        <div class="card-header h-auto">
                            <!--begin::Title-->
                            <div class="card-title py-5">
                                <h3 class="card-label">History Transfer</h3>
                            </div>
                            <!--end::Title-->
                        </div>
                        <!--end::Header-->
                        <div class="card-body table-responsive">
                            <div class="row d-flex justify-content-between">
                            <select class="form-control col-5" id="status_filter" name="status_filter" required>
                                <option value="">- Status -</option>
                                <option value="1">In Progress</option>
                                <option value="2">Done</option>
                                <option value="3">Draft</option>
                            </select>
                            <input type="search" class="form-control form-control-sm col-5" id="history_search" placeholder="Cari user / artikel"/>
                            </div>
                            <table class="table table-hover table-checkable" id="TransferHistorytb">
                                <thead class="bg-primary text-light">
                                    <tr>
                                        <th class="text-light">No</th>
                                        <th class="text-light">Kode</th>
                                        <th class="text-light">User</th>
                                        <th class="text-light" style="white-space: nowrap;">Qty</th>
                                        <th class="text-light" style="white-space: nowrap;">Store Awal</th>
                                        <th class="text-light" style="white-space: nowrap;">Store Tujuan</th>
                                        <th class="text-light">Penerima</th>
                                        <th class="text-light" style="white-space: nowrap;">Tanggal</th>
                                        <th class="text-light">Status</th>
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
@include('app.stock_transfer.stock_transfer_modal')
@include('app._partials.js')
@include('app.stock_transfer.stock_transfer_js')
@endSection()