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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Break Time Report</h5>
                <!--end::Page Title-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <a href="{{ route('break-times.index') }}" class="btn btn-light-primary font-weight-bolder">
                    <i class="fas fa-arrow-left"></i>
                    Back to Break Time
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
                        <div class="card-header flex-wrap py-3">
                            <div class="card-title">
                                <h3 class="card-label">Break Time Report</h3>
                            </div>
                        </div>
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

                            <!-- Statistics Cards -->
                            <div class="row mb-4">
                                @foreach($stats ?? [] as $stat)
                                    <div class="col-md-2">
                                        <div class="card bg-light">
                                            <div class="card-body text-center">
                                                <h4 class="text-primary">{{ $stat->total }}</h4>
                                                <small class="text-muted">
                                                    @switch($stat->bt_status)
                                                        @case('active')
                                                            Active
                                                            @break
                                                        @case('completed')
                                                            Completed
                                                            @break
                                                        @case('cancelled')
                                                            Cancelled
                                                            @break
                                                        @default
                                                            {{ ucfirst($stat->bt_status) }}
                                                    @endswitch
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Filter Form -->
                            <form method="GET" action="{{ route('break-times.report') }}" class="mb-3">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="start_date">Tanggal Mulai</label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                                           value="{{ request('start_date', date('Y-m-d')) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="end_date">Tanggal Akhir</label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                                           value="{{ request('end_date', date('Y-m-d')) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="user_id">Karyawan</label>
                                            <select class="form-control" id="user_id" name="user_id">
                                                <option value="">Semua Karyawan</option>
                                                @foreach($users as $user)
                                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                        {{ $user->u_name }} ({{ $user->u_nip }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="division_id">Divisi</label>
                                            <select class="form-control" id="division_id" name="division_id">
                                                <option value="">Semua Divisi</option>
                                                @foreach($divisions as $division)
                                                            <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                                        {{ $division->ud_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="">Semua Status</option>
                                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="break_time_search">Search</label>
                                            <input type="text" class="form-control" id="break_time_search" 
                                                   placeholder="Search by name or NIP">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="button" class="btn btn-primary btn-block" onclick="applyFilters()">
                                                <i class="fas fa-search"></i> Apply Filters
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover table-checkable" id="breakTimeTable">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Nama Karyawan</th>
                                            <th>NIP</th>
                                            <th>Divisi</th>
                                                    <th>Tipe Break</th>
                                            <th>Jam Mulai Istirahat</th>
                                            <th>Jam Selesai Istirahat</th>
                                                    <th>Durasi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this -->
                                    </tbody>
                                </table>
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
@include('app.break_time.report_js')
