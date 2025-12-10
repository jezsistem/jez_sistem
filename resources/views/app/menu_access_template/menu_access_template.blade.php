@extends('app.structure')
@section('content')
    <!--begin::Content-->
    <style>
        .gap-2>* {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .menu-item.active {
            display: none;
        }
    </style>
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
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label"></h3>
                                </div>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-primary font-weight-bolder" id="open_modal_create">
                                        <i class="fas fa-plus"></i> Create Template
                                    </button>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <!--begin: Datatable-->
                                <table class="table table-bordered table-hover" id="templateTable">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Template Name</th>
                                            <th>Division</th>
                                            <th>Description</th>
                                            <th width="15%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTable will populate this -->
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



    @include('app.menu_access_template.menu_access_template_modal')

    @include('app._partials.js')
    @include('app.menu_access_template.menu_access_template_js')
@endSection
