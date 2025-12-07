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
                <form action="{{ route('personal_data.update') }}" method="POST" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <!--begin::Card - Identitas-->
                            <div class="card card-custom gutter-b">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="card-label">Data Identitas</h3>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">NIK</label>
                                        <div class="col-lg-9">
                                            <input type="text form_input" name="nik" class="form-control" placeholder="Masukkan NIK" value="{{ old('nik', $user_data->u_ktp ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Foto KTP</label>
                                        <div class="col-lg-9">
                                            <div class="d-flex align-items-center">
                                                <input type="file" name="ktp_image" class="form-control" accept="image/*">
                                                @if(isset($user_data->u_ktp_image))
                                                    <button type="button" class="btn btn-sm btn-info ml-2" data-toggle="modal" data-target="#modalFotoKTP">
                                                        <i class="fa fa-eye"></i> View
                                                    </button>
                                                @endif
                                            </div>
                                            <small class="form-text text-muted">Format: JPG, PNG (Max: 2MB)</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">NPWP</label>
                                        <div class="col-lg-9">
                                            <input type="text" name="npwp" class="form-control" placeholder="Masukkan NPWP" value="{{ old('npwp', $user_data->u_npwp ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Foto NPWP</label>
                                        <div class="col-lg-9">
                                            <div class="d-flex align-items-center">
                                                <input type="file" name="npwp_image" class="form-control" accept="image/*">
                                                @if(isset($user_data->u_npwp_image))
                                                    <button type="button" class="btn btn-sm btn-info ml-2" data-toggle="modal" data-target="#modalFotoNPWP">
                                                        <i class="fa fa-eye"></i> View
                                                    </button>
                                                @endif
                                            </div>
                                            <small class="form-text text-muted">Format: JPG, PNG (Max: 2MB)</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Tanggal Lahir</label>
                                        <div class="col-lg-9">
                                            <input type="date" name="tanggal_lahir" class="form-control" placeholder="Masukkan tanggal lahir" value="{{ old('tanggal_lahir', $user_data->u_birthday ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-3 col-form-label">Alamat Domisili</label>
                                        <div class="col-lg-9">
                                            <textarea name="alamat_domisili" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">
{{ old('alamat_domisili', $user_data->u_address ?? '') }}
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card - Identitas-->

                            <!--begin::Card - Kepegawaian-->
                            <div class="card card-custom gutter-b">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="card-label">Data Kepegawaian</h3>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Foto Formal</label>
                                        <div class="col-lg-9">
                                            <div class="d-flex align-items-center">
                                                <input type="file" name="foto_formal" class="form-control" accept="image/*">
                                                @if(isset($user_data->u_photo))
                                                    <button type="button" class="btn btn-sm btn-info ml-2" data-toggle="modal" data-target="#modalFotoFormal">
                                                        <i class="fa fa-eye"></i> View
                                                    </button>
                                                @endif
                                            </div>
                                            <small class="form-text text-muted">Format: JPG, PNG (Max: 2MB)</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">No BPJS Kesehatan</label>
                                        <div class="col-lg-9">
                                            <input type="text" name="no_bpjs_kesehatan" class="form-control" placeholder="Masukkan No BPJS Kesehatan" value="{{ old('no_bpjs_kesehatan', $user_data->u_bpjs_kes_number ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-3 col-form-label">Foto BPJS Kesehatan</label>
                                        <div class="col-lg-9">
                                            <div class="d-flex align-items-center">
                                                <input type="file" name="foto_bpjs_kesehatan" class="form-control" accept="image/*">
                                                @if(isset($user_data->u_bpjs_kes_image))
                                                    <button type="button" class="btn btn-sm btn-info ml-2" data-toggle="modal" data-target="#modalFotoBPJSKesehatan">
                                                        <i class="fa fa-eye"></i> View
                                                    </button>
                                                @endif
                                            </div>
                                            <small class="form-text text-muted">Format: JPG, PNG (Max: 2MB)</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">No BPJS Ketenagakerjaan</label>
                                        <div class="col-lg-9">
                                            <input type="text" name="no_bpjs_ketenagakerjaan" class="form-control" placeholder="Masukkan No BPJS Ketenagakerjaan" value="{{ old('no_bpjs_ketenagakerjaan', $user_data->u_bpjs_tk_number ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-3 col-form-label">Foto BPJS Ketenagakerjaan</label>
                                        <div class="col-lg-9">
                                            <div class="d-flex align-items-center">
                                                <input type="file" name="foto_bpjs_ketenagakerjaan" class="form-control" accept="image/*">
                                                @if(isset($user_data->u_bpjs_tk_image))
                                                    <button type="button" class="btn btn-sm btn-info ml-2" data-toggle="modal" data-target="#modalFotoBPJSKetenagakerjaan">
                                                        <i class="fa fa-eye"></i> View
                                                    </button>
                                                @endif
                                            </div>
                                            <small class="form-text text-muted">Format: JPG, PNG (Max: 2MB)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card - Kepegawaian-->

                            <!--begin::Card - Rekening-->
                            <div class="card card-custom gutter-b">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="card-label">Data Rekening Bank</h3>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">Nama Bank</label>
                                        <div class="col-lg-9">
                                            <input type="text" name="nama_bank" class="form-control" placeholder="Masukkan nama bank" value="{{ old('nama_bank', $user_data->u_bank_name ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-lg-3 col-form-label">No Rekening</label>
                                        <div class="col-lg-9">
                                            <input type="text" name="no_rekening" class="form-control" placeholder="Masukkan nomor rekening" value="{{ old('no_rekening', $user_data->u_bank_account_number ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-3 col-form-label">Atas Nama</label>
                                        <div class="col-lg-9">
                                            <input type="text" name="atas_nama_rekening" class="form-control" placeholder="Masukkan nama pemilik rekening" value="{{ old('atas_nama_rekening', $user_data->u_bank_account_holder ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-lg-12 text-right">
                                            <button type="submit" class="btn btn-primary mr-2">Simpan</button>
                                            <button type="reset" class="btn btn-secondary">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card - Rekening-->
                        </div>
                    </div>
                </form>
            </div>
            <!--end::Container-->
        </div>
        <!--end::Entry-->
    </div>
    <!--end::Content-->

    <!-- Modals for Image Preview -->
    @if(isset($user_data->u_ktp_image))
    <div class="modal fade" id="modalFotoKTP" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto KTP</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ Storage::disk('s3')->url($user_data->u_ktp_image) }}" class="img-fluid" alt="Foto KTP">
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($user_data->u_npwp_image))
    <div class="modal fade" id="modalFotoNPWP" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto NPWP</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ Storage::disk('s3')->url($user_data->u_npwp_image) }}" class="img-fluid" alt="Foto NPWP">
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($user_data->u_photo))
    <div class="modal fade" id="modalFotoFormal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto Formal</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ Storage::disk('s3')->url($user_data->u_photo) }}" class="img-fluid" alt="Foto Formal">
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($user_data->u_bpjs_kes_image))
    <div class="modal fade" id="modalFotoBPJSKesehatan" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto BPJS Kesehatan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ Storage::disk('s3')->url($user_data->u_bpjs_kes_image) }}" class="img-fluid" alt="Foto BPJS">
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($user_data->u_bpjs_tk_image))
    <div class="modal fade" id="modalFotoBPJSKetenagakerjaan" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto BPJS Ketenagakerjaan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ Storage::disk('s3')->url($user_data->u_bpjs_tk_image) }}" class="img-fluid" alt="Foto BPJS">
                </div>
            </div>
        </div>
    </div>
    @endif

    @include('app._partials.js')
@endSection()
