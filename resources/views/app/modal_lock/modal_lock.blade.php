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
                                <div class="mb-3">
                                    <button type="button" class="btn btn-primary" id="btn_modal_config">
                                        <i class="fas fa-cog"></i> Config
                                    </button>
                                    <button type="button" class="btn btn-success ml-2" id="btn_modal_allowed_models">
                                        <i class="fas fa-list"></i> Allowed Model
                                    </button>
                                </div>
                                <!--begin: Datatable-->
                                <input type="search" class="form-control  col-6" id="modal_lock_search"
                                    placeholder="Cari Lock" /><br />
                                <table class="table table-hover table-checkable" id="ModalLocktb">
                                    <thead class="bg-light text-dark">
                                        <tr>
                                            <th class="text-dark">No</th>
                                            <th class="text-dark">User</th>
                                            <th class="text-dark">ID Model</th>
                                            <th class="text-dark">Type</th>
                                            <th class="text-dark">Identifier</th>
                                            <th class="text-dark">Expires At</th>
                                            <th class="text-dark">Action</th>
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
    @include('app.modal_lock.modal_lock_modal')
    @include('app._partials.js')
    @include('app.modal_lock.modal_lock_js')
@endSection()
