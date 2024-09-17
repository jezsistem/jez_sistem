<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<?php echo $__env->make('app.offline_pos.offline_pos_head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!--end::Head-->
<!--begin::Body-->

<body id="tc_body" class="header-fixed header-mobile-fixed subheader-enabled aside-enabled aside-fixed"
      style="background-color:#e2e2e2;" onload="forceFullScreen()">
<!-- Paste this code after body tag -->
<div class="se-pre-con">
    <div class="pre-loader">
        <img class="img-fluid" src="<?php echo e(asset('pos')); ?>/jez.gif" alt="loading" width="20%">
    </div>
</div>
<?php echo $__env->make('app.offline_pos.offline_pos_header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="contentPOS">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-9 col-lg-8 col-md-8">
                <div class="">
                    <div class="card card-custom gutter-b bg-white border-0 table-contentpos"
                         style="border-radius:10px;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between colorfull-select">
                                <input type="hidden" id="_pt_id" value=""/>
                                <input type="hidden" id="_pt_id_complaint" value=""/>
                                <input type="hidden" id="_exchange" value=""/>
                                <input type="hidden" id="cross_order" value="0"/>
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
                                    <input type="hidden" id="cust_id" value="1"/>
                                    <input type="hidden" id="cust_id" value="<?php echo e(Auth::user()->st_id); ?>"/>
                                    <input type="search" id="cust_id_label"
                                           placeholder="Ketik minimal 4 huruf customer" autocomplete="off"/> <a
                                            href="#" class="btn btn-inventory" data-id=""
                                            id="check_customer">Check</a>
                                    <div id="itemListCust"></div>
                                    <br/>
                                    <select class="form-control border-dark col-12 mr-1 bg-info text-white"
                                            id="std_id">
                                        <option value="17" selected>OFFLINE</option>
                                        <option value="14">ONLINE / WHATSAPP</option>
                                        <option value="18">TIKTOK</option>
                                        <option value="19">Shopee</option>
                                    </select>
                                </div>
                                <input type="hidden" id="free_sock_customer_mode" value=""/>
                                <div class="btn btn-info d-none" id="waiting_customer_label">
                                    menunggu customer isi rating ..
                                    <a class="btn-sm btn-danger" id="cancel_rating_btn">X</a>
                                </div>
                                <div class="btn btn-info d-none" id="free_sock_customer_panel">
                                    <label class="text-white d-flex">[RATING CUSTOMER]</label>
                                    <input type="text" id="free_sock_customer_label" value=""/>
                                    <input type="hidden" id="free_sock_customer_id" value=""/>
                                    <input type="hidden" id="free_sock_customer_ur_id" value=""/>
                                </div>
                                <div class="selectmain">
                                    <label class="btn-sm btn-primary col-12 rounded text-white d-flex"
                                           id="reload_refund_list">Refund / Penukaran</label>
                                    <div id="refund_reload"></div>
                                    <a class="btn-sm btn-primary" id="product_barcode_btn"
                                       style="cursor:pointer;">Lengkapi Barcode</a>
                                </div>
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                            </div>
                        </div>
                    </div>
                    <div class="card card-custom gutter-b bg-white border-0 table-contentpos" id="posContent"
                         style="display:none;">
                        <div class="card-body bg-primary rounded">
                            <div class="form-group row mb-0">
                                <div class="col-md-12">
                                    <label class="text-white">LOKASI</label>
                                    <fieldset class="form-group mb-0 d-flex barcodeselection">
                                        <select class="form-control border-dark col-2 mr-2 bg-info text-white"
                                                id="item_type">
                                            <option value="waiting">WAITING</option>
                                            <option value="store" selected>STORE</option>
                                            <option value="b1g1">B1G1</option>
                                        </select>
                                        <input style="background:#fef6df; color:black;" type="text"
                                               class="form-control border-dark col-3 mr-1" id="product_name_input"
                                               placeholder="Ketik minimal 3 huruf pertama nama artikel"
                                               autocomplete="off">
                                        <input style="background:#D7F6E9; color:black;" type="text"
                                               class="form-control border-dark col-4 mr-1" id="barcode_input"
                                               placeholder="Klik untuk barcode mode" autocomplete="off" autofocus>
                                        <input type="text" class="form-control border-dark col-3"
                                               id="invoice_input" placeholder="Invoice (ketik 5 angka invoice)">
                                    </fieldset>
                                    <div id="itemList"></div>
                                    <input type="hidden" value="<?php echo e(Auth::user()->u_name); ?>" name="u_name"
                                           class="form-control border-dark col-3" id="invoice_input">
                                    <input type="hidden" value="<?php echo e($data['pst_custom']->id); ?>" name="u_name"
                                           class="form-control border-dark col-3" id="pst_custom">
                                    <input type="hidden" value="<?php echo e($data['psc_custom']->id); ?>" name="u_name"
                                           class="form-control border-dark col-3" id="psc_custom">
                                    <input type="hidden" value="<?php echo e($data['pl_custom']->id); ?>" name="u_name"
                                           class="form-control border-dark col-3" id="pl_custom">
                                    <input type="hidden" value="<?php echo e($data['store']->st_name); ?>" name="st_name"
                                           class="form-control border-dark col-3" id="invoice_input">


                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="table-datapos">
                            <div class="table-responsive" id="printableTable">
                                <input type="hidden" id="total_row" value="0"/>
                                <table id="orderTable" class="display table table-hover" style="width:100%">
                                    <thead class="bg-primary">
                                    <tr>
                                        <th class="text-white">Produk</th>
                                        <th class="text-white">Stok</th>
                                        <th class="text-white">Qty</th>
                                        <th class="text-white">Discount (%)</th>
                                        <th class="text-white">Discount (Rp)</th>
                                        <th class="text-white">Nameset</th>
                                        
                                        <th class="text-white">Harga</th>
                                        <th class="text-white">Subtotal</th>
                                        <th class="text-right no-sort"></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div id="voucher_informatio n" class='d-none'>
                                <input type="hidden" id="_voucher_value"/>
                                
                                
                                <input type="hidden" id="_voc_value"/>
                                <input type="hidden" id="_voc_disc_value"/>
                                <input type="hidden" id="_voc_total_disc_value">
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $__env->make('app.offline_pos.offline_pos_sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
</div>
<?php echo $__env->make('app.offline_pos.offline_pos_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('app.offline_pos.offline_pos_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</body>

</html><?php /**PATH C:\laragon\www\jez\jez_sistem\resources\views/app/offline_pos/offline_pos.blade.php ENDPATH**/ ?>