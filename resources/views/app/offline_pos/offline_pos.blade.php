<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
@include('app.offline_pos.offline_pos_head')
<!--end::Head-->
<!--begin::Body-->

<body id="tc_body" class="header-fixed header-mobile-fixed subheader-enabled aside-enabled aside-fixed"
    style="background-color:#e2e2e2;" onload="forceFullScreen()">

    <!-- Paste this code after body tag -->
    <div class="se-pre-con">
        <div class="pre-loader">
            <img class="img-fluid" src="{{ asset('pos') }}/jez.gif" alt="loading" width="20%">
        </div>
    </div>
    @include('app.offline_pos.offline_pos_header')
    <div class="contentPOS">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-9 col-lg-8 col-md-8">
                    <div class="">
                        <div class="card card-custom gutter-b bg-white border-0 table-contentpos"
                            style="border-radius:10px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between colorfull-select">
                                    <input type="hidden" id="_pt_id" value="" />
                                    <input type="hidden" id="_pt_id_complaint" value="" />
                                    <input type="hidden" id="_exchange" value="" />
                                    <input type="hidden" id="cross_order" value="0" />
                                    <div class="selectmain bg-primary" style="padding:5px; border-radius:10px;">
                                        <label class="text-white d-flex font-weight-bold">Customer
                                            <span class="badge badge-success white rounded-circle" id="add_customer_btn"
                                                data-toggle="modal" data-target="#shiftCustomerModal">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="svg-sm"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                    id="Layer_122" x="0px" y="0px" width="512px" height="512px"
                                                    viewBox="0 0 512 512" enable-background="new 0 0 512 512"
                                                    xml:space="preserve">
                                                    <g>
                                                        <rect x="234.362" y="128" width="43.263" height="256"></rect>
                                                        <rect x="128" y="234.375" width="256" height="43.25"></rect>
                                                    </g>
                                                </svg>
                                            </span>
                                        </label>
                                        <input type="hidden" id="cust_id" value="1" />
                                        <input type="hidden" id="cust_id" value="{{ Auth::user()->st_id }}" />
                                        <input type="search" id="cust_id_label"
                                            placeholder="Ketik minimal 4 huruf customer" autocomplete="off" />
                                        <a href="#" class="btn-inventory" data-id="" id="check_customer"
                                            style="background:#FFCB8E; color:black; font-size: 11px; text-decoration: none; padding: 10px 20px; border: none; border-radius: 4px; display: inline-block; font-weight:bold"
                                            onmouseover="this.style.background='#e78282'; this.style.color='white'; this.style.fontWeight='bold';"
                                            onmouseout="this.style.background='#FFCB8E'; this.style.color='black'; this.style.fontWeight='normal';">
                                            Check
                                        </a>
                                        <div id="itemListCust"></div>

                                        <br />
                                        <select class="form-control border-dark col-12 mr-1 bg-info text-white"
                                            id="std_id">
                                            <option value="17" selected>OFFLINE</option>
                                            <option value="14">ONLINE / WHATSAPP</option>
                                            <option value="18">TIKTOK</option>
                                            <option value="19">Shopee</option>
                                        </select>
                                    </div>
                                    <input type="hidden" id="free_sock_customer_mode" value="" />
                                    <div class="btn btn-info d-none" id="waiting_customer_label">
                                        menunggu customer isi rating ..
                                        <a class="btn-sm btn-danger" id="cancel_rating_btn">X</a>
                                    </div>
                                    <div class="btn btn-info d-none" id="free_sock_customer_panel">
                                        <label class="text-white d-flex">[RATING CUSTOMER]</label>
                                        <input type="text" id="free_sock_customer_label" value="" />
                                        <input type="hidden" id="free_sock_customer_id" value="" />
                                        <input type="hidden" id="free_sock_customer_ur_id" value="" />
                                    </div>
                                    <div class="selectmain">
                                        <label class="btn-sm btn-primary col-12 rounded text-white d-flex"
                                            id="reload_refund_list">Refund / Penukaran</label>
                                        <div id="refund_reload"></div>
                                        <a class="btn-sm btn-primary" id="product_barcode_btn"
                                            style="cursor:pointer;">Lengkapi Barcode</a>
                                    </div>
                                    {{--                                <div class="selectmain"> --}}
                                    {{--                                    <label --}}
                                    {{--                                            class="btn btn-inventory col-12 rounded text-white d-flex font-weight-bold">Store</label> --}}
                                    {{--                                    <select class="arabic-select-store select-down " id="st_id" --}}
                                    {{--                                            name="st_id"> --}}
                                    {{--                                        <option value="">- Store -</option> --}}
                                    {{--                                        @foreach ($data['st_id'] as $key => $value) --}}
                                    {{--                                            <option value="{{ $key }}">{{ $value }}</option> --}}
                                    {{--                                        @endforeach --}}
                                    {{--                                    </select> --}}
                                    {{--                                </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="card card-custom gutter-b bg-white border-0 table-contentpos" id="posContent"
                            style="display:none;">
                            <div class="card-body bg-primary rounded">
                                <div class="form-group row mb-0">
                                    <div class="col-md-12">
                                        <label class="text-white"
                                            style="font-weight: bold; font-size: 15px;">LOKASI</label>
                                        <fieldset class="form-group mb-0 d-flex barcodeselection">
                                            <select class="form-control border-dark col-2 mr-2 bg-info text-white"
                                                id="item_type">
                                                <option value="waiting">WAITING</option>
                                                <option value="store" selected>STORE</option>
                                                <option value="b1g1">B1G1</option>
                                            </select>
                                            <input style="background:#FFCB8E; color:black; font-size: 11px"
                                                type="text" class="form-control border-dark col-3 mr-1"
                                                id="product_name_input"
                                                placeholder="Ketik minimal 3 huruf pertama nama artikel"
                                                autocomplete="off">
                                            <input style="background:#DAD4B5; color:black; font-size: 11px;"
                                                type="text" class="form-control border-dark col-4 mr-1"
                                                id="barcode_input" placeholder="Klik untuk Barcode Mode"
                                                autocomplete="off" autofocus>
                                            <input type="text" class="form-control border-dark col-3"
                                                style="font-size: 11px; background:#ffffff; color:black;
                                               id="invoice_input"
                                                placeholder="Invoice (ketik 5 angka invoice)">
                                        </fieldset>
                                        <div id="itemList"></div>
                                        <input type="hidden" value="{{ Auth::user()->u_name }}" name="u_name"
                                            class="form-control border-dark col-3" id="invoice_input">
                                        <input type="hidden" value="{{ $data['pst_custom']->id }}" name="u_name"
                                            class="form-control border-dark col-3" id="pst_custom">
                                        <input type="hidden" value="{{ $data['psc_custom']->id }}" name="u_name"
                                            class="form-control border-dark col-3" id="psc_custom">
                                        <input type="hidden" value="{{ $data['pl_custom']->id }}" name="u_name"
                                            class="form-control border-dark col-3" id="pl_custom">
                                        <input type="hidden" value="{{ $data['store']->st_name }}" name="st_name"
                                            class="form-control border-dark col-3" id="invoice_input">
                                        {{--                                    <input type="hidden" value="{{ $data['starting_date'] }}" name="st_name" --}}
                                        {{--                                           class="form-control border-dark col-3" id="invoice_input"> --}}
                                    </div>
                                </div>
                            </div>
                            <br />

                            <div class="table-datapos">


                                <div class="table-container">

                                    <input type="hidden" id="total_row" value="0" />
                                    <table id="orderTable" class="display table table-hover"
                                        style="width: 80%; margin: 0 auto; text-align: center; font-size: 10px;">
                                        <thead class="table-header">
                                            <tr>
                                                <th>Produk</th>
                                                <th>Stok</th>
                                                <th>Qty</th>
                                                <th>Discount (%)</th>
                                                <th>Discount (Rp)</th>
                                                <th>Nameset</th>
                                                <th>Harga</th>
                                                <th>Subtotal</th>
                                                <th></th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Table rows go here -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="voucher_informatio n" class='d-none'>
                                    <input type="hidden" id="_voucher_value" />
                                    {{--								<table id="orderTable" class="display table table-hover" style="width:100%"> --}}
                                    {{--								<input type="hidden" id="_voc_pst_id"/> --}}
                                    <input type="hidden" id="_voc_value" />
                                    <input type="hidden" id="_voc_disc_value" />
                                    <input type="hidden" id="_voc_total_disc_value">
                                    {{--									<input type="hidden" id="_voc_id"/> --}}
                                    {{--									<thead class="bg-primary"> --}}
                                    {{--									<tr class="d-flex align-items-center justify-content-between pl-3 pr-3" style="background:#fef6df;"> --}}
                                    {{--										<th class="col-6"> --}}
                                    {{--												Produk --}}
                                    {{--										</th> --}}
                                    {{--										<th class="col-6"> --}}
                                    {{--												<span id="_voc_article"></span> --}}
                                    {{--										</th> --}}
                                    {{--									</tr> --}}
                                    {{--									<tr class="d-flex align-items-center justify-content-between pl-3 pr-3" style="background:#fef6df;"> --}}
                                    {{--										<th class="col-6"> --}}
                                    {{--												Bandrol --}}
                                    {{--										</th> --}}
                                    {{--										<th class="col-6"> --}}
                                    {{--											<span id="_voc_bandrol"></span> --}}
                                    {{--										</th> --}}
                                    {{--									</tr> --}}
                                    {{--									<tr class="d-flex align-items-center justify-content-between pl-3 pr-3" style="background:#fef6df;"> --}}
                                    {{--										<th class="col-6"> --}}
                                    {{--												Disc --}}
                                    {{--										</th> --}}
                                    {{--										<th class="col-6"> --}}
                                    {{--											<span id="_voc_disc"></span> <span id="_voc_disc_type"></span> <span id="_voc_disc_value"></span> --}}
                                    {{--										</th> --}}
                                    {{--									</tr> --}}
                                    {{--									<tr class="d-flex align-items-center justify-content-between pl-3 pr-3" style="background:#fef6df;"> --}}
                                    {{--										<th class="col-6"> --}}
                                    {{--												Harga Baru --}}
                                    {{--										</th> --}}
                                    {{--										<th class="col-6"> --}}
                                    {{--											<span id="_voc_value_show"></span> --}}
                                    {{--										</th> --}}
                                    {{--									</tr> --}}
                                    {{--									</thead> --}}
                                    {{--								</table> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('app.offline_pos.offline_pos_sidebar')
            </div>
        </div>
    </div>
    @include('app.offline_pos.offline_pos_modal')
    @include('app.offline_pos.offline_pos_js')
    {{--	asdasd --}}
</body>



<style>
    .btn-inventory:hover {
        background: #ce5a5a;
        color: white;
    }

    .table-container {
        margin: 10px auto;
        max-width: 1000px;
        /* padding: 12px; */
        background-color: #ffffff;
        overflow-x: auto; /* Enables horizontal scrolling */
        overflow-y: auto; /* Enables vertical scrolling */
    }

    .table {
        width: 80%; /* Ensures the table takes up the full width of the container */
        border-collapse: collapse; /* Ensures borders are collapsed */
    }

    .table-header {
        background-color: #C02727;
    }

    .table-header th {
        color: #ffffff;
        padding: 12px;
        font-size: 12px;
        text-align: center;
    }

    .table td,
    .table th {
        border: 1px solid #dddddd;
        padding: 10px;
        text-align: center;
    }

    .table tbody tr:hover {
        background-color: #edb6ae;
        color: #ffffff;
    }

    .table td {
        vertical-align: middle;
    }

    /* Media queries for responsiveness */
    @media (max-width: 768px) {
        .table-container {
            padding: 8px;
            max-width: 80%;
        }

        .table-header th {
            font-size: 12px;
            padding: 8px;
        }

        .table td,
        .table th {
            padding: 8px;
        }
    }

    @media (max-width: 480px) {
        .table-container {
            padding: 6px;
        }

        .table-header th {
            font-size: 10px;
            padding: 6px;
        }

        .table td,
        .table th {
            padding: 6px;
        }

        .table tbody tr:hover {
            background-color: #f5b0a6;
        }
    }


</style>


</html>
