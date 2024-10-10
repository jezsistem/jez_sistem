@extends('app.structure')
@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                <div class="d-flex align-items-center flex-wrap mr-1">
                    <div class="d-flex align-items-baseline flex-wrap mr-5">
                        <h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5 fs-3">{{ $data['subtitle'] }}</h5>
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
                                <iframe title="DAILY REPORT" width="1340" height="720.25"
                                        src="https://app.powerbi.com/reportEmbed?reportId=3ba8e8c9-d53b-48ba-b263-b2a6c10a7c53&autoAuth=true&ctid=360615dc-a711-43f5-a2bf-9e1f5c5dd846"
                                        frameborder="0" allowFullScreen="true"></iframe>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('app._partials.js')
    {{--    @include('app.asset_detail.asset_detail_js')--}}
@endSection()
