@extends('app.structure')
@section('content')
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader" style="background:#efefef;">
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
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <!-- if ($data['user']->g_name == 'administrator') -->
                        <div class="card-header flex-wrap py-3">
                            <div class="card-toolbar">
                            <select class="form-control" id="st_id" name="st_id" required>
                                <option value="" selected>- Pilih -</option>
                                @foreach ($data['st_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        <!-- endif -->
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <input type="search" class="form-control  col-6" id="data_search" placeholder="Cari artikel"/><br/>
                            <table class="table table-hover table-checkable" id="Datatb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Tanggal</th>
                                        <th class="text-dark">Store</th>
                                        <th class="text-dark">Helper</th>
                                        <th class="text-dark">Artikel</th>
                                        <th class="text-dark">Qty</th>
                                        <th class="text-dark">BIN</th>
                                        <th class="text-dark">Approval</th>
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

                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <!-- if ($data['user']->g_name == 'administrator') -->
                        <div class="card-header flex-wrap py-3">
                            <div class="card-toolbar">
                            <select class="form-control" id="st_id_history" name="st_id_history" required>
                                <option value="" selected>- Pilih -</option>
                                @foreach ($data['st_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        <!-- endif -->
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <input type="search" class="form-control  col-6" id="history_data_search" placeholder="Cari artikel"/><br/>
                            <table class="table table-hover table-checkable" id="HistoryDatatb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Tanggal</th>
                                        <th class="text-dark">Store</th>
                                        <th class="text-dark">Helper</th>
                                        <th class="text-dark">Artikel</th>
                                        <th class="text-dark">Qty</th>
                                        <th class="text-dark">BIN</th>
                                        <th class="text-dark">Approval</th>
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
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->
@include('app._partials.js')
@include('app.instock_list.instock_list_js')
@endSection()
