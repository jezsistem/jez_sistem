@extends('app.structure')
@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-xxl-12">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b">
                            <Label style="margin-left: 20px; margin-top: 20px;">
                                <h5 class="text-dark font-weight-bold my-1 mr-5">Cek Dana Online</h5>
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
                                                     xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                     height="24px"
                                                     viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path
                                                                d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                                fill="#000000" opacity="0.3"/>
                                                        <path
                                                                d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                                fill="#000000"/>
                                                    </g>
                                                </svg>
                                            </span>Import
                                        </button>
                                    </div>
                                    <div class="dropdown dropdown-inline mr-2">
                                        <button type="button" class="btn btn-primary font-weight-bolder"
                                                aria-expanded="false" id="export_btn">
                                            <span class="svg-icon svg-icon-md">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                     height="24px"
                                                     viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"/>
                                                        <path
                                                                d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                                fill="#000000" opacity="0.3"/>
                                                        <path
                                                                d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                                fill="#000000"/>
                                                    </g>
                                                </svg>
                                            </span>Export
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="container mt-6">
                                <div class="row">
                                    <div class="col-3">
                                        <input type="search" class="form-control" id="cek_dana_online_search"
                                               placeholder="Cari No Order / No resi"/>
                                    </div>
                                    <div class="col-2">
                                        <select class="form-control border" id="st_id" name="st_id">
                                            <option value="">- Pilih Store -</option>
                                            @foreach ($data['st_id'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <div id="st_id_parent"></div>
                                    </div>
                                    <div class="col-2">
                                        <select name="filter_platform" id="filter_platform" class="form-control">
                                            <option value="">-- Pilih Platform --</option>
                                            <option value="tiktok">Tiktok</option>
                                            <option value="shopee">Shopee</option>
                                        </select>
                                        <div id="filter_platform_parent"></div>
                                    </div>
                                    <div class="col-2">
                                        <select name="filter_status" id="filter_status" class="form-control">
                                            <option value="">-- Pilih Status Cetak --</option>
                                            <option value="false">Belum di Cetak</option>
                                            <option value="true">Sudah di Cetak</option>
                                        </select>
                                        <div id="filter_status_parent"></div>
                                    </div>
                                </div>
                                <div class="row mt-8 ml-1">
                                    <div class="col-4">
                                        <label for="trx_date_picker" class="font-weight-bold">Tanggal Transaksi:</label>
                                        <a href="#" class="btn btn-date-info font-weight-bold mr-2"
                                            id="trx_date_picker" data-toggle="tooltip"
                                            title="Tanggal Transaksi" data-placement="left">
                                            <span class="font-size-base"
                                                id="trx_date_picker_title">Today</span>
                                            <span class="font-size-base font-weight-bolder"
                                                id="trx_date_picker_date"></span>
                                            <input type="hidden" id="trx_date" />
                                        </a>
                                    </div>
                                    <div class="col-4">
                                        <label for="cash_out_date_picker" class="font-weight-bold">Tanggal Cash Out:</label>
                                        <a href="#" class="btn btn-date-info font-weight-bold mr-2"
                                            id="cash_out_date_picker" data-toggle="tooltip"
                                            title="Tanggal Cash Out" data-placement="left">
                                            <span class="font-size-base"
                                                id="cash_out_date_picker_title">Today</span>
                                            <span class="font-size-base font-weight-bolder"
                                                id="cash_out_date_picker_date"></span>
                                            <input type="hidden" id="cash_out_date" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body" style="overflow-x: auto; width: 100%;">
                                <table class="table table-hover table-checkable" id="CekDanaOnlinetb" style="min-width: 1500px;">
                                    <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Store</th>
                                        <th class="text-dark">Platform Name</th>
                                        <th class="text-dark">No Order</th>
                                        <th class="text-dark">Order Date Settlement</th>
                                        <th class="text-dark">Total Revenue</th>
                                        <th class="text-dark">Total Settlement Amount</th>
                                        <th class="text-dark">Seller voucher discount</th>
                                        <th class="text-dark">Total Fees</th>
                                        <th class="text-dark">Presentase Fee</th>
                                        <th class="text-dark">Presentase Seller Voucher</th>
                                        <th class="text-dark">Tanggal Transaksi</th>
                                        <th class="text-dark">Net Sales Jezpro</th>
                                        <th class="text-dark">Diff Jezpro - MP</th>
                                        <th class="text-dark">Status</th>
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
    <!--end::Content-->
    @include('app._partials.js')
    @include('app.cekdanaonline.cek_dana_online_modal')
    @include('app.cekdanaonline.cek_dana_online_js')
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
