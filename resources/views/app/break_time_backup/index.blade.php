@extends('app.structure')
@section('content')
<style>
    .table-responsive {
        overflow-x: auto;
    }
    
    .btn-group-vertical .btn {
        margin-bottom: 2px;
        width: 30px;
        height: 30px;
        padding: 5px;
        font-size: 12px;
    }
    
    .btn-group-vertical .btn:last-child {
        margin-bottom: 0;
    }
    
    .badge {
        font-size: 11px;
        padding: 4px 8px;
    }
    
    .dataTables_wrapper .dt-buttons {
        margin-bottom: 10px;
    }
    
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 10px;
    }
    
    .table th {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
    }
    
    .table td {
        font-size: 12px;
        vertical-align: middle;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.02);
    }
    .btn-red {
        background-color: #cd8804;
        color: #ffffff;
        border-color: #cd8804;
    }
    .btn-red i{
        color: #ffffff;
    }

    .btn-red:hover {
        background-color:rgb(210, 140, 11);
        color: #ffffff;
        border-color: #cd8804;
    }
    .btn-red:focus, .btn-red.focus {}
</style>
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
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <a href="{{ route('break-times-backup.report') }}" class="btn btn-light-primary font-weight-bolder mr-2">
                    <i class="ki-outline ki-graph-2"></i>
                    View Report
                </a>
                <a href="{{ route('break-times-backup.summary-report') }}" class="btn btn-dark font-weight-bolder">
                    <i class="ki-outline ki-chart-line"></i>
                    Summary Report
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
            <div class="row">
                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">
                        <!-- <div class="card-header flex-wrap py-3">
                            <div class="card-title">
                                <h3 class="card-label">Data Break Time</h3>
                            </div>
                </div> -->
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Break Control Panel -->
                    <div class="row mb-6">
                        <div class="col-md-6">
                            <!-- Break Button -->
                            <div class="card card-custom bg-warning rounded-xl">
                                <div class="card-body text-center p-8">
                                    <div class="mb-4">
                                        <i class="ki-outline ki-coffee text-white" style="font-size: 3rem;"></i>
                                    </div>
                                    <button type="button" class="btn btn-white btn-lg btn-block mb-3 rounded-lg" id="mainBreakButton" style="min-height: 60px; font-size: 1.2rem;">
                                         <span id="breakButtonText">Start Break</span>
                                    </button>
                                    <div id="breakTimer" class="text-white h4 mb-0" style="display: none;">
                                        <i class="fas fa-clock text-white mr-2"></i> <span id="timerDisplay">00:00</span>
                                        <div class="text-white-50 small mt-1">
                                            <span id="breakTypeDisplay">Break Time</span> - <span id="durationDisplay">Remaining</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Break Allowance Info -->
                            <!-- <div class="alert alert-secondary mt-3 d-flex align-items-center" id="breakAllowanceInfo">
                                <i class="ki-outline ki-information-4 mr-2"></i>
                                <strong>Break Allowance:</strong> <span id="allowanceText">Loading...</span>
                            </div> -->
                        </div>
                        
                        <div class="col-md-6">
                            <!-- Currently on Break List -->
                            <div class="card card-custom">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="card-label">
                                            Currently on Break
                                        </h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <select class="form-control" id="divisionFilter" style="width: 150px;">
                                            <option value="">All Divisions</option>
                                            @foreach(\App\Models\UserDivision::where('ud_status', 'active')->get() as $division)
                                                <option value="{{ $division->id }}">{{ $division->ud_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                                    <div id="currentBreakList">
                                        <div class="text-center p-4">
                                            <i class="fas fa-spinner fa-spin"></i> Loading...
                                        </div>
                                    </div>
                                </div>
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
@endsection

@include('app._partials.js')
@include('app.break_time_backup.break_time_js') 