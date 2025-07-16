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
        <!--begin::Body-->
        <div class="card-body p-0">
            <!--begin::Stats-->
            <div class="card-spacer" style="padding-top: 0px;">
                <!--begin::Row-->
                <div class="row m-0">
                    <div class="col-lg-6 col-xxl-6" id="user_activity_reload">
                        <!--begin::List Widget 9-->
                        <div class="card card-custom card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header align-items-center border-0 mt-4">
                                <h3 class="card-title align-items-start flex-column">
                                    Scan Items
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pt-4 gap-2">
                                <div class="mb-2">
                                    <label for="bin_filter" class="form-label">Pilih Bin</label>
                                    <select class="form-control mt-2 bg-primary text-white" id="bin_filter"
                                            name="bin_filter">
                                        <option value=''>- Pilih Store Terlebih Dahulu -</option>
                                    </select>
                                    <div id="bin_filter_parent"></div>
                                </div>
                                <div class="mt-6 mb-2">
                                    <label for="scan_sku" class="form-label">Scan SKU</label>
                                    <input type="text" id="scan_sku" name="scan_sku" class="form-control"
                                           placeholder="Scan SKU disini">
                                </div>

                                <!-- Tombol Reset -->
                                <div class="mt-12 text-end">
                                    <button type="button" class="btn btn-secondary" id="reset_btn">Reset</button>
{{--                                    <button type="button" class="btn btn-secondary" id="scan_btn">Submit</button>--}}
                                </div>

                                <br/>
                                <!--end::Timeline-->
                            </div>
                            <!--end: Card Body-->
                        </div>
                        <!--end: List Widget 9-->
                    </div>

                    {{--                    Detail Item --}}
                    <div class="col-lg-6 col-xxl-6" id="items_details">
                        <!--begin::List Widget 9-->
                        <div class="card card-custom card-stretch gutter-b">
                            <!--begin::Header-->
                            <div class="card-header align-items-center border-0 mt-4">
                                <h3 class="card-title align-items-start flex-column">
                                    Items Detail
                                </h3>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pt-4 gap-2">
                                <div class="mb-2">
                                    <label for="p_name" class="form-label">Article Name</label>
                                    <input type="text" id="p_name" name="p_name" class="form-control" disabled>
                                </div>
                                <div class="mt-6 mb-2">
                                    <label for="sz_name" class="form-label">Variant</label>
                                    <input type="text" id="sz_name" name="sz_name" class="form-control" disabled>
                                </div>
                                <div class="mt-6 mb-2">
                                    <label for="pl_code" class="form-label">Bin</label>
                                    <input type="text" id="pl_code" name="pl_code" class="form-control" disabled>
                                </div>
                                <div class="mt-6 mb-2">
                                    <label for="pls_qty" class="form-label">QTY</label>
                                    <input type="text" id="pls_qty" name="pls_qty" class="form-control" disabled>
                                </div>
                                <br/>
                                <!--end::Timeline-->
                            </div>
                            <!--end: Card Body-->
                        </div>
                        <!--end: List Widget 9-->
                    </div>
                </div>
                <!--end::Row-->
            </div>
            <!--end::Stats-->
        </div>
        <!--end::Body-->
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b">
                            <!--begin::Header-->
                            <div class="card-header h-auto align-items-center justify-content-between">
                                <!--begin::Dropdown-->
                                <a class="btn btn-primary mt-2" id="export_btn">Export Template</a>
                                <!--end::Dropdown-->
                            </div>
                            <div class="card-body table-responsive">
                                <input type="search" class="form-control  col-6" id="stock_search"
                                       placeholder="Cari stok"/><br/>
                                <table class="table table-hover table-checkable" id="Stocktb">
                                    <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">BIN</th>
                                        <th class="text-dark">BRAND</th>
                                        <th class="text-dark">ARTIKEL</th>
                                        <th class="text-dark">WARNA</th>
                                        <th class="text-dark">SIZE</th>
                                        <th class="text-dark">Sub Kategori</th>
                                        <th class="text-dark">Stok</th>
                                        <th class="text-dark">Harga Beli</th>
                                        <th class="text-dark">Harga Jual</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--begin::Card-->

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
                        #loader_download .dots::after {
                            content: "";
                            animation: dots 1s steps(4, end) infinite;
                        }

                        #loader_download .loading-text {
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
                        <div class="loading-text">Load all history item Adjustment ....<span class="dots">...</span>
                        </div>
                    </div>

                    <div id="loader_download"
                         style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:9999; text-align:center;">
                        <img src="{{ asset('pos') }}/jez.gif" alt="loading" width="20%"
                             style="position:absolute; top:45%; left:50%; transform:translate(-50%, -50%); background-color:white; padding:10px; border-radius:10px;">
                        <div class="loading-text">Download History item Adjustment ....<span class="dots">...</span>
                        </div>
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
    @include('app.cycle_counts.cycle_count_modal')
    @include('app._partials.js')
    @include('app.cycle_counts.cycle_count_js')
@endSection()
