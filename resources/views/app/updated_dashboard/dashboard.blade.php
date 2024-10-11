@extends('app.structure')
@section('content')
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <!--begin::Subheader-->
        <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <!--begin::Info-->
                <div class="d-flex align-items-center flex-wrap mr-2">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5 fs-3">{{ $data['subtitle'] }}</h5>
                    <!--end::Page Title-->
                </div>
                <!--end::Info-->
                <!--begin::Toolbar-->
                <div class="d-flex align-items-center">
                    <!--begin::Daterange-->
                    <a href="{{ url('dashboard') }}" class="d-flex align-items-center btn btn-inventory mr-2"><i
                            class="ki-outline ki-arrow-up-left pr-0 fs-3"><i class="path1"></i><i class="path2"></i></i>
                        Dashboard V1</a>

                    <a href="{{ url('asset_detail') }}" class="btn btn-dark mr-2">
                        Detail Asset/Penjualan
                    </a>

                    <a href="#" class="btn btn-primary d-flex align-items-center" id="detail_activity_btn">
                        <i class="ki-outline ki-questionnaire-tablet pr-0">
                            <i class="path1"></i>
                            <i class="path2"></i>
                        </i>
                        Aktifitas User
                    </a>

                </div>
                <!--end::Toolbar-->
            </div>
        </div>
        <!--end::Subheader-->

        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">
                <!--begin::Dashboard-->
                <!--begin::Row-->
                <div class="row">
                    <div class="col-lg-12 col-xxl-12" id="user_activity_reload">
                        <!--begin::List Widget 9-->
                        <div class="card card-custom card-stretch gutter-b">
                            <!--begin::Body-->




                            <div class="card-body pt-6">
                                <!--begin::Timeline-->
                                <div class="d-flex justify-content-center">
                                    <div class="w-25 mr-1">
                                        <label class="form-label text-dark">GRAFIK</label>
                                        <div class="select-card-lg">
                                            <select class="form-control" id="info_filter">
                                                <option value="brand">Grafik Brand</option>
                                                <option value="store">Grafik Store</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="w-25 mr-3">
                                        <label class="form-label">DIVISI</label>
                                        <div class="select-card-lg">
                                            <select class="form-control text-dark" id="division_filter">
                                                <option value='all'>- Semua Divisi -</option>
                                                <option value="online">Online</option>
                                                <option value="offline">Offline</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class=" d-flex col-3 offset-3 w-20 justify-content-end" style="height:fit-content">
                                        <input type="hidden" id="dashboard_date" value="" />
                                        <a class="btn btn-date-info font-weight-bold" id="kt_dashboard_daterangepicker"
                                            data-toggle="tooltip" title="Select dashboard daterange" data-placement="left"
                                            style="cursor:pointer;">
                                            <span class="font-size-base"
                                                id="kt_dashboard_daterangepicker_title">Today</span>
                                            <span class="font-size-base font-weight-bolder"
                                                id="kt_dashboard_daterangepicker_date"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                fill="currentColor" class="bi bi-caret-down-fill" viewBox="0 0 16 16">
                                                <path
                                                    d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                <label class="d-flex form-label mt-4">STORE</label>
                                <div class="row d-flex justify-content-center">
                                        @foreach ($data['st_id'] as $key => $value)
                                            <a class="btn btn-sm btn-light-primary col-3 st_selection w-10"
                                                data-id="{{ $key }}">{{ $value }}</a>
                                        @endforeach
                                </div>


                                <!--end::Timeline-->
                            </div>





                            <!--end: Card Body-->
                        </div>
                        <!--end: List Widget 9-->
                    </div>
                </div>




                <div class="row gy-5 g-xl-5">


                    <!--begin::Col-->
                    <div class="col-sm-6 col-xl-3 mb-xl-6">
                        <!--begin::Card widget 2-->
                        <div class="card h-lg-100" id="nett_sales_btn">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-outline ki-financial-schedule bg-icon text-gray-600"></i>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-5 mb-0">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="adm_nett_sales_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">Non Admin</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-3">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="nett_sales_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">Net Sales</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card widget 2-->
                    </div>
                    <!--end::Col-->


                    <!--begin::Col-->
                    <div class="col-sm-6 col-xl-3 mb-xl-6">
                        <!--begin::Card widget 2-->
                        <div class="card h-lg-100" id="profits_btn">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-outline ki-wallet bg-icon text-gray-600"></i>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-5 mb-0">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="adm_profits_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">Non Admin</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-3">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="profits_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">Net Profit</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card widget 2-->
                    </div>
                    <!--end::Col-->


                    <!--begin::Col-->
                    <div class="col-sm-6 col-xl-3 mb-xl-6">
                        <!--begin::Card widget 2-->
                        <div class="card h-lg-100" id="cross_nett_sales_btn">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-outline ki-graph-up bg-icon text-gray-600"></i>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-5 mb-0">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="adm_cross_nett_sales_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">Non Admin</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-3">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="cross_nett_sales_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">Cross Order</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card widget 2-->
                    </div>
                    <!--end::Col-->



                    <!--begin::Col-->
                    <div class="col-sm-6 col-xl-3 mb-xl-6">
                        <!--begin::Card widget 2-->
                        <div class="card h-lg-100" id="cross_profits_btn">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-outline ki-bill bg-icon text-gray-600"></i>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-5 mb-0">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="adm_cross_profits_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">Non Admin</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-3">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="cross_profits_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">Cross Order Profit</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card widget 2-->
                    </div>
                    <!--end::Col-->


                    <div class="col-sm-6 col-xl-3 mb-xl-6">
                        <!--begin::Card widget 2-->
                        <div class="card h-lg-100" id="cc_assets_btn">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-outline ki-financial-schedule bg-icon text-gray-600"></i>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-5 mb-0">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="cc_assets_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">CC Assets</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card widget 2-->
                    </div>
                    <!--end::Col-->


                    <div class="col-sm-6 col-xl-3 mb-xl-6">
                        <!--begin::Card widget 2-->
                        <div class="card h-lg-100" id="c_assets_btn">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-outline ki-financial-schedule bg-icon text-gray-600"></i>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-5 mb-0">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="c_assets_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">C Assets</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card widget 2-->
                    </div>
                    <!--end::Col-->


                    <div class="col-sm-6 col-xl-3 mb-xl-6">
                        <!--begin::Card widget 2-->
                        <div class="card h-lg-100" id="purchases_btn">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-outline ki-financial-schedule bg-icon text-gray-600"></i>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-5 mb-0">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="purchases_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">Pembelian</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card widget 2-->
                    </div>
                    <!--end::Col-->


                    <div class="col-sm-6 col-xl-3 mb-xl-6">
                        <!--begin::Card widget 2-->
                        <div class="card h-lg-100" id="debts_btn">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-outline ki-financial-schedule bg-icon text-gray-600"></i>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-5 mb-0">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="debts_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">Hutang</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card widget 2-->
                    </div>
                    <!--end::Col-->


                    <div class="col-sm-6 col-xl-3 mb-xl-6">
                        <!--begin::Card widget 2-->
                        <div class="card h-lg-100" id="cc_exc_assets_btn">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-outline ki-financial-schedule bg-icon text-gray-600"></i>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-5 mb-0">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="cc_exc_assets_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">CC Exception Assets</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card widget 2-->
                    </div>
                    <!--end::Col-->


                    <div class="col-sm-6 col-xl-3 mb-xl-6">
                        <!--begin::Card widget 2-->
                        <div class="card h-lg-100" id="c_exc_assets_btn">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-outline ki-financial-schedule bg-icon text-gray-600"></i>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-5 mb-0">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="c_exc_assets_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">C Exception Assets</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card widget 2-->
                    </div>
                    <!--end::Col-->


                    <div class="col-sm-6 col-xl-3 mb-xl-6">
                        <!--begin::Card widget 2-->
                        <div class="card h-lg-100" id="gid_btn">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-outline ki-financial-schedule bg-icon text-gray-600"></i>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-5 mb-0">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="gid_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">Goods In Draft</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>
                                <!--end::Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card widget 2-->
                    </div>
                    <!--end::Col-->


                    <div class="col-sm-6 col-xl-3 mb-xl-6">
                        <!--begin::Card widget 2-->
                        <div class="card h-lg-100" id="git_btn">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-start flex-column">
                                <!--begin::Icon-->
                                <div class="m-0">
                                    <i class="ki-outline ki-financial-schedule bg-icon text-gray-600"></i>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Section-->
                                <div class="d-flex flex-column mt-5 mb-0">
                                    <!--begin::Number-->
                                    <span class="font-weight-semibold sz-font text-gray-800 lh-1 ls-n2"
                                        id="git_label"></span>
                                    <!--end::Number-->
                                    <!--begin::Follower-->
                                    <div class="m-0">
                                        <span class="fw-semibold fs-7 text-gray-400">Goods In Transit</span>
                                    </div>
                                    <!--end::Follower-->
                                </div>

                                <!--end::Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Card widget 2-->
                    </div>


                    <!--end::Col-->
                </div>

                <div class="row">
                    <div class="col-lg-12 col-xxl-12 d-none" style="min-height: 315px;" id="graph_panel">
                        <!--begin::Stats Widget 12-->
                        <div class="card card-custom card-stretch gutter-b" style="border-radius:.625rem;">
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
                                    <span class="btn-sm btn-primary font-weight-bolder">
                                        Graph
                                    </span>
                                </div>
                                <div id="chart"></div>
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Stats Widget 12-->
                    </div>
                    <div class="col-lg-12 col-xxl-12" id="loadTable"></div>
                    <!-- INCOMING STOCK -->
                </div>

                <!--end::Row-->
                <!--end::Dashboard-->
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
    <!--end::Content-->



    <style>
        .card {
            background-color: #ffeded;
            border-radius: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card-body {
            padding: 1.5rem;
        }

        .bg-icon {
            background-color: #ff7671;
            padding: 10px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .text-gray-600 {
            color: #2e2f30;
        }


        .sz-font {
            font-size: 18px;
        }

        .text-gray-800 {
            color: #343a40;
        }

        .text-gray-400 {
            color: #89919f;
        }

        .fw-semibold {
            font-weight: 600;
        }

        .lh-1 {
            line-height: 1;
        }

        .ls-n2 {
            letter-spacing: -0.02em;
        }

        .m-0 {
            margin: 0;
        }

        .mt-5 {
            margin-top: 1.5rem;
        }

        .mb-0 {
            margin-bottom: 0;
        }
    </style>







    @include('app.updated_dashboard.dashboard_modal')
    @include('app._partials.js')
    @include('app.updated_dashboard.dashboard_js')
@endSection()
