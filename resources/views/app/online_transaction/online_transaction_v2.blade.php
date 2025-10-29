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
            </div>
        </div>
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-xxl-12">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b edit-style">
                            <Label style="margin-left: 20px; margin-top: 20px;">
                                <h5 class="text-dark font-weight-bold my-1 mr-5">Export Laporan</h5>
                            </Label><br>
                            <div class="card-header h-auto align-items-center justify-content-between">
                                <!--begin::Title-->
                                <input type="hidden" id="sales_date" value="" />

                                <div class="card-title py-5">
                                    <select class="form-control" id="branch_trx" name="st_id_filter" required>
                                        <option value="">-- Cabang --</option>
                                        @foreach ($data['st_id'] as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="card-title py-5">
                                    <select class="form-control" id="status_trx">
                                        <option value="">-- Pilih Status Cetak ---</option>
                                        <option value="2">Semua Status</option>
                                        <option value="1">Sudah Cetak</option>
                                        <option value="0">Belum Cetak</option>
                                    </select>
                                </div>

                                <div class="card-title py-5">
                                    <select class="form-control" id="changeplatform">
                                        <option value="">-- Platform ---</option>
                                        <option value="">Semua Platform</option>
                                        <option value="Shopee">Shopee</option>
                                        <option value="TikTok">Tiktok</option>
                                    </select>
                                </div>


                                <div class="" role="alert">
                                    <input type="hidden" id="sales_date" value="" />
                                    <a href="#" class="btn btn-date-info font-weight-bold mr-2 col-12"
                                        id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Filter Tanggal"
                                        data-placement="left">
                                        <span class="text-muted font-size-base font-weight-bold mr-2"
                                            id="kt_dashboard_daterangepicker_title">Today</span>
                                        <span class="text-primary font-size-base font-weight-bolder"
                                            id="kt_dashboard_daterangepicker_date"></span>
                                    </a>
                                </div>&nbsp;

                                <div class="justify-content-center">
                                    <a href="#" class="btn btn-sm btn-success mr-2 col-12" id="sales_online_export">
                                        Export laporan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Card-->
                </div>
            </div>
            <!--end::Container-->
        </div>
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-xxl-12">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b">
                            <Label style="margin-left: 20px; margin-top: 20px;">
                                <h5 class="text-dark font-weight-bold my-1 mr-5">Transaksi</h5>
                            </Label><br>

                            <div class="card-header flex-wrap py-3">
                                <div class="card-toolbar">
                                    <!--begin::Dropdown-->
                                    <div class="dropdown dropdown-inline mr-2">
                                        <button type="button" class="btn btn-light-primary font-weight-bolder"
                                            data-toggle="modal" data-target="#ImportModal" aria-haspopup="true"
                                            aria-expanded="false">
                                            <span class="svg-icon svg-icon-md">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                                    viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                            fill="#000000" opacity="0.3" />
                                                        <path
                                                            d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                            fill="#000000" />
                                                    </g>
                                                </svg>
                                            </span>Import
                                        </button>
                                    </div>


                                    <div class="dropdown dropdown-inline mr-2">
                                        <button type="button" class="btn btn-light-primary font-weight-bolder"
                                            data-toggle="modal" data-target="#ImportResiModal" aria-haspopup="true"
                                            aria-expanded="false">
                                            <span class="svg-icon svg-icon-md">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none"
                                                        fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24" />
                                                        <path
                                                            d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                            fill="#000000" opacity="0.3" />
                                                        <path
                                                            d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                            fill="#000000" />
                                                    </g>
                                                </svg>
                                            </span>Import Resi
                                        </button>
                                    </div>


                                </div>
                            </div>

                            <div class="container mt-6">
                                <div class="row">
                                    <div class="col-3">
                                        <input type="search" class="form-control" id="online_transaction_search"
                                            placeholder="Cari No Order / No resi" />
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-2">
                                        <select name="filter_statuss" id="filter_status" class="form-control col-6">
                                            <option value="">-- Pilih Status Cetak --</option>
                                            <option value="0">Belum di Cetak</option>
                                            <option value="1">Sudah di Cetak</option>
                                        </select>
                                        <div id="filter_status_parent"></div>
                                    </div>
                                    <div class="col-2">
                                        <select name="filter_status_chat" id="filter_status_chat"
                                            class="form-control col-6">
                                            <option value="">-- Pilih Status Chat --</option>
                                            <option value="unreaded">Belum di Baca</option>
                                            <option value="readed">Sudah di Betak</option>
                                        </select>
                                        <div id="filter_status_chat_parent"></div>
                                    </div>
                                    <div class="col-2">
                                        <select name="filter_warehouse" id="filter_warehouse" class="form-control col-6">
                                            <option value="">-- Pilih Warehouse --</option>
                                            @foreach ($data['warehouses'] as $warehouse)
                                                <option value="{{ $warehouse->w_code }}">{{ $warehouse->w_code }}</option>
                                            @endforeach
                                        </select>
                                        <div id="filter_warehouse_parent"></div>
                                    </div>
                                    <div class="col-2">
                                        <select name="filter_courier" id="filter_courier" class="form-control col-6">
                                            <option value="">-- Pilih Ekspedisi --</option>
                                            <option value="J&T">J&T</option>
                                            <option value="JNE">JNE</option>
                                            <option value="SPX">SPX</option>
                                            <option value="Gojek">Gojek</option>
                                        </select>
                                        <div id="filter_courier_parent"></div>
                                    </div>
                                    <div class="col-2">
                                        <select name="filter_platform" id="filter_platform" class="form-control col-6">
                                            <option value="">-- Pilih Platform --</option>
                                            <option value="Shopee">Shopee</option>
                                            <option value="TikTok">TikTok</option>
                                        </select>
                                        <div id="filter_platform_parent"></div>
                                    </div>
                                </div>
                            </div>


                            <div class="card-body table-responsive">
                                <div class="card-header pb-0">
                                    <ul class="nav nav-tabs card-header-tabs" id="trxTabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-status="" href="#">All TRX</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-status="INSTANT" href="#">Instant</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-status="NEW TRX" href="#">New TRX</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-status="WAITING ONLINE" href="#">Waiting
                                                Online</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-status="UNDER REVIEW" href="#">Under
                                                Review</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-status="WAITING RECEIPT" href="#">Waiting
                                                Receipt</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-status="WAITING PACKING" href="#">Waiting
                                                Packing</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-status="DONE" href="#">Done</a>
                                        </li>
                                    </ul>
                                </div>
                                <input type="hidden" id="tab_status" value="">
                                <table class="table table-hover table-checkable" id="OnlineTransactionb">
                                    <thead class="bg-light text-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Nomer Order</th>
                                            <th>Nomer Resi</th>
                                            <th>Platform Name</th>
                                            <th>Tanggal Order</th>
                                            <th>Item</th>
                                            <th>Ongkos Kirim</th>
                                            <th>Ekspedisi</th>
                                            <th>Total Pembayaran</th>
                                            <th>Status Pengiriman</th>
                                            <th>Status TRX</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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

    <style>
        #loader .loading-text {
            position: absolute;
            top: 60%;
            left: 50%;
            margin-top: 20px;
            transform: translateX(-50%);
            font-size: 1.5em;
            font-weight: bold;
            color: #333;
            font-family: Arial, sans-serif;
        }

        /* Dot animation */
        #loader .dots::after {
            content: "";
            animation: dots 1s steps(4, end) infinite;
        }


        @keyframes dots {

            0%,
            100% {
                content: "";
            }

            25% {
                content: ".";
            }

            50% {
                content: "..";
            }

            75% {
                content: "...";
            }
        }
    </style>


    <div id="loader"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:9999; text-align:center;">
        <img src="{{ asset('pos') }}/jez.gif" alt="loading" width="20%"
            style="position:absolute; top:45%; left:50%; transform:translate(-50%, -50%); background-color:white; padding:10px; border-radius:10px;">
        <div class="loading-text">Loading<span class="dots">...</span></div>
    </div>
    <!--end::Content-->
    @include('app.online_transaction.online_transaction_modal')
    @include('app._partials.js')
    @include('app.online_transaction.online_transaction_js')
@endSection()


<style>
    .card.card-custom.gutter-b.edit-style {
        background-color: #fff0f4;
        /* Ganti warna ini dengan warna yang Anda inginkan */
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        border: 1px solid #dee2e6;
    }

    .table-responsive {
        overflow-x: auto;
        /* Mengaktifkan scroll horizontal */
        -webkit-overflow-scrolling: touch;
        /* Menambahkan smooth scrolling pada perangkat sentuh */
    }
</style>
