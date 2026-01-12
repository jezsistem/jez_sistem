@extends('app.structure')

@section('content')

    <style>
        /* Custom CSS for Metronic dropdown menu */
        /* Modal styles using vanilla CSS (like staff) */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 0;
            border: 1px solid #888;
            width: 90%;
            max-width: 500px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 15px;
        }

        .modal-footer {
            padding: 15px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
        }

        .close:hover {
            color: #000;
        }

        .modal-open {
            overflow: hidden;
        }

        /* Custom CSS for Metronic dropdown menu */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .menu.menu-sub-dropdown {
            z-index: 9999 !important;
            position: absolute !important;
            top: 100% !important;
            right: 0 !important;
            margin-top: 5px !important;
            min-width: 150px !important;
            background: white !important;
            border: 1px solid #e4e6ef !important;
            border-radius: 0.475rem !important;
            box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075) !important;
        }

        /* Ensure proper positioning for DataTables */
        .dataTables_wrapper .dataTables_processing {
            z-index: 9998;
        }

        /* Fix for menu positioning in table cells */
        #leaveRequestTable td {
            position: relative;
        }

        /* Menu item styling */
        .menu-item .menu-link {
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: #3f4254 !important;
            font-weight: 500;
            font-size: 1rem;
        }

        .menu-item .menu-link:hover {
            background-color: #f3f6f9 !important;
            color: #3699FF !important;
        }

        .menu-item .menu-link.text-danger {
            color: #f64e60 !important;
        }

        .menu-item .menu-link.text-danger:hover {
            background-color: #ffe2e5 !important;
            color: #f64e60 !important;
        }

        .menu-item .menu-link.text-success {
            color: #1bc5bd !important;
        }

        .menu-item .menu-link.text-success:hover {
            background-color: #e1f0ff !important;
            color: #1bc5bd !important;
        }

        .bg-all {
            background-color: #FFEBEB;
        }

        .bg-other {
            background-color: #F1F1F4;
        }

        /* Button styling for menu trigger */
        [data-kt-menu-trigger="click"] {
            cursor: pointer;
            user-select: none;
        }

        /* SVG icon styling */
        .svg-icon {
            display: inline-block;
            vertical-align: middle;
        }

        .svg-icon svg {
            width: 1em;
            height: 1em;
        }

        /* Fallback menu system styles */
        .menu.show {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .menu:not(.show) {
            display: none !important;
        }
    </style>


    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    {{-- ========================= SUMMARY CARDS ========================= --}}
                    {{-- ========================= SUMMARY CARDS ========================= --}}
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-4">
                            <div class="card card-custom rounded-lg bg-light">
                                <div class="card-body d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                    <span class="symbol-label">
                        <i class="fa fa-users"></i>
                    </span>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold font-size-h5">{{ $summary['total_customer'] ?? 0 }}</div>
                                        <div class="text-muted">Total Customer</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4">
                            <div class="card card-custom rounded-lg bg-success text-white">
                                <div class="card-body d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                    <span class="symbol-label bg-white text-success">
                        <i class="fa fa-coins"></i>
                    </span>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold font-size-h5">{{ $summary['active_customer'] ?? 0 }}</div>
                                        <div class="text-white-50">Activated Customer</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4">
                            <div class="card card-custom rounded-lg bg-warning">
                                <div class="card-body d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                    <span class="symbol-label bg-white">
                        <i class="fa fa-user-clock"></i>
                    </span>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold font-size-h5">{{ $summary['inactive_customer'] ?? 0 }}</div>
                                        <div class="text-muted">Not Activated</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-4">
                            <div class="card card-custom rounded-lg bg-info text-white">
                                <div class="card-body d-flex align-items-center">
                                    <div class="symbol symbol-40 mr-4">
                    <span class="symbol-label bg-white text-info">
                        <i class="fa fa-star"></i>
                    </span>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold font-size-h5">{{ $summary['elite'] ?? 0 }}</div>
                                        <div class="text-white-50">Elite Customer</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ========================= FILTER FORM ========================= --}}
                    <div class="card mb-5">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fa fa-filter"></i> Filter Customer</h5>
                        </div>
                        <div class="card-body">
                            <form id="filterForm" class="row">
                                <div class="col-md-3 mb-3">
                                    <label>Tier</label>
                                    <select class="form-control" name="cust_tier">
                                        <option value="">All</option>
                                        <option value="academy">Academy</option>
                                        <option value="pro">Pro</option>
                                        <option value="elite">Elite</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label>Activation</label>
                                    <select class="form-control" name="cust_coin_active">
                                        <option value="">All</option>
                                        <option value="1">Active</option>
                                        <option value="0">Not Active</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label>Cari Customer</label>
                                    <input type="text" class="form-control" name="phone" placeholder="enter keyword customer ....">
                                </div>

                                <div class="col-md-3 mb-3 align-self-end">
                                    <button class="btn btn-primary w-100">
                                        <i class="fa fa-search"></i> Apply
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Data Customer</h4>
                            <div>
                                <button type="button" class="btn btn-light-green font-weight-bolder mr-2"
                                        onclick="exportRequestToExcel()">
                                        <span class="svg-icon svg-icon-md">
                                            <i class="ki-outline ki-file-down"></i>
                                        </span>Export Excel
                                </button>
                                <a href="{{ url('overtime/create') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> New Request
                                </a>
                            </div>
                        </div>

                        <div class="card-body table-responsive">
                            <table id="customerTable" class="table table-hover w-100">
                                <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Tier</th>
                                    <th>Coin</th>
                                    <th>Trx Value</th>
                                    <th>Member Card</th>
                                    <th>Activation</th>
                                    <th>Created At</th>
                                    <th width="120">Action</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    @include('app._partials.js')
    @include('app.customer_v2.customer_v2_js')
@endsection
