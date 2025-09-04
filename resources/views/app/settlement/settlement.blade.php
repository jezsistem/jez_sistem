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
                                <h4 class="text-dark font-weight-bold mb-8">Filter Transaksi</h4>

                                <div class="row">
                                    <div class="col-md-2 mb-4">
                                        <label class="form-label">Tanggal Mulai</label>
                                        <input type="date" id="start_date" class="form-control">
                                    </div>
                                    <div class="col-md-2 mb-4">
                                        <label class="form-label">Tanggal Akhir</label>
                                        <input type="date" id="end_date" class="form-control">
                                    </div>
                                    <div class="col-md-2 mb-4" id="payment_method">
                                    </div>
                                    <div class="col-md-2 mb-4">
                                        <label class="form-label">Outlet</label>
                                        <select class="form-control border border-secondary" id="st_id" onchange="loadPaymentMethods()">
                                            <option value="">-- Pilih Outlet --</option>
                                            @forelse ($data['st_id'] as $storeId => $storeName)
                                                <option value="{{ $storeId }}">{{ $storeName }}</option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-4">
                                        <label class="form-label">Status</label>
                                        <select class="form-control border border-secondary" id="status_trx">
                                            <option value="">-- Pilih Status --</option>
                                            @forelse ($data['statusses'] as $status)
                                                <option value="{{ $status }}">{{ $status }}</option>
                                            @empty
                                                
                                            @endforelse
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-4">
                                        <button type="button" class="btn btn-primary mr-2" id="filter_btn">
                                            <i class="fas fa-filter mr-2"></i>Filter
                                        </button>
                                        <button type="button" class="btn btn-secondary mr-2" id="reset_btn">
                                            <i class="fas fa-undo mr-2"></i>Reset
                                        </button>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-download mr-2"></i>Export
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#">Export Transaction</a>
                                                <a class="dropdown-item" href="#">Export Detail Transaction</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-custom gutter-b">

                            <div class="card-body table-responsive">
                                <div class="d-flex justify-content-between">
                                    <div class="">
                                        <h3>Total Net Sales</h3>
                                        <h1 class="text-success">Rp. <span id="total_netsales">0</span></h1>
                                    </div>
                                    <div class="">
                                        <p>Selected for settlement: <span id="selected">0</span> transaction</p>
                                        <p class="mb-2"><strong>Selected Net Sales:</strong> <span class="text-success font-weight-bold">Rp</span> <span class="text-success font-weight-bold" id="selected_netsales">0</span</p>
                                    </div>
                                    <div class="h-100 d-flex align-items-center">
                                        <button class="btn btn-primary px-6" id="settlement_btn"><i class="fas fa-check mr-2"></i>Settlement</button>
                                    </div>
                                </div>
                                <hr>
                                <p class="text-dark font-weight-bold mt-5 mb-5">Net Sales by Payment Method</p>
                                <div id="payment_calc_cards" class="mb-2">
                                </div>
                                <!--begin: Datatable-->
                                <table class="table table-hover table-checkable" id="SettlementTable">
                                    <thead class="bg-light text-dark">
                                        <tr>
                                            <th> <input type="checkbox" name="check_all_data" id="check_all_data"></th>
                                            <th class="text-dark">Tanggal Transaksi</th>
                                            <th class="text-dark">Receipt Number</th>
                                            <th class="text-dark">Outlet</th>
                                            <th class="text-dark">Qty</th>
                                            <th class="text-dark">Net Sales</th>
                                            <th class="text-dark">Payment Method</th>
                                            <th class="text-dark">Sub Payment</th>
                                            <th class="text-dark">Status TRX</th>
                                            <th class="text-dark">Status Settle</th>
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
    @include('app.settlement.settlement_modal')
    @include('app._partials.js')
    @include('app.settlement.settlement_js')
@endSection()
