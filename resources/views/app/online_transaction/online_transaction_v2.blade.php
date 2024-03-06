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
                            <div class="card-header flex-wrap py-3">
                                <div class="card-toolbar">
                                    <!--begin::Dropdown-->
                                    <div class="dropdown dropdown-inline mr-2">
                                        <button type="button"
                                                class="btn btn-light-primary font-weight-bolder dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="svg-icon svg-icon-md">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                             viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"/>
                                                <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                      fill="#000000" opacity="0.3"/>
                                                <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                      fill="#000000"/>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>Export
                                        </button>
                                        <!--begin::Dropdown Menu-->
                                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                            <!--begin::Navigation-->
                                            <ul class="navi flex-column navi-hover py-2">
                                                <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
                                                    Bentuk File :
                                                </li>
                                                <li class="navi-item">
                                                    <a href="#" class="navi-link">
                                                    <span class="navi-icon">
                                                        <i class="la la-copy"></i>
                                                    </span>
                                                        <span id="product_supplier_excel_btn"></span>
                                                    </a>
                                                </li>
                                            </ul>
                                            <!--end::Navigation-->
                                        </div>
                                        <!--end::Dropdown Menu-->
                                    </div>
                                    <!--end::Dropdown-->
                                    <!--begin::Dropdown-->
                                    <div class="dropdown dropdown-inline mr-2">
                                        <button type="button" class="btn btn-light-primary font-weight-bolder"
                                                data-toggle="modal" data-target="#ImportModal" aria-haspopup="true"
                                                aria-expanded="false">
                                    <span class="svg-icon svg-icon-md">
                                        <!--begin::Svg Icon | path:assets/media/svg/icons/Design/PenAndRuller.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                                             viewBox="0 0 24 24" version="1.1">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <rect x="0" y="0" width="24" height="24"/>
                                                <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z"
                                                      fill="#000000" opacity="0.3"/>
                                                <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z"
                                                      fill="#000000"/>
                                            </g>
                                        </svg>
                                        <!--end::Svg Icon-->
                                    </span>Import
                                        </button>
                                    </div>
                                    <!--end::Dropdown-->
                                    <!--begin::Button-->

                                </div>
                            </div>
                            <div class="card-body table-responsive" >
                                <div class="container">
                                    <div class="row">
                                        <div class="form-group" style="padding-top:22px;">
                                            <select class="form-control" id="filter_platform" name="filter_platform"  onchange="showDiv(this)"   required>
{{--                                            <select class="form-control" id="filter_platform" name="filter_platform" required>--}}
                                                <option value="">- Pilih Platform -</option>
                                                <option value="shopee">Shopee</option>
                                                <option value="tiktok">TikTok</option>
                                            </select>
                                            <div id="st_id_filter_parent"></div>
                                        </div>

                                        <div class="form-group ml-4" style="padding-top:22px;">
                                            <select class="form-control" id="filter_status" name="filter_platform" required>
                                                <option value="">- Pilih Status transaksi -</option>
                                                <option value="Shopee">All</option>
                                                <option value="TikTok">In-Progress</option>
                                                <option value="TikTok">Cancel</option>
                                                <option value="TikTok">Done</option>
                                            </select>
                                            <div id="st_id_filter_parent"></div>
                                        </div>

                                    </div>
                                </div>

                                <div id="shopee_div" style="display: none">
                                    <input type="search" class="form-control form-control col-6" id="purchase_order_search" placeholder="Cari Order Number / No Resi)"/><br/>
                                    <table class="table table-hover table-checkable" id="OnlineTransactionTb">
                                        <thead class="bg-light text-dark">
                                        <tr>
                                            <th class="text-dark">No</th>
                                            <th class="text-dark">Order Number</th>
                                            <th class="text-dark">Order Status</th>
                                            <th class="text-dark">Reason Cancellation</th>
                                            <th class="text-dark">Resi</th>
                                            <th class="text-dark">Shipping Method</th>
                                            <th class="text-dark">Ship Deadline</th>
                                            <th class="text-dark">Date Order</th>
                                            <th class="text-dark">Payment Method</th>
                                            <th class="text-dark">Payment Date</th>
                                            <th class="text-dark">SKU / Barcode</th>
                                            <th class="text-dark">Original Price</th>
                                            <th class="text-dark">Price After Discount</th>
                                            <th class="text-dark">QTY</th>
                                            <th class="text-dark">Return QTY</th>
                                            <th class="text-dark">Total Price</th>
                                            <th class="text-dark">Total Discount</th>
                                            <th class="text-dark">Shipping Fee</th>
                                            <th class="text-dark">Voucher By Seller</th>
                                            <th class="text-dark">Cashback Coin</th>
                                            <th class="text-dark">Voucher</th>
                                            <th class="text-dark">Voucher By Shopee</th>
                                            <th class="text-dark">Discount By Seller</th>
                                            <th class="text-dark">Discount By Shopee</th>
                                            <th class="text-dark">Coin Used</th>
                                            <th class="text-dark">Credit Card Discount</th>
                                            <th class="text-dark">Shipping Cost</th>
                                            <th class="text-dark">Total Payment</th>
                                            <th class="text-dark">Regency and City</th>
                                            <th class="text-dark">Province</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>

                                <div id="tiktok_div">
                                    <input type="search" class="form-control form-control col-6" id="purchase_order_search" placeholder="Cari Number Order / No Resi)"/><br/>
                                    <table class="table table-hover table-checkable" id="OnlineTransactionTikTokTb">
                                        <thead class="bg-light text-dark">
                                        <tr>
                                            <th class="text-dark">No</th>
                                            <th class="text-dark">Order Number</th>
                                            <th class="text-dark">Order Status</th>
{{--                                            <th class="text-dark">Order Sub Status</th>--}}
{{--                                            <th class="text-dark">Order Created</th>--}}
{{--                                            <th class="text-dark">Cancel Type</th>--}}
{{--                                            <th class="text-dark">Cancel By</th>--}}
{{--                                            <th class="text-dark">Reason Cancellation</th>--}}
{{--                                            <th class="text-dark">Seller Note</th>--}}
{{--                                            <th class="text-dark">Pre-Order</th>--}}
{{--                                            <th class="text-dark">Resi</th>--}}
{{--                                            <th class="text-dark">Shipping Method</th>--}}
{{--                                            <th class="text-dark">Payment Date</th>--}}
{{--                                            <th class="text-dark">Payment Method</th>--}}
{{--                                            <th class="text-dark">SKU</th>--}}
{{--                                            <th class="text-dark">Original Price</th>--}}
{{--                                            <th class="text-dark">Price After Discount</th>--}}
{{--                                            <th class="text-dark">Quantity</th>--}}
{{--                                            <th class="text-dark">Return Quantity</th>--}}
{{--                                            <th class="text-dark">Total Discount</th>--}}
{{--                                            <th class="text-dark">Shipping Fee</th>--}}
{{--                                            <th class="text-dark">Discount Seller</th>--}}
{{--                                            <th class="text-dark">Discount Platform</th>--}}
{{--                                            <th class="text-dark">Total Payment</th>--}}
{{--                                            <th class="text-dark">Regency and City</th>--}}
{{--                                            <th class="text-dark">Province</th>--}}
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
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
    @include('app.online_transaction.online_transaction_modal')
    @include('app._partials.js')
    @include('app.online_transaction.online_transaction_js')
@endSection()