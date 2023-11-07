@extends('app.structure')
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-1">
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <h3 class="card-label">{{ $data['subtitle'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-xxl-12">
                    <div class="card card-custom gutter-b">
                        <div class="card-header flex-wrap py-3 mt-4">
                            <form class="row col-sm-12 col-12 col-md-12 col-lg-12 col-xl-12" id="f_filter">
                            <div class="form-group col-sm-12 col-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="exampleTextarea">Store</label>
                                <select class="form-control bg-light-success" id="st_id" name="st_id" required>
                                    <option value="">-</option>
                                    @foreach ($data['st_id'] as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-sm-12 col-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="exampleTextarea">Tipe Data</label>
                                <select class="form-control bg-light-success" id="data_filter" name="data_filter" required>
                                    <option value="">-</option>
                                    <option value="brand">Brand</option>
                                    <option value="article">Artikel</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-12 col-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="exampleTextarea">Data Artikel</label>
                                <select class="form-control bg-light-success" id="article_filter" name="article_filter">
                                    <option value="">-</option> 
                                    <option value="color">Warna</option>
                                    <option value="size">Size</option>
                                </select>
                            </div>
                            <div class="form-group col-sm-12 col-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="exampleTextarea">Range Tanggal</label><br/>
                                <a class="btn btn-sm btn-light font-weight-bold col-12 bg-light-success" id="kt_dashboard_daterangepicker" data-toggle="tooltip" title="Select dashboard daterange" data-placement="left" style="cursor:pointer;">
                                    <span class="text-dark font-size-base font-weight-bold mr-2" id="kt_dashboard_daterangepicker_title">Today</span>
                                    <span class="text-dark font-size-base font-weight-bolder" id="kt_dashboard_daterangepicker_date"></span>
                                </a>
                            </div>
                            <div class="col-sm-12 col-12 col-md-12 col-lg-6 col-xl-6">
                                <button type="button" class="btn btn-success font-weight-bold" id="export_btn">Export</button>
                                <button type="submit" class="btn btn-primary font-weight-bold">Tampilkan</button>
                            </div>
                            </form>
                        </div>
                        <div class="card-body table-responsive">
                            <div id="table"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('app._partials.js')
@include('app.asset_detail.asset_detail_js')
@endSection()
