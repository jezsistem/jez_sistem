
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
                <div class="row">
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
                            <div class="card-header flex-wrap py-3" style="background:#ffe6e6;">
                                <div class="card-toolbar col-12 mt-4">
                                    <!--begin::Dropdown-->
                                    <div class="dropdown dropdown-inline col-xl-4 col-xxl-4 mt-5">
                                        <select class="form-control col-md-12" id="pc_id">
                                            @foreach ($data['pc_id'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <div id="pc_id_parent"></div>
                                    </div>
                                    <!--end::Dropdown-->
                                    <!--begin::Dropdown-->
                                    <div class="dropdown dropdown-inline col-xl-4 col-xxl-4 mt-5">
                                        <select class="form-control col-md-12" id="psc_id">
                                            @foreach ($data['psc_id'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <div id="psc_id_parent"></div>
                                    </div>
                                    <!--end::Dropdown-->
                                    <!--begin::Dropdown-->
                                    <div class="dropdown dropdown-inline col-xl-4 col-xxl-4 mt-5">
                                        <select class="form-control col-md-12" id="pssc_id">
                                            @foreach ($data['pssc_id'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <div id="pssc_id_parent"></div>
                                    </div>
                                    <!--end::Dropdown-->
                                    <!--begin::Dropdown-->
                                    <div class="dropdown dropdown-inline col-xl-4 col-xxl-4 mt-5">
                                        <select class="form-control col-md-12" id="br_id">
                                            @foreach ($data['br_id'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <div id="br_id_parent"></div>
                                    </div>
                                    <!--end::Dropdown-->
                                    <!--begin::Dropdown-->
                                    <div class="dropdown dropdown-inline col-xl-4 col-xxl-4 mt-5">
                                        <select class="form-control col-md-12" id="sz_id">
                                            @foreach ($data['sz_id'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <div id="sz_id_parent"></div>
                                    </div>
                                    <!--end::Dropdown-->
                                    <!--begin::Dropdown-->
                                    {{-- <div class="dropdown dropdown-inline col-xl-4 col-xxl-4 mt-5">
                                        <select class="form-control col-md-12" id="p_name">
                                            @foreach ($data['p_name'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <div id="p_name_parent"></div>
                                    </div> --}}
                                    <!--end::Dropdown-->
                                    <!--begin::Dropdown-->
                                    <div class="dropdown dropdown-inline col-xl-4 col-xxl-4 mt-5">
                                        <select class="form-control col-md-12" id="main_color_id">
                                            @foreach ($data['main_color_id'] as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                        <div id="main_color_id_parent"></div>
                                    </div>
                                    <!--end::Dropdown-->
                                    <!--begin::Dropdown-->
                                    <div class="dropdown dropdown-inline col-xl-4 col-xxl-4 mt-2">
                                        <button style="white-space: nowrap;" type="button"
                                            class="btn btn-primary font-weight-bolder mb-2" id="reset_btn"
                                            aria-haspopup="true" aria-expanded="false">
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
                                                <!--end::Svg Icon-->
                                            </span>Reset</button>
                                        {{--                                    @if (strtolower($data['user']->stt_name) == 'offline') --}}
                                        <button style="white-space: nowrap;" type="button"
                                            class="btn btn-primary font-weight-bolder mb-2" id="pickup_list_btn"
                                            aria-haspopup="true" aria-expanded="false">
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
                                                <!--end::Svg Icon-->
                                            </span>Pickup List</button>
                                        {{--                                    @endif --}}
                                    </div>
                                    <!--end::Dropdown-->
                                </div>
                            </div>


                            <div class="card-body table-responsive">
                                <form id="f_search">
                                    <div id="reader" class="rounded"></div>
                                    <div id="result"></div>
                                    <input type="text" class="form-control" id="stock_data_search"
                                        placeholder="Ketik 3 huruf pertama Nama Produk atau Nama Artikel"
                                        style="border:1px solid black; padding:20px; background:#efefef;" /><br />
                                    <p>Please don't click any buttons; just wait to get the data you want.</p>

                                    <div class="mt-3">
                                        <button type="button" class="btn btn-secondary btn-sm" id="pickZeroBtn">Stok
                                            Semua Varian
                                        </button>
                                        <button type="button" class="btn btn-primary btn-sm" id="pickAvailableBtn">Stok
                                            Tersedia</button>
                                        <input type="hidden" value="" id="is_zero">
                                    </div>
                                </form>


                                <table class="table table-hover table-checkable" id="StockDatatb">
                                    <thead class="bg-light text-dark">
                                        <tr>
                                            <th class="text-dark" style="width: 80%;">Available Stock
                                            </th>
                                            <th class="hidden"></th>
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
    @include('app.stock_data.stock_data_modal')
    @include('app._partials.js')
    @include('app.stock_data.stock_data_js')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@endSection()

<style>
    #StockDatatb th {
        width: 80%;
        white-space: nowrap;
        padding: 10px;
    }
    #StockDatatb th.hidden {
        width: 20%;
    }
    @media only screen and (max-width: 1080px) {
        .table tbody tr {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }
        .table tbody td {
            display: block;
            text-align: left;
            border-bottom: 1px solid #ddd;
            padding: 10px 5px;
            position: relative;
        }
        .table tbody td:before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            font-weight: bold;
        }
    }
</style>