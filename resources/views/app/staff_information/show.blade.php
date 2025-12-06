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
                <div class="d-flex align-items-center">
                    <a href="{{ route('staff-information.index') }}" class="btn btn-light-primary font-weight-bolder">
                        <i class="ki-outline ki-arrow-left"></i>Back
                    </a>
                </div>
            </div>
        </div>
        <!--end::Subheader-->
        <!--begin::Entry-->
        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">
                <div class="row">
                    <div class="col-lg-4">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">Profile Photo</h3>
                                </div>
                            </div>
                            <div class="card-body text-center">
                                <img src="{{ $staff->u_photo ? Storage::disk('s3')->url($staff->u_photo) : asset('photos/no_image.png') }}" 
                                     alt="Photo" 
                                     class="img-fluid rounded"
                                     style="max-height: 300px;">
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                    <div class="col-lg-8">
                        <!--begin::Card-->
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">Personal Information</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label font-weight-bold">Name:</label>
                                    <div class="col-lg-8">
                                        <span class="form-control-plaintext">{{ $staff->u_name }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label font-weight-bold">Birthday:</label>
                                    <div class="col-lg-8">
                                        <span class="form-control-plaintext">{{ $staff->u_birthday }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label font-weight-bold">Address:</label>
                                    <div class="col-lg-8">
                                        <span class="form-control-plaintext">{{ $staff->u_address }}</span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-form-label font-weight-bold">Position:</label>
                                    <div class="col-lg-8">
                                        <span class="form-control-plaintext">{{ $staff->up_name ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label class="col-lg-4 col-form-label font-weight-bold">Division:</label>
                                    <div class="col-lg-8">
                                        <span class="form-control-plaintext">{{ $staff->ud_name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <!--begin::Card KTP-->
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">KTP</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">KTP Number:</label>
                                    <p>{{ $staff->u_ktp ?? '-' }}</p>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">KTP Image:</label>
                                    <div class="mt-2">
                                        <img src="{{ $staff->u_ktp_image ? Storage::disk('s3')->url($staff->u_ktp_image) : asset('photos/no_image.png') }}" 
                                             alt="KTP" 
                                             class="img-fluid rounded border"
                                             style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                    <div class="col-lg-6">
                        <!--begin::Card NPWP-->
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">NPWP</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">NPWP Number:</label>
                                    <p>{{ $staff->u_npwp ?? '-' }}</p>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">NPWP Image:</label>
                                    <div class="mt-2">
                                        <img src="{{ $staff->u_npwp_image ? Storage::disk('s3')->url($staff->u_npwp_image) : asset('photos/no_image.png') }}" 
                                             alt="NPWP" 
                                             class="img-fluid rounded border"
                                             style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <!--begin::Card BPJS Kesehatan-->
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">BPJS Kesehatan</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">BPJS Kesehatan Number:</label>
                                    <p>{{ $staff->u_bpjs_kes_number ?? '-' }}</p>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">BPJS Kesehatan Image:</label>
                                    <div class="mt-2">
                                        <img src="{{ $staff->u_bpjs_kes_image ? Storage::disk('s3')->url($staff->u_bpjs_kes_image) : asset('photos/no_image.png') }}" 
                                             alt="BPJS Kesehatan" 
                                             class="img-fluid rounded border"
                                             style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                    <div class="col-lg-6">
                        <!--begin::Card BPJS Ketenagakerjaan-->
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">BPJS Ketenagakerjaan</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">BPJS Ketenagakerjaan Number:</label>
                                    <p>{{ $staff->u_bpjs_tk_number ?? '-' }}</p>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold">BPJS Ketenagakerjaan Image:</label>
                                    <div class="mt-2">
                                        <img src="{{ $staff->u_bpjs_tk_image ? Storage::disk('s3')->url($staff->u_bpjs_tk_image) : asset('photos/no_image.png') }}" 
                                             alt="BPJS Ketenagakerjaan" 
                                             class="img-fluid rounded border"
                                             style="max-height: 200px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Card-->
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <!--begin::Card Bank-->
                        <div class="card card-custom gutter-b">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="card-label">Bank Information</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Bank Name:</label>
                                            <p>{{ $staff->u_bank_name ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Account Number:</label>
                                            <p>{{ $staff->u_bank_account_number ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="font-weight-bold">Account Holder:</label>
                                            <p>{{ $staff->u_bank_account_holder ?? '-' }}</p>
                                        </div>
                                    </div>
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
    @include('app._partials.js')
@endSection()
