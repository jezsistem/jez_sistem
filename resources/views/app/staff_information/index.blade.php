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
                            <div class="card-header flex-wrap py-3">
                                <div class="card-toolbar d-flex justify-content-between w-100">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="position_filter">Position</label>
                                            <select class="form-control" id="position_filter">
                                                <option value="">All Positions</option>
                                                @foreach ($positions as $position)
                                                    <option value="{{ $position->id }}">{{ $position->up_code }} -
                                                        {{ $position->up_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="division_filter">Division</label>
                                            <select class="form-control" id="division_filter">
                                                <option value="">All Divisions</option>
                                                @foreach ($divisions as $division)
                                                    <option value="{{ $division->id }}">{{ $division->ud_code }} -
                                                        {{ $division->ud_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <input type="search" class="form-control" style="width: 300px;" id="staff_search"
                                            placeholder="Search" />
                                        <button type="button" class="btn btn-light-green font-weight-bolder ml-2" id="export_btn"><span
                                            class="svg-icon svg-icon-md">
                                            <i class="ki-outline ki-file-down"></i>
                                            </span>Export Excel</button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <table id="StaffInformationtb" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>KTP</th>
                                            <th>NPWP</th>
                                            <th>Birthday</th>
                                            <th>Address</th>
                                            <th>BPJS Kesehatan</th>
                                            <th>BPJS Ketenagakerjaan</th>
                                            <th>Bank Name</th>
                                            <th>Account Number</th>
                                            <th>Bank Holder</th>
                                            <th>Position</th>
                                            <th>Division</th>
                                            <th>Action</th>
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
    @include('app._partials.js')
    @include('app.staff_information.staff_information_js')
@endSection()
