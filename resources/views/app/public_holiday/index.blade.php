@extends('app.structure')
@section('content')

<style>
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
#shiftCodeTable td {
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

                        <div class="card-header flex-wrap py-3">
                            <div class="card-toolbar d-flex justify-content-between w-100">
                                <div class="d-flex align-items-center">
                                    <input type="search" class="form-control mr-3" style="width: 300px;" id="public_holiday_search" placeholder="Search"/>

                                    <button type="button"
                                            class="btn btn-primary"
                                            id="btnSyncPublicHoliday">
                                        <i class="fa fa-sync"></i> Sync Hari Libur
                                    </button>
                                </div>
                            </div>

                        </div>
                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <table class="table table-hover table-checkable" id="phTable">
                                <thead class="bg-light text-dark">
                                <tr>
                                    <th class="text-dark">No</th>
                                    <th class="text-dark">Tanggal</th>
                                    <th class="text-dark">Description</th>
                                    <th class="text-dark">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- DataTables will populate this -->
                                </tbody>
                            </table>
                            <!--end: Datatable-->
                        </div>
                        <hr>


                    </div>
                    <!--end::Card-->
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-xxl-12">
                    <!--begin::Card-->
                    <div class="card card-custom gutter-b">

                        <div class="card-body table-responsive">
                            <!--begin: Datatable-->
                            <h5 class="mt-4 mb-3">📅 Kalender Hari Libur</h5>

                            <div id="publicHolidayCalendar"></div>
                            <!--end: Datatable-->
                        </div>
                        <hr>


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
@include('app.public_holiday.public_holiday_js') 