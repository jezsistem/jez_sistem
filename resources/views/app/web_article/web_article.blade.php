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
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <div class="row d-flex justify-content-between">
                            <select class="form-control col-5" id="pc_id" name="pc_id" required>
                                <option value="">- Kategori -</option>
                                @foreach ($data['pc_id'] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <select class="form-control col-5" id="img_filter" name="img_filter" required>
                                <option value="">- Status Foto -</option>
                                <option value="1">Sudah Upload</option>
                                <option value="0">Belum Upload</option>
                            </select>
                            </div>
                            <br/>
                            <input type="search" class="form-control  col-6" id="article_search" placeholder="Cari brand artikel warna"/><br/>
                            <table class="table table-hover table-checkable" id="Articletb">
                                <thead class="bg-light text-dark">
                                    <tr>
                                        <th class="text-dark">No</th>
                                        <th class="text-dark">Brand</th>
                                        <th class="text-dark">Artikel</th>
                                        <th class="text-dark">Warna</th>
                                        <th class="text-dark">Berat(gr)</th>
                                        <th class="text-dark">Gambar</th>
                                        <th class="text-dark">SizeChart</th>
                                        <th class="text-dark">slug</th>
                                        <th class="text-dark">Deskripsi</th>
                                        <th class="text-dark">Stok</th>
                                        <th class="text-dark"></th>
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
@include('app.web_article.web_article_modal')
@include('app._partials.js')
@include('app.web_article.web_article_js')
@endSection()